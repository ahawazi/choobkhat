<?php

use App\Models\Session;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

Artisan::command('inspire', function () {
    config()->set('database.connections.mysql.database', 'invobook');

    $invobookSessions = DB::connection('mysql')
        ->table('work_sessions')
        ->get();

    $invobookSessions->each(function ($invobookSession) {
        $start = new Carbon($invobookSession->start);
        $end = new Carbon($invobookSession->end);

        if ($start->diff($end)->totalSeconds != $invobookSession->duration) {
            $this->error('time mismatch on session #'.$invobookSession->id);
            $this->error('duration: '.$invobookSession->duration);
            $this->error('diff: '.$start->diff($end)->totalSeconds);
        }

        Session::query()->create([
            'user_id' => 1,
            'start' => $invobookSession->start,
            'end' => $invobookSession->end,
            'project_id' => null,
            'notes' => $invobookSession->description,
        ]);
    });

    $this->info('Successfully imported Invobook sessions!');
})->purpose('Display an inspiring quote')->hourly();
