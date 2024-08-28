<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class MeController extends Controller
{
    public function settings()
    {
        return view('me.settings', [
            'page' => 'User Settings',
            'user' => auth()->user()
        ]);
    }

    public function username(Request $request)
    {
        $username = $request->input('username');
        $used = User::where('username', $username)->value('id');
        if ($used) {
            $msg = 'ERROR';
        } else {
            $msg = 'OK';
        }

        return response()->json([
            'msg' => $msg,
            'data' => $username
        ]);
    }

    public function submit(Request $request)
    {
        $data = $request->all();
        $data['admin'] = false;
        if (isset($data['role'])) {
            if ($data['role'] == 'administrator') {
                $data['admin'] = true;
            }
            unset($data['role']);
        }

        unset($data['_token']);
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);

            User::where('id', auth()->user()->id)->update($data);
            
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('auth.index');
        } else {
            User::where('id', auth()->user()->id)->update($data);
            return redirect()->route('root');
        }          
    }

    public function cert()
    {
        $path = public_path('certs/pipernigrum.local.crt');

        if (file_exists($path)) {
            return response()->download($path);
        }

        abort(404);
    }

    public function logs()
    {
        $path = base_path('logs/login.log');

        if (file_exists($path)) {
            return response()->download($path);
        }

        abort(404);
    }
}
