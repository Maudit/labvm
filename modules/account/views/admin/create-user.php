<?php

/** @var yii\web\View $this */
/** @var yii\bootstrap5\ActiveForm $form */
/** @var app\modules\account\models\CreateUserForm $model */

use yii\bootstrap5\Html;
use yii\bootstrap5\ActiveForm;

$this->title = 'Nuovo utente';
$this->params['homeLink'] = ['label' => 'Home', 'url' => '/site/index'];
$this->params['breadcrumbs'][] = ['label' => 'Utenti', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <div class="col-12 mb-2">
        <h1 class="display-5"><?= Html::encode($this->title) ?></h1>
        <p class="lead">Compila il modulo per proseguire.</p>
    </div>
</div>
<div class="row">
    <div class="col-lg-8 col-md-12 col-sm-12">
        <?php $form = ActiveForm::begin(['id' => 'form-signup', 'options' => ['autocomplete' => 'new-password']]); ?>
        <div class="card">
            <div class="card-header">Dati utente</div>
            <div class="card-body">
                <div class="row">
                    <div class="col-sm-12 col-md-6">
                        <?= $form->field($model, 'name')->textInput(['autofocus' => true]) ?>
                    </div>
                    <div class="col-sm-12 col-md-6">
                        <?= $form->field($model, 'surname')->textInput() ?>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-sm-12 col-md-12">
                        <?= $form->field($model, 'username')->textInput(['autocomplete' => 'new-password']) ?>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-12 col-md-6">
                        <?= $form->field($model, 'password')->passwordInput(['autocomplete' => 'new-password']) ?>
                    </div>
                    <div class="col-sm-12 col-md-6">
                        <?= $form->field($model, 'password_repeat')->passwordInput(['autocomplete' => 'new-password']) ?>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-sm-12 col-md-12">
                        <?= $form->field($model, 'email') ?>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <?= Html::submitButton('Salva', ['class' => 'btn btn-primary', 'name' => 'signup-button']) ?>
            </div>
        </div>
        <?php ActiveForm::end(); ?>

    </div>
</div>