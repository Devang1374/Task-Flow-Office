<?php

namespace App\Imports;

use App\Models\task;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Collection;

class TaskImport implements ToCollection, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function collection(Collection $rows)
    {
        foreach($rows as $row){
            task::create([
                'user_id' => auth()->user()->id,
                'title' => $row['title'],
                'caption' => $row['caption'],
            ]);
        }
    }
}
