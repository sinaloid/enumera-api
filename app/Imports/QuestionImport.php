<?php

namespace App\Imports;

use App\Models\Question;
use Maatwebsite\Excel\Concerns\ToModel;
use Illuminate\Support\Str;


class QuestionImport implements ToModel
{
    protected $evaluation;
    public function __construct($evaluation)
    {
        //
        $this->evaluation = $evaluation;
    }
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        if($row[0] && $row[0] !=="Question" && $row[1] !=="Type"){
            return new Question([
                'question'     => $row[0],
                'type'    => $row[1],
                'choix'    => $row[2],
                'reponses'    => $row[3],
                'point'    => $row[4],
                'evaluation_id' => $this->evaluation->id,
                'slug' => Str::random(10),
            ]);
        }

    }
}
