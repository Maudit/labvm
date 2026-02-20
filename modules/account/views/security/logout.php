<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap4\ActiveForm */

/** @var \app\modules\account\models\LoginForm $model */

use yii\bootstrap5\Html;
use yii\bootstrap5\ActiveForm;

$this->title = 'Logout';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-logout">
    <h1><?= Html::encode($this->title) ?></h1>

    <h1>Grazie <?= $name ?>, hai eseguito il logout.</h1>
    <?= Html::a('Home page', ['/'], ['class' => 'btn btn-primary']) ?>

</div>