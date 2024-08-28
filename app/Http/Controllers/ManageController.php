<?php

namespace App\Http\Controllers;

use App\Models\Log;
use App\Models\Session;
use App\Models\Station;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Arr;

class ManageController extends Controller
{
    public function index()
    {
        return view('manage.index', [
            'page' => 'manage'
        ]);
    }

    public function table()
    {
        $host = str_ends_with(Arr::get($_SERVER,'HTTP_HOST'), '.local');

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
                return view('manage.href_name', ['data'=> $data]);
            })
            ->addColumn('status', function($data) use ($running) {
                return view('manage.status', ['data'=> $data, 'running' => $running]);
            })
            ->addColumn('action', function($data) {
                return view('manage.button', ['data'=> $data]);
            })
            ->editColumn('ip_address', function($data) use ($host) {
                return view('manage.href_ip', ['data'=> $data, 'host' => $host]);
            })
            ->rawColumns(['name', 'status', 'action', 'ip_address'])
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

                return response()->json([
                    'msg' => $msg,
                    'data' => [
                        'name' => $station->name,
                        'info' => $info,
                        'status' => view('manage.status', ['data'=> $station])->render(),
                        'button' => view('manage.button', ['data'=> $station])->render()
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

    public function running(Request $request)
    {
        $id_station = $request->input('id');
        $station = Station::where('id', $id_station)->first();

        if (is_null($station)) {
            return response()->json([
                'msg' => 'ERROR',
                'data' => [
                    'id' => $id_station,
                    'info' => 'Station not found!'
                ]
            ]);
        }

        try {
            $response = Http::get('http://'.$station->ip_address.'/running')->throw();
            if ($response->body() == 1) $msg = "RUNNING";
            else $msg = "STOPPED";
            $info = $station->ip_address;
        } catch (Exception $e) {
            $msg = "ERROR";
            $info = $e->getMessage();
        }

        return response()->json([
            'msg' => $msg,
            'data' => [
                'id' => $id_station,
                'info' => $info
            ]
        ]);
    }

    public function params(Request $request)
    {
        $id_station = $request->input('id');
        $station = Station::where('id', $id_station)->first();

        if (is_null($station)) {
            return abort(404, 'Station with id '.$id_station.' not found.');
        }

        try {
            $response = Http::get('http://'.$station->ip_address.'/params')->throw();
            $params = $response->json();

            foreach ($params as &$value) {
                $value = round($value, 2);
            }

            return view('manage.params', [
                'page' => 'manage_params',
                'station' => $station,
                'params' => $params
            ]);
        } catch (RequestException $e) {
            return abort(503, 'Station server error.');
        } catch (ConnectionException $e) {
            return abort(503, 'Failed to connect station.');
        }
    }

    public function params_submit(Request $request)
    {
        $data = $request->all();
        $station = Station::where('id', $data['id'])->first();

        if (is_null($station)) {
            return response()->json([
                'msg' => 'ERROR',
                'data' => [
                    'id' => $data['id'],
                    'info' => 'Station not found!'
                ]
            ]);
        }

        unset($data['id']);
        $data['token'] = $station->token;

        try {
            $response = Http::asForm()->post('http://'.$station->ip_address.'/params', $data)->throw();
            $response_data = $response->json();
            $msg = $response_data['msg'];
            $info = $response_data['data'];

            $log = [
                'timestamp'   => now(),
                'id_user'     => auth()->id(),
                'id_station'  => $station->id,
                'host'        => Arr::get($_SERVER,'HTTP_HOST'),
                'entity'      => false,
                'activity'    => ucfirst('Set new params to the station')
            ];
            Log::create($log);
        } catch (Exception $e) {
            $msg = "ERROR";
            $info = $e->getMessage();
        }

        return response()->json([
            'msg' => $msg,
            'data' => [
                'id' => $station->id,
                'info' => $info
            ]
        ]);
    }

    public function station(Request $request)
    {
        $id_station = $request->input('id');
        $station = Station::where('id', $id_station)->first();

        if (is_null($station)) {
            return abort(404, 'Station with id '.$id_station.' not found.');
        }

        $used = Station::whereNotNull('ip_address')->get()->pluck("ip_address");
        foreach ($used as $ip) {
            $host[] = explode('.', $ip)[3];
        }
        $free[] = explode('.', $station->ip_address)[3];
        for ($i = 201; $i <= 230; $i++) {
            if (!in_array(strval($i), $host)) {
                $free[] = strval($i);
            }
        }

        return view('manage.station', [
            'page' => 'manage_station',
            'station' => $station,
            'free' => $free
        ]);
       
    }

    public function station_update(Request $request)
    {   
        $id_station = $request->input('id');
        $station = Station::where('id', $id_station)->first();

        if (is_null($station)) {
            return response()->json([
                'msg' => 'ERROR',
                'data' => [
                    'id' => $id_station,
                    'info' => 'Station not found!'
                ]
            ]);
        }

        $file = $request->file('firmware');

        if ($file->isValid()) {              
            try {
                $response = Http::asMultipart()->post('http://' . $station->ip_address . '/update', [
                    [
                        'name' => 'file',
                        'contents' => fopen($file->getRealPath(), 'r'),
                        'filename' => $file->getClientOriginalName()
                    ],
                    [
                        'name' => 'firmwaretype',
                        'contents' => 'firmware'
                    ],
                    [
                        'name' => 'token',
                        'contents' => $station->token
                    ]
                ])->throw();
                if ($response->body() == "OK") {
                    $msg = "OK";
                    $info = "Firmware successfully updated";
                } else {
                    $msg = "FAIL";
                    $info = $response->body();
                }

                $log = [
                    'timestamp'   => now(),
                    'id_user'     => auth()->id(),
                    'id_station'  => $id_station,
                    'host'        => Arr::get($_SERVER,'HTTP_HOST'),
                    'entity'      => false,
                    'activity'    => ucfirst('Update new firmware to the station')
                ];
                Log::create($log);
            } catch (Exception $e) {
                $msg = "ERROR";
                $info = $e->getMessage();
            }
        } else {
            $msg = "ERROR";
            $info = "Firmware File Corrupt!";
        }

        return response()->json([
            'msg' => $msg,
            'data' => [
                'id' => $id_station,
                'info' => $info
            ]
        ]);
    }

    public function station_name(Request $request)
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

    public function station_config(Request $request)
    {
        $id_station = $request->input('id');
        $station = Station::where('id', $id_station)->first();

        if (is_null($station)) {
            return response()->json([
                'msg' => 'ERROR',
                'data' => [
                    'id' => $id_station,
                    'info' => 'Station not found!'
                ]
            ]);
        }

        $name = $request->input('name');
        $new_ip = $request->input('new_ip');
                
        try {
            $response = Http::asForm()->post('http://'.$station->ip_address.'/config', [
                'ip' => $new_ip,
                'hostname' => $name,
                'token' => $station->token
            ])->throw();
            $data = $response->json();
            $msg = $data['msg'];
            $info = $data['data'];
            Station::where('id', $id_station)->update([
                'name' => $name,
                'ip_address' => $new_ip
            ]);

            $log = [
                'timestamp'   => now(),
                'id_user'     => auth()->id(),
                'id_station'  => $id_station,
                'host'        => Arr::get($_SERVER,'HTTP_HOST'),
                'entity'      => false,
                'activity'    => ucfirst('Update new config to the station')
            ];
            Log::create($log);
        } catch (RequestException $e) {
            $msg = "ERROR";
            $info = $e->getMessage();
        }
        
        return response()->json([
            'msg' => $msg,
            'data' => [
                'info' => $info
            ]
        ]);
    }

    public function station_delete(Request $request)
    {
        $id_station = $request->input('id');
        $station = Station::where('id', $id_station)->first();

        if (is_null($station)) {
            return response()->json([
                'msg' => 'ERROR',
                'data' => [
                    'id' => $id_station,
                    'info' => 'Station not found!'
                ]
            ]);
        }

        $force_delete = $request->boolean('force');
        if ($force_delete) {
            Station::where('id', $id_station)->delete();
            $msg = "OK";
            $info = "Station successfully deleted";
        } else {
            try {
                $response = Http::asForm()->post('http://'.$station->ip_address.'/delete', [
                    'token' => $station->token
                ])->throw();
                $data = $response->json();
                $msg = $data['msg'];
                $info = $data['data'];
                Station::where('id', $id_station)->delete();
            } catch (RequestException $e) {
                $msg = "ERROR";
                $info = $e->getMessage();
            }
        }

        return response()->json([
            'msg' => $msg,
            'data' => [
                'info' => $info
            ]
        ]);
    }
}