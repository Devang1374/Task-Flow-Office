<?php

use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Carbon;

// Models
use App\Models\User;
use App\Models\customer;
use App\Models\Task;

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
    Route::view('MyRoles','MyRoles')->name('myRoles');
    Route::view('customer','customer')->name('customer');
    Route::view('invoice','invoice')->name('invoice');
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

Route::get('test', function(){
    auth()->user()->invoice()->create([
            'customer_id' => 2,
            'invoice_number' => "INV-asdfasdf",
            'order_number' => 2,
            'due_date' => Carbon::now(),
            'company_name' => "taskflow",
            'company_email' => "taskflow@",
            'company_number' => "12121212",
            'company_address' => "sardanagar",
            'terms' => "dfdfdf",
        ]);

        return "update";
});

Route::get('testTask', [UserController::class, 'testTask']);