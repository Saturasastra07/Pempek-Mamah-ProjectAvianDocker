<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class AuthController extends Controller
{
    public function showLogin() {
        return view('auth.login');
    }

    public function showRegister() {
        return view('auth.register');
    }

    public function register(Request $request) {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|unique:users',
            'password' => 'required|min:6',
            'province' => 'required',
            'city' => 'required',
            'district' => 'required',
            'full_address' => 'required',
        ]);

        $user = User::create([
            'name' => $request->name,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'province' => $request->province,
            'city' => $request->city,
            'district' => $request->district,
            'full_address' => $request->full_address,
        ]);

        Auth::login($user);
        return redirect()->route('home');
    }

    public function login(Request $request) {
        $request->validate([
            'phone' => 'required',
            'password' => 'required',
        ]);

        $phone = $request->phone;

        if (str_starts_with($phone, '0')) {
            $phone = '+62' . substr($phone, 1);
        }

        if (Auth::attempt(['phone' => $phone, 'password' => $request->password])) {
            $request->session()->regenerate();
            return redirect()->route('home'); 
        }

        return back()->withErrors(['phone' => 'Nomor atau password salah!']);
    }

    public function logout(Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('home');
    }

    public function showChangeName() {
        return view('profile.change-name');
    }

    public function reviewName(Request $request) {
        if ($request->isMethod('get')) {
            $newName = session('new_name_backup');
            if (!$newName) return redirect()->route('profile.change-name');
        } else {
            $request->validate(['new_name' => 'required|string|max:255']);
            $newName = $request->new_name;
            session(['new_name_backup' => $newName]);
        }

        return view('profile.review-changeProfile', compact('newName'));
    }

    public function updateName(Request $request) {
        $request->validate([
            'new_name' => 'required|string|max:255',
            'password' => 'required',
        ]);

        if (!Hash::check($request->password, auth()->user()->password)) {
            return back()->withErrors(['password' => 'Password yang Anda masukkan salah.']);
        }

        $user = auth()->user();
        $user->name = $request->new_name;
        $user->save();

        return redirect()->route('profile')->with('success', 'Nama berhasil diperbarui!');
    }

    public function showChangePassword() {
        return view('profile.change-password');
    }

    public function updatePasswordFinal(Request $request) {
        $request->validate([
            'new_password' => 'required|string|min:6',
        ]);

        $user = User::find(auth()->user()->id);
        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json(['success' => true, 'message' => 'Password berhasil diubah']);
    }

    public function updatePhoto(Request $request) {
        $request->validate(['photo' => 'required']);
        $image_service = $request->photo; 
        $image_parts = explode(";base64,", $image_service);
        $image_type_aux = explode("image/", $image_parts[0]);
        $image_type = $image_type_aux[1];
        $image_base64 = base64_decode($image_parts[1]);

        $fileName = 'profile_' . auth()->id() . '_' . time() . '.' . $image_type;
        $path = 'profile-photos/' . $fileName;
        
        Storage::disk('public')->put($path, $image_base64);
        $user = auth()->user();
        
        if ($user->profile_photo) {
            Storage::disk('public')->delete($user->profile_photo);
        }

        $user->profile_photo = $path;
        $user->save();

        return response()->json(['success' => true, 'path' => asset('storage/' . $path)]);
    }
}