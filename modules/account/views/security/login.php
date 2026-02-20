<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap4\ActiveForm */

/** @var \app\modules\account\models\LoginForm $model */

use yii\bootstrap5\Html;
use yii\bootstrap5\ActiveForm;
use app\widgets\Alert;

$this->title = 'Login';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <div class="col-4"></div>
    <div class="col-4">
        <div class="card shadow border-0" style="background-color: rgba(255,255,255,0.95);">
            <img src="https://www.3tre3.it/3tres3_common/art/it/10042/covid19_160122.jpg?w=820&q=1&ts=1653979446" class="card-img-top">
            <div class="card-body">

                <h3 class="card-title text-center text-primary">Lab<strong>VM</strong></h5>
                    <h5 class="card-title text-center text-dark">Accedi</h5>

                    <?php $form = ActiveForm::begin(['id' => 'login-form']); ?>

                    <?= $form->field($model, 'username')->label(false)->textInput(['class' => 'form-control', 'placeholder' => 'Username']) ?>

                    <?= $form->field($model, 'password')->label(false)->passwordInput(['class' => 'form-control', 'placeholder' => 'Password']) ?>
                    
                    <div class="row text-dark">
                        <div class="col center"><?= $form->field($model, 'rememberMe')->checkbox(); ?></div>
                        <div class="col text-end"><a href="#">Password dimenticata?</a></div>
                    </div>
                    <?= Html::submitButton('Login', ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
                    <?php ActiveForm::end(); ?>
                    <hr>
                    <div class="text-dark mt-2">
                        <span class="fw-bold">Versione DEMO</span>
                        <p>
                            fabrizia.toscani : fabrizia.toscani<br>
                            rosaura.mazzanti : rosaura.mazzanti<br>
                            davide.schiavoni : davide.schiavoni
                        </p>
                    </div>
            </div>

        </div>
    </div>
    <div class="col-4"></div>
</div>