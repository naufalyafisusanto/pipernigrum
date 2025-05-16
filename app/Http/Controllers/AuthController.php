<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;

class AuthController extends Controller
{
    public function index(Request $request)
    {
        $ip = $request->header('CF-Connecting-IP') ?? $request->header('X-Forwarded-For') ?? $request->ip();
        $host = Arr::get($_SERVER,'HTTP_HOST');

        Log::channel('login')->info("INDEX|{$host}|{$ip}");
        return view('auth.login', [
            'page' => 'login'
        ]);
    }

    public function login(Request $request)
    {
        $ip = $request->header('CF-Connecting-IP') ?? $request->header('X-Forwarded-For') ?? $request->ip();
        
        if (RateLimiter::tooManyAttempts($ip, 2)) {
            $retry = RateLimiter::availableIn($ip);
            return back()->with('loginError', 'Too many login attempts. Please try again in ' . $retry . ' seconds.');
        }
        
        $credentials = $request->validate([
            'username' => ['required', 'min:8', 'max:32', 'regex:/^[a-zA-Z0-9_]+$/'],
            'password' => ['required', 'min:8', 'max:32', 'no_spaces']
        ], [
            'username.regex' => 'The username field format is invalid. Only letters, numbers, and underscores are allowed.',
            'password.no_spaces' => 'The password must not contain spaces.'
        ]);

        $username = $request->input('username');
        $host = Arr::get($_SERVER,'HTTP_HOST');
 
        if (Auth::attempt($credentials)) {    
            RateLimiter::clear($ip);
            Auth::logoutOtherDevices($request->input('password'));
            $request->session()->regenerate();
            User::where('username', Auth::user()->username)
                ->update([
                    'last_login' => date('Y-m-d H:i:s')
                ]);
            Log::channel('login')->info("SUCCESS|{$host}|{$ip}|{$username}");
            $intended = redirect()->intended()->getTargetUrl();
            if (str_contains($intended, 'naganoharamirai') && !str_contains($intended, '/pipernigrum')) {
                $intended .= '/pipernigrum';
                $intended = preg_replace('/^http:/', 'https:', $intended);
            }
            return redirect($intended)->with('loginSuccess', Auth::user()->name);
        }
        RateLimiter::increment($ip);
        $password = $request->input('password');   
        Log::channel('login')->warning("FAILED|{$host}|{$ip}|{$username}|{$password}");
        return back()->with('loginError', 'Username and password are incorrect.');
    }

    public function index_expo(Request $request)
    {
        $ip = $request->header('CF-Connecting-IP') ?? $request->header('X-Forwarded-For') ?? $request->ip();
        $host = Arr::get($_SERVER,'HTTP_HOST');

        Log::channel('expo')->info("INDEX|{$host}|{$ip}");
        return view('auth.expo', [
            'page' => 'login'
        ]);
    }

    public function login_expo(Request $request)
    {
        $ip = $request->header('CF-Connecting-IP') ?? $request->header('X-Forwarded-For') ?? $request->ip();
        
        $credentials = $request->validate([
            'username' => ['required', 'min:8', 'max:32', 'regex:/^[a-zA-Z0-9_]+$/'],
            'password' => ['required', 'min:8', 'max:32', 'no_spaces']
        ], [
            'username.regex' => 'The username field format is invalid. Only letters, numbers, and underscores are allowed.',
            'password.no_spaces' => 'The password must not contain spaces.'
        ]);

        $username = $request->input('username');
        $name = $request->input('name');
        $type = $request->input('type');
        $identity = $request->input('identity');
        $host = Arr::get($_SERVER,'HTTP_HOST');
        

        if (Auth::attempt($credentials)) {    
            $request->session()->regenerate();
            User::where('username', Auth::user()->username)
                ->update([
                    'last_login' => date('Y-m-d H:i:s')
                ]);
            Log::channel('expo')->info("SUCCESS|{$host}|{$ip}|{$username}|{$name}|{$type}|{$identity}");
            $intended = redirect()->intended()->getTargetUrl();
            if (str_contains($intended, 'naganoharamirai') && !str_contains($intended, '/pipernigrum')) {
                $intended .= '/pipernigrum';
                $intended = preg_replace('/^http:/', 'https:', $intended);
            }
            return redirect($intended)->with('loginSuccess', Auth::user()->name);
        }
        RateLimiter::increment($ip);
        $password = $request->input('password');
        Log::channel('expo')->warning("FAILED|{$host}|{$ip}|{$username}|{$password}|{$name}|{$type}|{$identity}");
        return back()->with('loginError', 'Username and password are incorrect.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
    
        return redirect()->route('auth.index')->with('logoutSuccess', 'Success');
    }
}
