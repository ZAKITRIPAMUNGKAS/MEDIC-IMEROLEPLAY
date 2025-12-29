<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class RecaptchaController extends Controller
{
    public static function verify($recaptchaResponse)
    {
        $secretKey = env('RECAPTCHA_SECRET_KEY');
        
        if (!$secretKey) {
            return false;
        }

        $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret' => $secretKey,
            'response' => $recaptchaResponse,
            'remoteip' => request()->ip()
        ]);

        $result = $response->json();
        
        return $result['success'] ?? false;
    }
}
