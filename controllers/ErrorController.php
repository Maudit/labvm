<?php

namespace app\controllers;

use yii\web\Controller;

class ErrorController extends Controller
{
    
    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'view' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }
}
