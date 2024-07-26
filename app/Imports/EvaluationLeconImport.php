<?php

namespace App\Imports;

use App\Models\EvaluationLecon;
use Maatwebsite\Excel\Concerns\ToModel;
use Illuminate\Support\Str;

class EvaluationLeconImport implements ToModel
{
    protected $lecon;
    public function __construct($lecon)
    {
        //
        $this->lecon = $lecon;
    }

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new EvaluationLecon([
            'question'     => $row[0],
            'type'    => $row[1],
            'choix'    => $row[2],
            'reponses'    => $row[3],
            'point'    => $row[4],
            'lecon_id' => $this->lecon->id,
            'slug' => Str::random(10),
        ]);
    }
}
