<?php

namespace App\Console\Commands;

use App\Models\Station;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Symfony\Component\Process\Process;

class PingTask extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:ping-task';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Continuous Check Ping to All Stations';


    private const TASK_KEY = 'ping_task_last_run';
    private const INTERVAL_SECONDS = 5;

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if ($this->canRunTask()) {
            $this->scan();
            $this->updateLastRun();
        }
    }

    private function canRunTask(): bool
    {
        $lastRun = Cache::get(self::TASK_KEY);

        if (!$lastRun) {
            return true;
        }

        $currentTime = time();
        return ($currentTime - $lastRun) >= self::INTERVAL_SECONDS;
    }

    private function updateLastRun(): void
    {
        Cache::put(self::TASK_KEY, time(), self::INTERVAL_SECONDS);
    }

    private function scan()
    {
        $stations = Station::all(['id', 'ip_address']);
        $updateData = [];

        foreach ($stations as $station) {
            $ip = $station->ip_address;
            $status = $this->pingAddress($ip) ? 1 : 0;
            $running = 0;
            $rotation = 0;

            if ($status) {
                $stats = $this->getStatus($ip);
                if ($stats) {
                    $running = (int)$stats['running'];
                    $rotation = $stats['rotation'];
                }
            }

            $updateData[] = [
                'id' => $station->id,
                'active' => $status,
                'running' => $running,
                'rotation' => $rotation
            ];
        }

        $this->batchUpdate('stations', $updateData);
    }

    private function pingAddress($ip)
    {
        try {
            $file = fsockopen($ip, 80, $errno, $errstr, 1);
            if (!$file) {
                return false;
            } else {
                fclose($file);
                return true;
            }
        } catch (\Exception $e) {
            return false;
        }
    }

    private function getStatus($ip)
    {
        try {
            $response = Http::timeout(3)->get("http://{$ip}/status");
            if ($response->successful()) {
                return $response->json();
            }
        } catch (\Exception $e) {

        }
        return null;
    }

    public function batchUpdate($table, $values)
    {  
        $ids = [];
        $activeCases = [];
        $runningCases = [];
        $rotationCases = [];
        
        foreach ($values as $item) {
            $id = $item['id'];
            $ids[] = $id;
            $activeCases[] = "WHEN {$id} THEN {$item['active']}";
            $runningCases[] = "WHEN {$id} THEN {$item['running']}";
            $rotationCases[] = "WHEN {$id} THEN {$item['rotation']}";
        }
        
        $idList = implode(',', $ids);
        $activeCase = implode(' ', $activeCases);
        $runningCase = implode(' ', $runningCases);
        $rotationCase = implode(' ', $rotationCases);
        
        $query = "UPDATE stations SET 
            active = CASE id {$activeCase} END,
            running = CASE id {$runningCase} END,
            rotation = CASE id {$rotationCase} END
            WHERE id IN ({$idList})";
        
        DB::statement($query);
    }
}
