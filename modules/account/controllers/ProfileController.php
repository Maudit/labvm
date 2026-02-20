<?php

namespace app\modules\account\controllers;

use Yii;
use yii\base\InvalidArgumentException;
use yii\web\Controller;
use yii\filters\AccessControl;

class ProfileController extends Controller
{

    /**
     * @inheritDoc
     */
    
     public function behaviors()
     {
         return array_merge(
             parent::behaviors(),
             [
                 'access' => [
                     'class' => AccessControl::class,
                     'rules' => [
                         [
                             'actions' => ['index'],
                             'allow' => true,
                             'roles' => ['@'],
                         ],
                     ],
                 ],
             ]
         );
     }

    public function actionIndex()
    {        
        return $this->render('index',[
            'user'=>Yii::$app->user->identity,
        ]);
    }

}
