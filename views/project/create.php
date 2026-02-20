<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Project $model */

$this->title = 'Nuovo progetto';
$this->params['breadcrumbs'][] = ['label' => 'Progetti', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <div class="col-12 mb-2">
        <h1 class="display-5"><?= Html::encode($this->title) ?></h1>
        <p class="lead">Compila il modulo per proseguire.</p>
    </div>
</div>
<div class="row">
    <div class="col-12">
        <?= $this->render('_form', [
            'model' => $model,
        ]) ?>

    </div>
</div>