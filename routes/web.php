<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');
    
    // Маршрут для страницы шаблонов
    Volt::route('templates', 'templates.template-list')->name('templates');
    Volt::route('organizations', 'organizations.organization-list')->name('organizations');
    Volt::route('audits', 'audits.audit-list')->name('audits');
    Route::get('/marks', App\Livewire\Marks\MarkList::class)->name('marks.index');
});

require __DIR__.'/auth.php';
