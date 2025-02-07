<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Search Interface</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }

        h1 {
            color: #333;
            margin-bottom: 20px;
        }

        .form-container {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            width: 100%;
            max-width: 500px;
            margin-bottom: 20px;
        }

        .form-container label {
            font-size: 16px;
            font-weight: bold;
            color: #555;
        }

        .form-container input {
            width: 100%;
            padding: 10px;
            margin-top: 8px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 16px;
            background-color: #f9f9f9;
        }

        .form-container button {
            width: 100%;
            padding: 12px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }

        .form-container button:hover {
            background-color: #45a049;
        }

        .response-container {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            width: 100%;
            max-width: 500px;
        }

        .response-container ul {
            list-style-type: none;
            padding: 0;
        }

        .response-container li {
            padding: 8px 0;
            color: #333;
        }

        .query-result {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
        }
    </style>

</head>
<body>

<h1>AI Companion</h1>

<div class="form-container">
    @csrf
    <form method="POST" action="{{ route('handle.input') }}">
        <label for="user_input">Enter your query:</label>
        <input type="text" name="user_input" id="user_input" placeholder="Ask your question here...">
        <button type="submit">Submit</button>
    </form>
</div>

<div class="response-container">
    @if(isset($user_prompt) && !empty($user_prompt))
        <ul>
            <li><strong>User Prompt:</strong> {{ $user_prompt }}</li>
        </ul>
    @endif
    @if(isset($response) && !empty($response))
        <ul>
            <li><strong>AI Response:</strong> {{ $response }}</li>
        </ul>
    @endif

    @if(isset($queryResult) && is_array($queryResult))
        <div class="query-result">
            <strong>Query Results:</strong>
            <ul>
                @foreach($queryResult as $row)
                    <li>{!! $row !!}</li>
                @endforeach
            </ul>
        </div>
    @elseif(isset($queryResult))
        @if(Str::contains($queryResult, 'javascript'))
            @php
                $updatedQueryResult = preg_replace('/javascript/i', '', $queryResult);
            @endphp

            {{-- If the response contains JavaScript, inject it into the page --}}
            <div id="chart-container">
                <canvas id="myChart">
                    <script>
                        {!! $updatedQueryResult !!}
                    </script>
                </canvas>
            </div>
        @else
            {{-- Otherwise, display the result as-is (for tables or other HTML) --}}
            <div class="query-result">
                {!! $queryResult !!}
            </div>
        @endif
    @endif
</div>
</body>
</html>
