<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\task;
use App\Models\user;

use Illuminate\Database\Eloquent\Factories\Sequence;

class UserController extends Controller
{
    public function testTask(): View
    {
        return view('MyView.task', ['tasks' => Task::Paginate(2), 'fake' => '$task']);
    }
}
