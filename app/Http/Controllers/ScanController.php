<?php

namespace App\Http\Controllers;

use App\Models\Station;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Redirect;

class ScanController extends Controller
{
    public function index()
    {
        return view('scan.index', [
            'page' => 'scan'
        ]);
    }
    
    public function scanner()
    {   
        return view('scan.scanner');
    }

    public function verify(Request $request) {
        $encryptedText = $request->input('result');
        $key = "rliQbrzKBOE53mK3";
        $iv = "Vu648RUf7fr2PYxB";

        $decryptedText = openssl_decrypt(base64_decode($encryptedText), "AES-128-CBC", $key, OPENSSL_RAW_DATA, $iv);
        if ($decryptedText === false) {
            return response()->json([
                'msg' => 'ERROR',
                'data' => 'invalid'
            ]);
        }
        
        return response()->json([
            'msg' => 'OK',
            'data' => 'valid'
        ]);
    }

    public function form(Request $request)
    {
        $encryptedText = $request->input('result');
        $key = "rliQbrzKBOE53mK3";
        $iv = "Vu648RUf7fr2PYxB";

        $decryptedText = openssl_decrypt(base64_decode($encryptedText), "AES-128-CBC", $key, OPENSSL_RAW_DATA, $iv);
        $parts = explode(",", $decryptedText);

        $old_ip = '192.168.'.$parts[0];
        $mac = preg_replace('/[^\x20-\x7E]/', '', $parts[1]);

        $used = Station::whereNotNull('ip_address')->get()->pluck("ip_address");
        $host = [];
        foreach ($used as $ip) {
            $host[] = explode('.', $ip)[3];
        }
        for ($i = 201; $i <= 230; $i++) {
            if (!in_array(strval($i), $host)) {
                $free[] = strval($i);
            }
        }
        
        return view('scan.form', [
            'page' => 'scan',
            'free' => $free,
            'form' => [
                'old_ip' => $old_ip,
                'mac' => $mac
            ]
        ]);
    }

    public function name(Request $request)
    {
        $name = $request->input('name');
        $used = Station::where('name', $name)->value('id');
        if ($used) {
            $msg = 'ERROR';
        } else {
            $msg = 'OK';
        }

        return response()->json([
            'msg' => $msg,
            'data' => $name
        ]);
    }

    public function ping(Request $request)
    {
        $ip = $request->input('ip');
        
        $output = exec('timeout 0.5 ping -c 1 '.$ip);
        if ($output === "") {
            $msg = "ERROR";
        } else {
            $msg = "OK";
        }

        return response()->json([
            'msg' => $msg,
            'data' => $ip
        ]);
    }

    public function submit(Request $request)
    {
        $name = $request->input('name');
        $new_ip = $request->input('new_ip');
        $old_ip = $request->input('old_ip');
        $mac = $request->input('mac');
        $token = dechex(crc32(bin2hex(random_bytes(8))));
                
        try {
            $response = Http::asForm()->post('http://'.$old_ip.'/config', [
                'ip' => $new_ip,
                'hostname' => $name,
                'token' => $token
            ])->throw();
            $data = $response->json();
            $msg = $data['msg'];
            $info = $data['data'];
            $status = $response->status();
            Station::create([
                'token' => $token,
                'name' => $name,
                'ip_address' => $new_ip,
                'mac_address' => $mac,
                'added_at' => now()->addHours(7)
            ]);
        } catch (RequestException $e) {
            $msg = "ERROR";
            $status = $e->response->status();
            $info = $e->getMessage();
        }
        
        return response()->json([
            'msg' => $msg,
            'data' => [
                'status' => $status,
                'info' => $info
            ]
        ]);
    }
}
