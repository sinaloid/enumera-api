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

        if($row[0] && ( strtolower(ltrim($row[0])) !=="question" && strtolower(ltrim($row[0])) !=="questions")){
            return new QuestionLecon([
                'question'     => $row[0],
                'type'    => strtoupper($row[1]),
                'choix'    => ltrim($row[2]),
                'reponses'    => ltrim($row[3]),
                'point'    => $row[4],
                'evaluation_lecon_id' => $this->evaluation->id,
                'slug' => Str::random(10),
            ]);
        }

    }
}
