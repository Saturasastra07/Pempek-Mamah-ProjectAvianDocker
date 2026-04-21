<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function changePhone()
    {
        return view('profile.change-phone');
    }

    public function sendOtp(Request $request) 
    {
        $request->validate(['phone' => 'required']);
        
        $user = auth()->user();
        $user->pending_phone = $request->phone;
        $user->save();

        return response()->json(['message' => 'OTP Sent']);
    }

    public function updatePhone(Request $request)
    {
        $request->validate([
            'phone' => 'required|string|max:20',
        ]);

        $user = auth()->user();
        $user->phone = $request->phone;
        $user->save();

        return response()->json(['status' => 'success']);
    }

    public function verifyOtp(Request $request)
    {
        $user = auth()->user();
        $user->phone = $user->pending_phone;
        $user->pending_phone = null;
        $user->save();

        return response()->json(['status' => 'success']);
    }
}