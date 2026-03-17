<?php

use Illuminate\Support\Facades\Schedule;

use App\Jobs\AtualizarFeedsJob;
use App\Jobs\EnviarAniversariantesDoDiaJob;
use App\Jobs\DatabaseBackupJob;

Schedule::call(function () {
    AtualizarFeedsJob::dispatch();
})->twiceDaily(8, 14);

Schedule::job(new EnviarAniversariantesDoDiaJob())->dailyAt('08:00');

// Backup do banco de dados - todo dia às 06:00
Schedule::job(new DatabaseBackupJob())->dailyAt('06:00');
