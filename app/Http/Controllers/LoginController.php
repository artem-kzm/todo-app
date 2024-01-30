<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http;
use Illuminate\Support\Facades\Auth;

class LoginController
{
    /**
     * @throws AuthenticationException
     */
    public function authenticate(LoginRequest $request): Http\Response
    {
        $credentials = $request->validated();

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            return response('Authenticated.');
        }

        throw new AuthenticationException();
    }
}
