<?php

namespace App\Http\Controllers;

use App\Models\Log;
use App\Models\Session;
use App\Models\Station;
use Carbon\Carbon;
use DateInterval;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Arr;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;

class DashboardController extends Controller
{
    public function root()
    {
        if (session()->has('loginSuccess')) {
            session()->flash('loginSuccess', session('loginSuccess'));
        }
        return redirect()->route('dashboard.index'); 
    }

    public function index()
    {
        return view('dashboard.index', [
            'page' => 'dashboard'
        ]);
    }

    public function load() {
        $stations = Station::select('active', 'running', 'rotation')->get();

        $chart_Station = [0, 0, 0, 0, 0, 0];

        foreach ($stations as $station) {
            if ($station->active) {
                if ($station->running) $chart_Station[0]++;
                else {
                    $chart_Station[1]++;
                    if ($station->rotation === 1) $chart_Station[2]++;
                    else if ($station->rotation === 0) $chart_Station[3]++;
                    else if ($station->rotation === -1) $chart_Station[4]++;
                }
            } else $chart_Station[5]++;
        }

        $chart_0 = $chart_Station;
        $chart_0[1] = 0;
        $chart_1 = $chart_Station;
        $chart_1[2] = 0;
        $chart_1[3] = 0;
        $chart_1[4] = 0;
        $sum_station = array_sum($chart_0);
        if ($sum_station > 1) $chart_text = $sum_station.' Stations'; 
        else if ($sum_station == 1) $chart_text = $sum_station.' Station';
        else {
            $chart_0 = [];
            $chart_1 = [];
            $chart_text = "No Station";
        }

        $data_sessions = Session::select(
                DB::raw('COALESCE(DATE(start_at), \'0000-00-00\') as date'),
                DB::raw('COALESCE(ROUND(SUM(initial_mass)/1000, 2), 0) as initial_mass'),
                DB::raw('COALESCE(ROUND(SUM(final_mass)/1000, 2), 0) as final_mass'),
                DB::raw('COALESCE(ROUND(SUM(energy)/1000, 2), 0) as energy'),
                DB::raw('COALESCE(ROUND(SUM(duration)/3600, 2), 0) as duration')
            )
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->limit(7)
            ->get()
            ->toArray();

        array_walk_recursive($data_sessions, function (&$value) {
            if ($value === null) {
                $value = 0;
            }
        });

        $length = count($data_sessions);
        if ($length === 0) {
            $today = Carbon::today()->format('Y-m-d');
            $yesterday = Carbon::yesterday()->format('Y-m-d');
            $data_sessions = array_merge($data_sessions, [
                [
                    'date' => $yesterday,
                    'initial_mass' => 0,
                    'final_mass' => 0,
                    'energy' => 0
                ],
                [
                    'date' => $today,
                    'initial_mass' => 0,
                    'final_mass' => 0,
                    'energy' => 0
                ]
            ]);
        } else if ($length === 1) {
            $existing_date = $data_sessions[0]['date'];
            $carbonDate = Carbon::createFromFormat('Y-m-d', $existing_date);
            $new_date = $carbonDate->subDay()->format('Y-m-d');
            array_push($data_sessions, [
                'date' => $new_date,
                'initial_mass' => 0,
                'final_mass' => 0,
                'energy' => 0
            ]);
        }

        $data_sessions = array_reverse($data_sessions);
        $date = array_column($data_sessions, 'date');
        $initial_mass = array_column($data_sessions, 'initial_mass');
        $final_mass = array_column($data_sessions, 'final_mass');
        $energy = array_column($data_sessions, 'energy');

        $lifetime = Station::select(
                DB::raw('COALESCE(ROUND(SUM(mass)/1000, 1), 0) as mass'),
                DB::raw('COALESCE(ROUND(SUM(duration)/3600), 0) as duration'),
                DB::raw('COALESCE(ROUND(SUM(energy)/1000, 1), 0) as energy')
            )
            ->first();
        $count = Session::count();

        return response()->json([
            'msg' => 'OK',
            'data' => [
                'chart_mass' => [
                    'labels' => $date,
                    'initial_mass' => $initial_mass,
                    'final_mass' => $final_mass
                ],
                'chart_energy' => [
                    'labels' => $date,
                    'energy' => $energy,
                    'title' => !empty($date) ? $date[0] . ' to ' . $date[count($date) - 1] : 'None',
                    'sum' => array_sum($energy)
                ],
                'chart_station' => [
                    'chart_0' => $chart_0,
                    'chart_1' => $chart_1,
                    'chart_text' => $chart_text
                ],
                'statistics' => [
                    'session' => $count,
                    'energy' => $lifetime->energy,
                    'duration' => $lifetime->duration,
                    'production' => $lifetime->mass
                ]
            ]
        ]);
    }

