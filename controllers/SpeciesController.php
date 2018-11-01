<?php

namespace app\controllers;

use yii\web\Controller;
use yii\data\Pagination;
use app\models\Species;

class SpeciesController extends Controller
{
    public function actionIndex()
    {
        $query = Species::find();

        $pagination = new Pagination([
            'defaultPageSize' => 20,
            'totalCount' => $query->count(),
        ]);

        $species = $query->orderBy('id_specie')
            ->offset($pagination->offset)
            ->limit($pagination->limit)
            ->all();

        return $this->render('index', [
            'species' => $species,
            'pagination' => $pagination,
        ]);
    }
}