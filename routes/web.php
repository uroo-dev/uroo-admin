<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookmarkController;
use App\Http\Controllers\BrainDumpController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\CredentialController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GitHubController;
use App\Http\Controllers\IdeaController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\SavingsController;
use App\Http\Controllers\QualityControlController;
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

    // Features — index routes render Blade views, CRUD handled by Controllers
    Route::resource('projects', ProjectController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::resource('clients', ClientController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::resource('credentials', CredentialController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::resource('invoices', InvoiceController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::patch('invoices/{invoice}/update-payment', [InvoiceController::class, 'updatePayment'])->name('invoices.update-payment');
    Route::post('invoices/{invoice}/send-wa', [InvoiceController::class, 'sendToWhatsapp'])->name('invoices.send-wa');
    Route::get('invoices/{invoice}/pdf', [InvoiceController::class, 'downloadPdf'])->name('invoices.pdf');
    Route::resource('notes', NoteController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::resource('bookmarks', BookmarkController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::resource('ideas', IdeaController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::resource('brain-dumps', BrainDumpController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::resource('savings', SavingsController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::resource('subscriptions', SubscriptionController::class)->only(['index', 'store', 'update', 'destroy']);

    // Extra routes for features with additional actions
    Route::patch('notes/{note}/toggle-pin', [NoteController::class, 'togglePin'])->name('notes.toggle-pin');
    Route::patch('notes/{note}/toggle-favorite', [NoteController::class, 'toggleFavorite'])->name('notes.toggle-favorite');
    Route::patch('bookmarks/{bookmark}/toggle-favorite', [BookmarkController::class, 'toggleFavorite'])->name('bookmarks.toggle-favorite');
    Route::patch('brain-dumps/{brain_dump}/toggle-archive', [BrainDumpController::class, 'toggleArchive'])->name('brain-dumps.toggle-archive');
    Route::patch('brain-dumps/{brain_dump}/toggle-pin', [BrainDumpController::class, 'togglePin'])->name('brain-dumps.toggle-pin');
    Route::post('savings/{goal}/deposit', [SavingsController::class, 'deposit'])->name('savings.deposit');
    Route::post('savings/{goal}/withdraw', [SavingsController::class, 'withdraw'])->name('savings.withdraw');
    Route::patch('subscriptions/{subscription}/toggle-payment', [SubscriptionController::class, 'togglePayment'])->name('subscriptions.toggle-payment');
    Route::patch('subscriptions/{subscription}/toggle-active', [SubscriptionController::class, 'toggleActive'])->name('subscriptions.toggle-active');

    // GitHub & Quality Control
    Route::get('/github', [GitHubController::class, 'index'])->name('github');
    Route::post('/github/sync', [GitHubController::class, 'sync'])->name('github.sync');
    // Quality Control
    Route::get('/quality-control', [QualityControlController::class, 'index'])->name('quality-control');
    Route::post('/quality-control', [QualityControlController::class, 'store'])->name('quality-control.store');
    Route::patch('/quality-control/{qualityChecklist}', [QualityControlController::class, 'update'])->name('quality-control.update');
    Route::delete('/quality-control/{qualityChecklist}', [QualityControlController::class, 'destroy'])->name('quality-control.destroy');
    Route::patch('/quality-control/checklist-items/{checklistItem}/toggle-checked', [QualityControlController::class, 'toggleChecked'])->name('quality-control.toggle-checked');
});
