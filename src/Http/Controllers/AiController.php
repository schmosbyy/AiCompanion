<?php
namespace Schmosbyy\AiCompanion\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class AiController extends Controller
{
    public function ask(Request $request)
    {
        $prompt = $request->input('user_input');

        $this->setDatabaseConnection();
        $schemaContext = $this->getSchemaContext();
        $response = $this->getSQLQueryFromLLM($prompt, $schemaContext);
        $generatedSQLQuery = $this->getSQLResponse($response);
        $sqlOutput = $this->executeSQLQuery($generatedSQLQuery);
        $formattedOutput = $this->formatWithLLM($sqlOutput, $prompt);

        //dump($prompt,$schemaContext,$sqlOutput, $formattedOutput);

        $formattedOutput = $formattedOutput ?: ["No SQL Data returned!"];

        return view('ai-companion::home', [
            "user_prompt"=>$prompt,
            "response" => "The generated SQL is: " . $generatedSQLQuery,
            "queryResult" => $formattedOutput
        ]);
    }

    private function formatWithLLM(array $data, string $userPrompt)
    {
        $jsonData = json_encode($data);
        $formatPrompt = $this->buildFormatPrompt($jsonData, $userPrompt);

        return $this->cleanUpLLMResponse($this->getResponseFromLLM($formatPrompt));
    }

    private function buildFormatPrompt(string $jsonData, string $userPrompt): string
    {
        return "Format this data as an HTML table. Make do with all the columns provided.
                Here is the data: $jsonData";
    }

    public function executeSQLQuery(string $query)
    {
        try {
            if (stripos($query, 'SELECT') !== 0) {
                return "Only SELECT queries are allowed.";
            }

            return DB::connection('vendor_project')->select($query); // Execute query
        } catch (\Exception $e) {
            return "Error executing query: " . $e->getMessage();
        }
    }

    public function getSQLResponse($decodedResponse)
    {
        $fullResponse = $decodedResponse['response'] ?? '';
        $cleanResponse = preg_replace('/<think>.*?<\/think>/s', '', $fullResponse); // Remove think content
        preg_match('/```sql\s*(.*?)\s*```/s', $cleanResponse, $sqlMatches); // Extract SQL query
        return trim($sqlMatches[1] ?? '');
    }

    public function cleanUpLLMResponse($decodedResponse)
    {
        $fullResponse = $decodedResponse['response'] ?? '';
        $cleanResponse = preg_replace('/<think>.*?<\/think>/s', '', $fullResponse); // Remove <think> section
        preg_match('/```(.*?)```/s', $cleanResponse, $matches); // Extract content inside backticks
        return trim($matches[1] ?? $cleanResponse);
    }

    public function setDatabaseConnection()
    {
        // Retrieve environment variables for database connection
        $driver = env('DB_CONNECTION', 'sqlite'); // Default to 'sqlite' if not set
        $dbHost = env('DB_HOST', '127.0.0.1');
        $dbPort = env('DB_PORT', $driver === 'pgsql' ? 5432 : ($driver === 'mysql' ? 3306 : 1433));
        $dbName = env('DB_DATABASE', 'database.sqlite');
        $dbUsername = env('DB_USERNAME', '');
        $dbPassword = env('DB_PASSWORD', '');
        $dbCharset = env('DB_CHARSET', 'utf8mb4');
        $dbCollation = env('DB_COLLATION', 'utf8mb4_unicode_ci');
        $prefix = env('DB_PREFIX', '');
        $foreignKeyConstraints = env('DB_FOREIGN_KEYS', true);

        // Configure the database connection based on the driver
        $config = [
            'driver' => $driver,
            'host' => $dbHost,
            'port' => $dbPort,
            'database' => $dbName,
            'username' => $dbUsername,
            'password' => $dbPassword,
            'charset' => $dbCharset,
            'collation' => $dbCollation,
            'prefix' => $prefix,
            'foreign_key_constraints' => $foreignKeyConstraints,
        ];

        // Additional configuration for specific drivers
        switch ($driver) {
            case 'mysql':
                $config['unix_socket'] = env('DB_SOCKET', '');
                $config['mysql_attr_timeout'] = 30;
                break;
            case 'pgsql':
                $config['schema'] = env('DB_SCHEMA', 'public');
                break;
            case 'sqlsrv':
                $config['options'] = [
                    \PDO::SQLSRV_ATTR_ENCODING => \PDO::SQLSRV_ENCODING_UTF8,
                ];
                break;
            case 'sqlite':
                // Ensure the SQLite database path is set correctly
                $config['database'] = database_path($dbName);
                break;
        }

        // Set the database connection configuration
        Config::set('database.connections.vendor_project', $config);
    }

    public function getResponseFromLLM(mixed $prompt)
    {
        ini_set('max_execution_time', 300); // Handle timeout issue
        //command to run on a custom portOLLAMA_HOST=127.0.0.1:11435 ollama serve
        $response = Http::post('http://127.0.0.1:11435/api/generate', [
            'model' => 'qwen2.5-coder:14b',
            'prompt' => $prompt,
            'stream' => false
        ]);
        return json_decode($response->body(), true);
    }

    public function getSQLQueryFromLLM(mixed $prompt, array $schemaContext)
    {
        $prompt = "You are an expert SQL assistant. Your task is to generate the most efficient SQL query based on the given user request and schema.
                   User Request: " . $prompt .
            " Schema Context: " . json_encode($schemaContext) .
            " Rules to follow:
                   • If all required columns exist in one table, use that table alone.
                   • If a column is missing from the primary table, check the foreign key relationships to determine the appropriate join.
                   • Only join tables when necessary, and use the shortest valid join path based on foreign key relationships.
                   • Ensure the correct column names and table names are used.
                   • Preserve case sensitivity of column and table names.
                   • Only return the SQL query without additional explanations or formatting.";

        return $this->getResponseFromLLM($prompt);
    }

    public function getSchemaContext(): array
    {
        $excludedTables = ['migrations', 'password_reset_tokens', 'sessions', 'cache', 'cache_locks', 'jobs', 'job_batches', 'failed_jobs'];
        $excludedColumns = ['created_at', 'updated_at', 'deleted_at', 'remember_token', 'password'];

        $tables = DB::connection('vendor_project')->select("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%'");
        $schemaContext = [];

        foreach ($tables as $table) {
            $tableName = $table->name;

            if (in_array($tableName, $excludedTables)) {
                continue;
            }

            $columns = DB::connection('vendor_project')->select("PRAGMA table_info({$tableName})");
            $filteredColumns = array_filter($columns, fn($column) => !in_array($column->name, $excludedColumns));
            $columnNames = array_map(fn($column) => $column->name, $filteredColumns);

            $foreignKeys = DB::connection('vendor_project')->select("PRAGMA foreign_key_list({$tableName})");
            $foreignKeyRelations = array_map(fn($fk) => [
                'column' => $fk->from,
                'references' => [
                    'table' => $fk->table,
                    'column' => $fk->to
                ]
            ], $foreignKeys);

            $schemaContext[$tableName] = [
                'columns' => $columnNames,
                'foreign_keys' => $foreignKeyRelations
            ];
        }

        return $schemaContext;
    }
}
