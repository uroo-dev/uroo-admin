<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/github', function () { return view('github.index'); })->name('github');

    Route::prefix('projects')->group(function () {
        Route::get('/', \Modules\Projects\Livewire\ProjectList::class)->name('projects');
    });

    Route::prefix('credentials')->group(function () {
        Route::get('/', \Modules\Credential\Livewire\CredentialList::class)->name('credentials');
    });

    Route::prefix('clients')->group(function () {
        Route::get('/', \Modules\Client\Livewire\ClientList::class)->name('clients');
    });

    Route::prefix('invoices')->group(function () {
        Route::get('/', \Modules\Invoice\Livewire\InvoiceList::class)->name('invoices');
    });

    Route::prefix('notes')->group(function () {
        Route::get('/', \Modules\Notes\Livewire\NoteList::class)->name('notes');
    });

    Route::prefix('bookmarks')->group(function () {
        Route::get('/', \Modules\Bookmark\Livewire\BookmarkList::class)->name('bookmarks');
    });

    Route::prefix('quality-control')->group(function () {
        Route::get('/', function () { return view('quality-control.index'); })->name('quality-control');
    });

    Route::prefix('ideas')->group(function () {
        Route::get('/', \Modules\Ideas\Livewire\IdeaList::class)->name('ideas');
    });

    Route::prefix('brain-dump')->group(function () {
        Route::get('/', function () { return view('brain-dump.index'); })->name('brain-dump');
    });

    Route::prefix('savings')->group(function () {
        Route::get('/', \Modules\Savings\Livewire\GoalList::class)->name('savings');
    });

    Route::prefix('subscriptions')->group(function () {
        Route::get('/', \Modules\Subscription\Livewire\SubscriptionList::class)->name('subscriptions');
    });
});