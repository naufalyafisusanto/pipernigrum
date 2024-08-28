<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Data;
use App\Models\Session;
use App\Models\Station;
use Carbon\Carbon;

class ESPController extends Controller
{
    public function station(Request $request)
    {
        $ip = $request->json()->get('ip');
        $token = $request->json()->get('token');
        
        $id_station = Station::where('ip_address', $ip)->where('token', $token)->value("id");
        if ($id_station == null) {
            return response("-1")->header('Content-Type', 'text/plain');
        }
        
        return response($id_station)->header('Content-Type', 'text/plain');
    }

    public function start(Request $request)
    {
        $data = $request->json()->all();
        $token = Station::where('id', $data['id_station'])->value('token');

        if ($token != $data['token']) {
            return response("0,")->header('Content-Type', 'text/plain');
        }

        unset($data['token']);
        $data['token'] = dechex(crc32(bin2hex(random_bytes(8))));
        $session = Session::create($data);
        Station::where('id', $data['id_station'])->update(['running' => true]);

        return response($session->id.",".$session->token)->header('Content-Type', 'text/plain');
    }
    
    public function data(Request $request)
    {        
        $data = $request->json()->all();
        $session = Session::where('id', $data['id_session'])
            ->where('token', $data['token'])
            ->first();

        if (!$session) {
            return response("ERROR: INVALID SESSION OR TOKEN")->header('Content-Type', 'text/plain');
        }

        unset($data['token']);
        $query = Data::select('timestamp', 'power')
            ->where('id_session', $data['id_session'])
            ->orderBy('id', 'desc');
        if ($query->exists()) {
            $last = $query->first();
            $start = Carbon::parse($last->timestamp);
            $end = Carbon::parse($data['timestamp']);
            $energy = $session->energy + $start->diffInSeconds($end)*$last->power/3600;
            $start_duration = Carbon::parse($session->start_at);
            $end_duration = Carbon::now();
            Session::where('id', $data['id_session'])->update(['duration' => $start_duration->diffInSeconds($end_duration), 'energy' => $energy]);
        }
        
        Data::create($data);

        return response("OK")->header('Content-Type', 'text/plain');
    }

    public function eta(Request $request)
    {       
        $data = $request->json()->all();
        $session = Session::where('id', $data['id'])->first();

        if ($session->token != $data['token']) {
            return response("ERROR")->header('Content-Type', 'text/plain');
        }

        $data['eta'] = Carbon::createFromTimestamp($data['eta'], 'UTC')->format('Y-m-d H:i:s');
        unset($data['id']);
        unset($data['token']);
        Session::where('id', $session->id)->update($data);

        return response("OK")->header('Content-Type', 'text/plain');
    }

    public function end(Request $request)
    {
        $data = $request->json()->all();
        $session = Session::where('id', $data['id'])->first();

        if ($session->token != $data['token']) {
            return response("ERROR")->header('Content-Type', 'text/plain');
        }

        $last = Data::select('timestamp', 'power')
            ->where('id_session', $data['id'])
            ->orderBy('id', 'desc')
            ->first();
        $start = Carbon::parse($last->timestamp);
        $end = Carbon::parse($data['end_at']);
        $start_duration = Carbon::parse($session->start_at);
        $data['duration'] = $start_duration->diffInSeconds($end);
        $data['energy'] = $session->energy + $start->diffInSeconds($end)*$last->power/3600;

        unset($data['id']);
        unset($data['token']);
        Session::where('id', $session->id)->update($data);

        $query = Station::where('id', $session->id_station);
        $last_station = $query->select('mass', 'duration', 'energy')->first();
        $station = [
            'mass' => $last_station->mass + $data['final_mass'],
            'duration' => $last_station->duration + $data['duration'],
            'energy' => $last_station->energy + $data['energy'],
            'running' => false,
        ];
        $query->update($station);

        return response("OK")->header('Content-Type', 'text/plain');
    }
}