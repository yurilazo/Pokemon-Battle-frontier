<?php

namespace app\models;

use yii\base\Model;

class CodeBattleMove extends Model
{
    public $codebattlemove;

    public function rules()
    {
        return [
            [['codebattlemove'], 'required'],
        ];
    }
}