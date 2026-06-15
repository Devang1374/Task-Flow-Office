<?php

use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Carbon;


// Models
use App\Models\User;
use App\Models\customer;
use App\Models\task;

use App\Models\product;

// Controllers
use App\Http\Controllers\UserController;
use App\Http\Controllers\DownloadController;

// resources
use App\Http\Resources\UserResource;
use App\Http\Resources\TaskResource;
use App\Http\Resources\TaskCollection;
use App\Http\Resources\UserCollection;


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
    Route::view('roles','roles')->name('roles');
    Route::view('invoice','invoice')->name('invoice');
    Route::view('MyRoles','MyRoles')->name('myRoles');
    Route::view('weather','weather')->name('weather');
    Route::view('customer','customer')->name('customer');
    Route::view('category','category')->name('category');
    Route::view('assignRoles','assign-roles')->name('assignRoles');

    //download links
    Route::get('download', [DownloadController::class, 'pdf'])->name('pdf');
    Route::get('invoicePdf/{order_id}/{customer_id}', [DownloadController::class, 'invoicePdf'])->name('invoicePdf');
    Route::get('xlsx', [DownloadController::class, 'xlsx'])->name('xlsx');
    Route::get('csv', [DownloadController::class, 'csv'])->name('csv');
});

Route::get('addTask', function(){
    task::factory()->count(10)->state(new Sequence(['isActive' => 1],['isActive' => 0]))->create();
});

use App\Events\CsvCreated;
use App\Jobs\createCsv;
use App\Exports\TaskExport;

use Illuminate\Support\Facades\Cache;

//route
Route::get('test', function(){
    $collection = collect(['devang','yaksh','yash', null, ''])->map(function(?string $name){
        return strtoupper($name);
    })->reject(function(string $name){
        return empty($name);
    })->chunk(1);

    return "$collection";
});

Route::get('testTask', [UserController::class, 'testTask']);