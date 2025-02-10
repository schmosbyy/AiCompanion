<?php
return [
    'timeout' => env('AI_REQUEST_TIMEOUT', 600),
    'api_url' => env('AI_API_URL', 'http://127.0.0.1:11435'),
    'model' => env('AI_MODEL_NAME', 'qwen2.5-coder:14b'),
];