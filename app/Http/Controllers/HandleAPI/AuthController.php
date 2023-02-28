<?php

namespace App\Http\Controllers\HandleAPI;

use App\Http\Controllers\Controller;
use App\Models\User;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{

    public function redirect($provider): \Symfony\Component\HttpFoundation\RedirectResponse|\Illuminate\Http\RedirectResponse
    {
        config(["services.$provider.redirect" => env('API_CALLBACK_URI') . "/$provider"]);

        return Socialite::driver($provider)->redirect();
    }

    public function callback($provider): \Illuminate\Http\JsonResponse
    {
        try {
            config(["services.$provider.redirect" => env('API_CALLBACK_URI') . "/$provider"]);
            $data = Socialite::driver($provider)->user();
            if (! auth()->attempt(['email' => $data['email'], 'password' => '123'])) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }
            $token = encrypt(auth()->id());

            return handleResponseAPI(true, '', $data);
        } catch (\Exception $e) {
            return handleResponseAPI('Có lỗi xảy ra! ' . $e->getMessage());
        }
    }

    public function test($token)
    {
        $user = User::query()->find(decrypt($token))->first();
        return handleResponseAPI('oke', 'success', $user);
    }
}
