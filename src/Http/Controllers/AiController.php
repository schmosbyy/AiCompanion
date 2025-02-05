<?php

namespace Schmosbyy\AiCompanion\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AiController extends Controller
{
    public function ask(Request $request)
    {
        $prompt = $request->input('user_input');
        //command to run on a custom portOLLAMA_HOST=127.0.0.1:11435 ollama serve
        $response = Http::post('http://127.0.0.1:11435/api/generate', [
            'model' => 'deepseek-r1:14b',
            'prompt' => $prompt,
            'stream' => false
        ]);
        $decodedResponse = json_decode($response, true);
        $generatedSQL = $decodedResponse['response'];
        return "You entered: " . $generatedSQL;
    }
}
