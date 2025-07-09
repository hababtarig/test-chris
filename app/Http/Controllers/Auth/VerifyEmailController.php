<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

class VerifyEmailController extends Controller
{
    public function __invoke(EmailVerificationRequest $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->route('login')->with('status', 'Email already verified. Please login.');
        }

        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));
        }
        auth()->logout();

        return redirect()->route('login')->with('status', 'Email verified! Please wait for admin approval.');
    }
}
