<?php

use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;
use Livewire\Volt\Volt;
use App\Http\Controllers\TaskReportController;

Route::get('/', fn () => view('home'))->name('home');
Route::get('/profil-desa', fn () => view('profile'))->name('profile');
Route::get('/berita-acara', fn () => view('news'))->name('news');
Route::get('/berita-desa/{slug}', fn ($slug) => view('news-detail', ['slug' => $slug]))->name('news.detail');

Route::get('/terms-conditions', function () {
    return view('terms-conditions');
})->name('terms-conditions');

Route::middleware(['auth'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');

    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('profile.edit');
    Volt::route('settings/password', 'settings.password')->name('user-password.edit');
    // Volt::route('settings/appearance', 'settings.appearance')->name('appearance.edit');

    Volt::route('settings/two-factor', 'settings.two-factor')
        ->middleware(
            when(
                Features::canManageTwoFactorAuthentication()
                && Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword'),
                ['password.confirm'],
                [],
            ),
        )
        ->name('two-factor.show');

    Volt::route('projects', 'project.index')->name('projects');
    Volt::route('projects/{id}/tasks', 'project-task.index')->name('projects.tasks');
    Volt::route('projects/{id}/tasks/{taskId}/targets', 'project-task.target')->name('projects.tasks.targets');

    Volt::route('news-manage', 'news.index')->name('news.manage');
    Volt::route('complaints', 'complaint.index')->name('complaints');

    // PDF Report Routes
    Route::get('tasks/{taskId}/report/pdf', [TaskReportController::class, 'generateTaskReport'])
        ->name('tasks.report.pdf');
    Route::get('programs/{programId}/tasks/report/pdf', [TaskReportController::class, 'generateProgramTasksReport'])
        ->name('programs.tasks.report.pdf');

    Route::middleware(['role:admin|operator'])->group(function () {
        Volt::route('users', 'user.index')->name('users');
    });

    Volt::route('residents', 'resident.index')->name('residents');
});
