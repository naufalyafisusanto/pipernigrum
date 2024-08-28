<?php

namespace App\Http\Controllers;

use App\Models\Log;
use App\Models\Station;
use App\Models\User;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class LogsController extends Controller
{
    public function index()
    {
        return view('logs.index', [
            'page' => 'logs'
        ]);
    }

    public function table()
    {
        $users = User::pluck('username', 'id')->toArray();
        $stations = Station::pluck('name', 'id')->toArray();
        
        return DataTables::of(Log::all())
            ->addIndexColumn()
            ->setRowAttr([
                'style' => 'text-align: center;',
                'class' => 'vertical-center station-row'
            ])
            ->addColumn('username', function($data) use ($users) {
                return $users[$data->id_user];
            })
            ->addColumn('station', function($data) use ($stations) {
                return $stations[$data->id_station];
            })
            ->editColumn('entity', function($data) {
                return view('logs.entity', ['data'=> $data]);
            })
            ->rawColumns(['entity'])
            ->make(true);
    }
}