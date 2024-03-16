<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect()->route('clients.index');
});
Route::group(['prefix' => 'clients'], function () {
    Route::get('/', [App\Http\Controllers\ClientController::class, 'index'])->name('clients.index');
    Route::get('/create', [App\Http\Controllers\ClientController::class, 'create'])->name('clients.create');
    Route::post('/', [App\Http\Controllers\ClientController::class, 'store'])->name('clients.store');
    Route::put('/{client}', [App\Http\Controllers\ClientController::class, 'update'])->name('clients.update');

    Route::delete('/{client}', [App\Http\Controllers\ClientController::class, 'destroy'])->name('clients.destroy');
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
