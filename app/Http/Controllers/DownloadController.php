<?php

namespace App\Http\Controllers;

use App\Models\Data;
use App\Models\Session;
use App\Models\Station;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Yajra\DataTables\Facades\DataTables;
use Box\Spout\Common\Type;
use Box\Spout\Writer\Common\Creator\WriterEntityFactory;
use Box\Spout\Writer\Common\Creator\WriterFactory;

class DownloadController extends Controller
{
    public function index()
    {
        $stations = Station::all(['id', 'name']);

        return view('download.index', [
            'page' => 'download',
            'select_stations' => $stations
        ]);
    }

    public function session(Request $request)
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

        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');

        $sessions = Session::select('id', 
            DB::raw("
                CONCAT(start_at, ' - ', 
                    CASE 
                        WHEN end_at IS NULL OR end_at = '' THEN 'Now' 
                        ELSE end_at 
                    END
                ) AS date_range
            "))
            ->where('id_station', $id_station)
            ->whereBetween('start_at', [$start_date, $end_date])
            ->orderBy('id', 'desc')
            ->get();
        
        if ($sessions->isEmpty()) {
            return response()->json([
                'msg' => 'NULL',
                'data' => [
                    'id' => $id_station,
                    'info' => 'No sessions found on '.$station->name.' from '.$start_date.' to '.$end_date.'.'
                ]
            ]);
        }

        return response()->json([
            'msg' => "OK",
            'data' => [
                'id' => $id_station,
                'view' => view('download.session', [
                    'sessions' => $sessions
                ])->render()
            ]
        ]);
    }

    public function preview(Request $request)
    {
        $sessions = $request->input('sessions', []);

        return DataTables::of(Data::whereIn('id_session', $sessions)->limit(500)->get())
            ->setRowAttr([
                'style' => 'text-align: center;',
                'class' => 'vertical-center'
            ])
            ->make(true);
    }

    public function datacsv(Request $request)
    {
        $sessionIds = $request->input('sessions', []);

        $response = new StreamedResponse(function() use ($sessionIds) {
            $handle = fopen('php://output', 'w');

            $columns = Schema::getColumnListing('data');
            $columns[3] .= '(V)';
            $columns[4] .= '(A)';
            $columns[5] .= '(Watt)';
            $columns[6] .= '(Hz)';
            $columns[8] .= '(°C)';
            $columns[9] .= '(%)';
            
            fputcsv($handle, $columns);

            Data::whereIn('id_session', $sessionIds)
                ->orderBy('id', 'asc')
                ->chunk(1000, function($data) use ($handle) {
                foreach ($data as $datum) {
                    fputcsv($handle, $datum->toArray());
                }
            });

            fclose($handle);
        }, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="sessions.csv"',
        ]);

        return $response;
    }

    public function dataxlsx(Request $request)
    {
        $sessionIds = $request->input('sessions', []);

        $response = new StreamedResponse(function() use ($sessionIds) {
            $writer = WriterFactory::createFromType(Type::XLSX);
            $writer->openToFile('php://output');

            $columns = Schema::getColumnListing('data');
            $columns[3] .= '(V)';
            $columns[4] .= '(A)';
            $columns[5] .= '(Watt)';
            $columns[6] .= '(Hz)';
            $columns[8] .= '(°C)';
            $columns[9] .= '(%)';

            $headerRow = WriterEntityFactory::createRowFromArray($columns);
            $writer->addRow($headerRow);

            Data::whereIn('id_session', $sessionIds)
                ->orderBy('id', 'asc')
                ->chunk(1000, function($data) use ($writer) {
                    foreach ($data as $datum) {
                        $rowData = array_values($datum->toArray());
                        $row = WriterEntityFactory::createRowFromArray($rowData);
                        $writer->addRow($row);
                    }
                });

            $writer->close();
        }, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="sessions.xlsx"',
        ]);

        return $response;
    }
}
