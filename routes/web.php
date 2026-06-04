<?php

use Illuminate\Support\Facades\Route;
use App\Models\User;
use App\Http\Controllers\UserController;
use App\Http\Resources\UserResource;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::view('/', 'welcome');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

require __DIR__.'/auth.php';

Route::middleware(['auth', 'verified'])->group(function(){
    Route::view('task','Task')->name('task');
    Route::view('category','category')->name('category');
    Route::view('roles','roles')->name('roles');
    Route::view('MyRoles','MyRoles')->name('myRoles');
    Route::view('assignRoles','assign-roles')->name('assignRoles');
});

Route::get('test', function(){
    return new UserResource(User::find(1));
});

Route::get('testTask', [UserController::class, 'testTask']);