    public function statistics(Request $request)
    {
        $id = $request->input('id');

        if($id == 0) {
            $statistics = Station::select(
                DB::raw('COALESCE(ROUND(SUM(mass)/1000, 1), 0) as mass'),
                DB::raw('COALESCE(ROUND(SUM(duration)/3600), 0) as duration'),
                DB::raw('COALESCE(ROUND(SUM(energy)/1000, 1), 0) as energy')
            )
            ->first();
            $count = Session::count();
        } else {
            $statistics = Station::select(
                DB::raw('COALESCE(ROUND(mass/1000, 1), 0) as mass'),
                DB::raw('COALESCE(ROUND(duration/3600), 0) as duration'),
                DB::raw('COALESCE(ROUND(energy/1000, 1), 0) as energy')
            )
            ->where('id', $id)
            ->first();
            $count = Session::where('id_station', $id)->count();
        }

        return response()->json([
            'msg' => 'OK',
            'data' => [
                'statistics' => [
                    'session' => $count,
                    'energy' => $statistics->energy,
                    'duration' => $statistics->duration,
                    'production' => $statistics->mass
                ]
            ]
        ]);
    }

    public function table()
    {
        $id_running = Station::where('running', true)->pluck('id');
        $id_session = Session::whereIn('id_station', $id_running)
            ->select('id_station', 'start_at')
            ->orderBy('id', 'desc')
            ->get()
            ->unique('id_station');
        $running = [];
        $now = Carbon::now();
        foreach ($id_session as $session) {
            $startAt = Carbon::parse($session->start_at);
            $diff = $startAt->diff($now);
            $totalHours = $diff->d * 24 + $diff->h;        
            $running[$session->id_station] = sprintf('%02d:%02d:%02d', $totalHours, $diff->i, $diff->s);
        }
        
        return DataTables::of(Station::get())
            ->addIndexColumn()
            ->removeColumn('added_at')
            ->removeColumn('energy')
            ->removeColumn('mass')
            ->removeColumn('duration')
            ->setRowAttr([
                'style' => 'text-align: center;',
                'class' => 'vertical-center station-row'
            ])
            ->editColumn('name', function($data) {
                return view('dashboard.href_name', ['data'=> $data]);
            })
            ->addColumn('status', function($data) use ($running) {
                return view('dashboard.status', ['data'=> $data, 'running' => $running]);
            })
            ->addColumn('action', function($data) {
                return view('dashboard.button', ['data'=> $data]);
            })
            ->rawColumns(['name', 'status', 'action'])
            ->make(true);
    }

    public function action(Request $request)
    {
        $id_station = $request->input('id_station');
        $station = Station::where('id', $id_station)->first();
        if (is_null($station)) {
            return response()->json([
                'msg' => 'ERROR',
                'data' => [
                    'id' => $id_station,
                    'name' => 'Station',
                    'info' => 'Station not found!'
                ]
            ]);
        }

        try {
            $action = $request->input('action');
            $response = Http::asForm()->post('http://'.$station->ip_address.'/action', [
                'action' => $action,
                'token' => $station->token
            ])->throw();
            $data = $response->json();
            $msg = $data['msg'];
            if ($msg != "ERROR") {
                $info = 'Success to '.str_replace('_', ' ', $action).' the station.';
                Station::where('id', $id_station)->update([
                    'running' => $data['running'],
                    'rotation' => $data['rotation']
                ]);
                $station = Station::where('id', $id_station)->first();

                $log = [
                    'timestamp'   => now(),
                    'id_user'     => auth()->id(),
                    'id_station'  => $id_station,
                    'host'        => Arr::get($_SERVER,'HTTP_HOST'),
                    'entity'      => true,
                    'activity'    => ucfirst('Run action '.str_replace('_', ' ', $action).' to the station')
                ];
                Log::create($log);

                $stations = Station::select('active', 'running', 'rotation')->get();

                $chart_Station = [0, 0, 0, 0, 0, 0];

                foreach ($stations as $each_station) {
                    if ($each_station->active) {
                        if ($each_station->running) $chart_Station[0]++;
                        else {
                            $chart_Station[1]++;
                            if ($each_station->rotation === 1) $chart_Station[2]++;
                            else if ($each_station->rotation === 0) $chart_Station[3]++;
                            else if ($each_station->rotation === -1) $chart_Station[4]++;
                        }
                    } else $chart_Station[5]++;
                }

                $chart_0 = $chart_Station;
                $chart_0[1] = 0;
                $chart_1 = $chart_Station;
                $chart_1[2] = 0;
                $chart_1[3] = 0;
                $chart_1[4] = 0;

                return response()->json([
                    'msg' => $msg,
                    'data' => [
                        'name' => $station->name,
                        'info' => $info,
                        'status' => view('dashboard.status', ['data'=> $station])->render(),
                        'button' => view('dashboard.button', ['data'=> $station])->render(),
                        'chart_station' => [
                            'chart_0' => $chart_0,
                            'chart_1' => $chart_1
                        ],
                    ]
                ]);
            } else {
                $info = $data['data'];
            }
        } catch (RequestException $e) {
            $msg = "ERROR";
            $info = "Station server error.";
        } catch (ConnectionException $e) {
            $msg = "ERROR";
            $info = "Failed to connect station.";
        }

        return response()->json([
            'msg' => $msg,
            'data' => [
                'name' => $station->name,
                'info' => $info
            ]
        ]);
    }
}
