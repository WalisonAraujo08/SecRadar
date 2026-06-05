<?php

namespace App\Console\Commands;

use App\Jobs\RunScanJob;
use App\Models\User;
use Illuminate\Console\Command;

class RunAllScansCommand extends Command
{
    protected $signature   = 'secradar:scan-all';
    protected $description = 'Executa varredura em todos os clientes ativos';

    public function handle(): void
    {
        $users = User::whereHas('subscription', fn($q) => $q->where('status', 'authorized'))
            ->where('is_active', true)
            ->pluck('id');

        $this->info("Disparando varredura para {$users->count()} clientes...");

        $users->each(fn($id) => RunScanJob::dispatch($id)->onQueue('default'));

        $this->info('Jobs despachados com sucesso.');
    }
}
