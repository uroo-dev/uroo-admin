<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookmarkController;
use App\Http\Controllers\BrainDumpController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\CredentialController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\IdeaController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\SavingsController;
use App\Http\Controllers\SubscriptionController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Features — index route renders page with Livewire, CRUD handled by Controller
    Route::resource('projects', ProjectController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::resource('clients', ClientController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::resource('credentials', CredentialController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::resource('invoices', InvoiceController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::resource('notes', NoteController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::resource('bookmarks', BookmarkController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::resource('ideas', IdeaController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::resource('brain-dumps', BrainDumpController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::resource('savings', SavingsController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::resource('subscriptions', SubscriptionController::class)->only(['index', 'store', 'update', 'destroy']);

    // Extra routes for features with additional actions
    Route::patch('brain-dumps/{brain_dump}/toggle-archive', [BrainDumpController::class, 'toggleArchive'])->name('brain-dumps.toggle-archive');
    Route::post('savings/{goal}/deposit', [SavingsController::class, 'deposit'])->name('savings.deposit');
    Route::post('savings/{goal}/withdraw', [SavingsController::class, 'withdraw'])->name('savings.withdraw');
    Route::patch('subscriptions/{subscription}/toggle-payment', [SubscriptionController::class, 'togglePayment'])->name('subscriptions.toggle-payment');

    // GitHub & Quality Control
    Route::get('/github', function () { return view('github.index'); })->name('github');
    Route::get('/quality-control', function () { return view('quality-control.index'); })->name('quality-control');
});
