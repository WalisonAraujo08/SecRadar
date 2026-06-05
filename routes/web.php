<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\Client\DashboardController;
use App\Http\Controllers\Client\ScanController;
use App\Http\Controllers\Client\EmailController;
use App\Http\Controllers\Client\AlertController;
use App\Http\Controllers\Client\SubscriptionController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\ClientsController;
use App\Http\Controllers\Admin\ScanLogsController;
use App\Http\Controllers\Marketing\LandingController;
use App\Http\Controllers\Webhook\MercadoPagoController;
use Illuminate\Support\Facades\Route;

// ─── LANDING / MARKETING ─────────────────────────────────────────────────────
Route::get('/', [LandingController::class, 'index'])->name('home');
Route::get('/planos', [LandingController::class, 'pricing'])->name('pricing');

// ─── AUTH ─────────────────────────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/entrar', [LoginController::class, 'showForm'])->name('login');
    Route::post('/entrar', [LoginController::class, 'login'])
        ->middleware('throttle:5,1');             // 5 tentativas por minuto por IP

    Route::get('/cadastro', [RegisterController::class, 'showForm'])->name('register');
    Route::post('/cadastro', [RegisterController::class, 'register'])
        ->middleware('throttle:3,1');             // 3 cadastros por minuto por IP

    Route::get('/recuperar-senha', [PasswordResetController::class, 'showForm'])->name('password.request');
    Route::post('/recuperar-senha', [PasswordResetController::class, 'sendLink'])
        ->name('password.email')
        ->middleware('throttle:3,5');             // 3 tentativas a cada 5 minutos

    Route::get('/resetar-senha/{token}', [PasswordResetController::class, 'showReset'])->name('password.reset');
    Route::post('/resetar-senha', [PasswordResetController::class, 'reset'])
        ->name('password.update')
        ->middleware('throttle:3,5');
});

Route::post('/sair', [LoginController::class, 'logout'])
    ->name('logout')
    ->middleware('auth');

// ─── CLIENTE (painel) ─────────────────────────────────────────────────────────
Route::middleware(['auth', 'subscription.active'])
    ->prefix('painel')
    ->name('client.')
    ->group(function () {

    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Varreduras — limita 1 varredura manual a cada 5 min (reforço na rota)
    Route::get('/varredura', [ScanController::class, 'index'])->name('scan.index');
    Route::post('/varredura/iniciar', [ScanController::class, 'start'])
        ->name('scan.start')
        ->middleware('throttle:3,5');
    Route::get('/varredura/status', [ScanController::class, 'status'])->name('scan.status');

    // E-mails monitorados — limita adição de e-mails
    Route::get('/emails', [EmailController::class, 'index'])->name('emails.index');
    Route::post('/emails', [EmailController::class, 'store'])
        ->name('emails.store')
        ->middleware('throttle:5,1');
    Route::delete('/emails/{id}', [EmailController::class, 'destroy'])->name('emails.destroy');

    // Alertas
    Route::get('/alertas', [AlertController::class, 'index'])->name('alerts.index');
    Route::post('/alertas/{id}/lido', [AlertController::class, 'markRead'])->name('alerts.read');
    Route::post('/alertas/todos-lidos', [AlertController::class, 'markAllRead'])->name('alerts.readAll');

    // Download agente
    Route::get('/download-agente', [DashboardController::class, 'downloadAgent'])->name('download.agent');

    // Parceiros
    Route::get('/parceiros', [\App\Http\Controllers\Client\ReferralController::class, 'index'])->name('referral.index');
    Route::post('/parceiros/pix', [\App\Http\Controllers\Client\ReferralController::class, 'updatePix'])
        ->name('referral.pix')
        ->middleware('throttle:5,1');
    Route::post('/parceiros/solicitar', [\App\Http\Controllers\Client\ReferralController::class, 'requestPartnership'])
        ->name('referral.request')
        ->middleware('throttle:3,10');            // 3 solicitações a cada 10 minutos
});

// ─── ASSINATURA ───────────────────────────────────────────────────────────────
Route::middleware('auth')
    ->prefix('assinatura')
    ->name('subscription.')
    ->group(function () {

    Route::get('/', [SubscriptionController::class, 'index'])->name('index');
    Route::post('/criar', [SubscriptionController::class, 'create'])
        ->name('create')
        ->middleware('throttle:3,5');             // Evita criação duplicada de assinatura
    Route::get('/callback', [SubscriptionController::class, 'callback'])->name('callback');
    Route::post('/cancelar', [SubscriptionController::class, 'cancel'])
        ->name('cancel')
        ->middleware('throttle:3,10');
});

// ─── ADMIN ────────────────────────────────────────────────────────────────────
Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/clientes', [ClientsController::class, 'index'])->name('clients.index');
    Route::get('/clientes/{id}', [ClientsController::class, 'show'])->name('clients.show');
    Route::post('/clientes/{id}/toggle', [ClientsController::class, 'toggle'])->name('clients.toggle');
    Route::get('/logs', [ScanLogsController::class, 'index'])->name('logs.index');
    Route::get('/parceiros', [\App\Http\Controllers\Admin\PartnersController::class, 'index'])->name('partners.index');
    Route::post('/parceiros/{id}/aprovar', [\App\Http\Controllers\Admin\PartnersController::class, 'approve'])->name('partners.approve');
    Route::post('/parceiros/{id}/rejeitar', [\App\Http\Controllers\Admin\PartnersController::class, 'reject'])->name('partners.reject');
});

// ─── WEBHOOKS ─────────────────────────────────────────────────────────────────
Route::post('/webhook/mercadopago', [MercadoPagoController::class, 'handle'])
    ->name('webhook.mp')
    ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class]);
