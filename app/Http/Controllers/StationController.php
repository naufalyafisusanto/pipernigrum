<?php

namespace App\Http\Controllers;

use App\Models\Data;
use App\Models\Session;
use App\Models\Station;
use Carbon\Carbon;
use DateTime;
use Illuminate\Http\Request;

class StationController extends Controller
{
    public function index(Request $request)
    {   
        $id = $request->input('id');

        if (!Station::where('id', $id)->exists()) {
            return abort(404, 'Station with id '.$id.' not found.');
        }

        $station_date = [];
        $station_session = [];

        $session = Session::selectRaw('id, DATE(start_at) AS date, start_at')
            ->where('id_station', $id)
            ->orderBy('id', 'desc')
            ->first();
        
        if ($session) {
            $station_date = Session::selectRaw('DATE(start_at) AS date, COUNT(*) AS count')
                ->where('id_station', $id)
                ->groupBy('date')
                ->orderBy('date', 'desc')
                ->get();
            $station_session = Session::select('id','start_at')
                ->where('id_station', $id)
                ->whereDate('start_at', $session->date)
                ->orderBy('id', 'desc')
                ->get();
        } else {
            $session = false;
        }
        
        $station = Station::where('id', $id)->first();

        return view('station.index', [
            'page' => 'station',
            'station' => $station,
            'session' => $session,
            'station_date' => $station_date,
            'station_session' => $station_session
        ]);
    }

    public function load(Request $request)
    {
        $id_session = $request->input('id_session');
        $limit_data = 40;

        $last = new DateTime(Data::where('id_session', $id_session)
            ->orderBy('id', 'desc')
            ->value('timestamp'));
        $last->modify('-'.(15*$limit_data).'seconds');
        $chart = Data::select('id', 'timestamp', 'voltage', 'power', 'temp', 'humidity')
            ->where('id_session', $id_session)
            ->where('timestamp', '>', $last->format('Y-m-d H:i:s')) // Mods
            ->orderBy('id', 'desc')
            ->limit($limit_data) // Mods
            ->get();
        $card = Data::where('id_session', $id_session)
            ->orderBy('id', 'desc')
            ->first();
        $session = Session::select('start_at', 'eta', 'end_at', 'energy', 'initial_mass', 'final_mass')
            ->where('id', $id_session)
            ->first();

        $start = Carbon::parse($session->start_at);

        if (is_null($session->end_at)) {
            $card['mass_title'] = "Initial Mass";
            $card['mass'] = $session->initial_mass;
            $end = Carbon::now();
        } else {
            $card['mass_title'] = "Final Mass";
            $card['mass'] = $session->final_mass;
            $end = Carbon::parse($session->end_at);
        }

        $diff = $start->diff($end);
        $card['duration'] = sprintf('%02d:%02d:%02d', $diff->h + ($diff->days * 24), $diff->i, $diff->s);
        $card['energy'] = $session->energy;

        if (is_null($session->eta)) {
            if (is_null($session->end_at)) $card['status'] = "PREHEAT";
            else $card['status'] = "STOPPED";
        } else {
            if (is_null($session->end_at)) $card['status'] = "ETA ".$session->eta;
            else {
                $eta = Carbon::parse($session->eta);
                if ($end->greaterThan($eta)) $card['status'] = "FINISHED";
                else $card['status'] = "STOPPED";
            }
        }      

        Carbon::setLocale('en');
        $last_update = Carbon::createFromFormat('Y-m-d H:i:s', $chart[0]->timestamp)->diffForHumans();

        return response()->json([
            'msg' => 'OK',
            'data' => [
                'last_update' => $last_update,
                'chart' => $chart,
                'card' => $card
            ]
        ]);
    }

    public function update(Request $request)
    {        
        $id_session = $request->input('id_session');
        $last_id = $request->input('last_id');
        
        $chart = Data::select('id', 'timestamp', 'voltage', 'power', 'temp', 'humidity')
            ->where('id_session', $id_session)
            ->where('id', '>', $last_id)
            ->orderBy('id', 'desc')
            ->get();
        $card = Data::where('id_session', $id_session)
            ->orderBy('id', 'desc')
            ->first();
        $session = Session::select('eta', 'end_at', 'energy', 'initial_mass', 'final_mass')
            ->where('id', $id_session)
            ->first();
        
        if (is_null($session->end_at)) {
            $card['mass_title'] = "Initial Mass";
            $card['mass'] = $session->initial_mass;
            $end = Carbon::now();
        } else {
            $card['mass_title'] = "Final Mass";
            $card['mass'] = $session->final_mass;
            $end = Carbon::parse($session->end_at);
        }

        $card['energy'] = $session->energy;

        if (is_null($session->eta)) {
            if (is_null($session->end_at)) $card['status'] = "PREHEAT";
            else $card['status'] = "STOPPED";
        } else {
            if (is_null($session->end_at)) $card['status'] = "ETA ".$session->eta;
            else {
                $eta = Carbon::parse($session->eta);
                if ($end->greaterThan($eta)) $card['status'] = "FINISHED";
                else $card['status'] = "STOPPED";
            }
        }      

        Carbon::setLocale('en');
        if (count($chart) > 0) $last_update = Carbon::createFromFormat('Y-m-d H:i:s', $chart[0]->timestamp)->diffForHumans();
        else $last_update = 0;

        return response()->json([
            'msg' => 'OK',
            'data' => [
                'last_update' => $last_update,
                'chart' => $chart,
                'card' => $card
            ]
        ]);
    }

    public function date(Request $request)
    {
        $id = $request->input('id');
        $date = $request->input('date');
        
        $station_session = Session::select('id','start_at')
            ->where('id_station', $id)
            ->whereDate('start_at', $date)
            ->orderBy('id', 'desc')
            ->get();

        return view('station.session', [
            'station_session' => $station_session
        ]);
    }
}