<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\ArticlesController;
use App\Http\Controllers\WarPostShowController;
use App\Http\Controllers\WarPostsIndexController;
use App\Http\Controllers\Admin\VisitStatsController;
use App\Livewire\Admin\WarPosts\Editor;
use App\Livewire\Admin\WarPosts\Index;
use App\Livewire\Admin\Cities\Index as CityIndex;
use App\Livewire\Admin\Regions\Index as RegionIndex;
use App\Livewire\Admin\WestNews\Index as WestNewsIndex;
use App\Livewire\Frontline\CitiesBoard;
use App\Livewire\Frontline\RegionsBoard;
use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use App\Livewire\Settings\TwoFactor;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;

Route::middleware(\App\Http\Middleware\LogVisitorCountry::class)
    ->group(function () {
Route::get('/', HomeController::class)->name('home');
Route::get('/articles', ArticlesController::class)->name('articles.index');
Route::get('/war-posts', WarPostsIndexController::class)->name('war-posts.index');

Route::view('dashboard', 'dashboard')
    ->name('dashboard');

Route::get('/war-posts/{warPost}', WarPostShowController::class)
    ->name('war-posts.show');

Route::get('/cities-control', CitiesBoard::class)
    ->name('cities.index');

Route::get('/regions-control', RegionsBoard::class)
    ->name('regions.index');

Route::get('/frontline-control', function () {
    return redirect()->route('cities.index');
})->name('frontline.control');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');
    Route::get('/admin/cities', CityIndex::class)->name('admin.cities.index');
    Route::get('/admin/regions', RegionIndex::class)->name('admin.regions.index');
    Route::get('/admin/war-posts', Index::class)->name('admin.war-posts.index');
    Route::get('/admin/war-posts/create', Editor::class)->name('admin.war-posts.create');

    // Edit existing post
    Route::get('/admin/war-posts/{postId}/edit', Editor::class)->name('admin.war-posts.edit');
    Route::get('/admin/west-news', WestNewsIndex::class)->name('admin.west-news.index');
    Route::get('/admin/visits', VisitStatsController::class)->name('admin.visits.index');
    Route::get('settings/profile', Profile::class)->name('profile.edit');
    Route::get('settings/password', Password::class)->name('user-password.edit');
    Route::get('settings/appearance', Appearance::class)->name('appearance.edit');

    Route::get('settings/two-factor', TwoFactor::class)
        ->middleware(
            when(
                Features::canManageTwoFactorAuthentication()
                    && Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword'),
                ['password.confirm'],
                [],
            ),
        )
        ->name('two-factor.show');
});
    });
