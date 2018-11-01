<?php

namespace app\models;

use yii\base\Model;

class MoveEffect extends Model
{
    public $attacking_pokemon;
    public $id_move;

    public function rules()
    {
        return [
            [['attacking_pokemon', 'id_move'], 'required'],
            ['attacking_pokemon', 'id_move'],
        ];
    }
}