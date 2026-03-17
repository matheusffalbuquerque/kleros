<?php

use Illuminate\Support\Facades\Schedule;

use App\Jobs\AtualizarFeedsJob;
use App\Jobs\EnviarAniversariantesDoDiaJob;

Schedule::call(function () {
    AtualizarFeedsJob::dispatch();
})->twiceDaily(8, 14);

Schedule::job(new EnviarAniversariantesDoDiaJob())->dailyAt('08:00');
