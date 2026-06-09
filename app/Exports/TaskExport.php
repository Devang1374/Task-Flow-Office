<?php

namespace App\Exports;

use App\Models\task;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;

class TaskExport implements FromQuery, WithHeadings
{
    protected $userId;
    public function __construct($userId){
        $this->userId = $userId;
    }


    /**
    * @return \Illuminate\Support\Collection
    */
    public function query()
    {
        return task::query()->select('title', 'caption','isActive')->where('user_id', $this->userId);
    }

    public function headings(): array
    {
        return [
            'title',
            'caption',
            'isActive'
        ];
    }
}
