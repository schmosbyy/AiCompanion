<?php

namespace Schmosbyy\AiCompanion\Http\Controllers;

use Illuminate\Http\Request;

class AiController extends Controller
{
    public function ask(Request $request)
    {
        $prompt = $request->input('user_input');
        return "You entered: " . $prompt;
    }
}
