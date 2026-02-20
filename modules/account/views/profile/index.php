<?php

/** @var yii\web\View $this */

use yii\helpers\Html;

$this->title = 'Profilo';
$this->params['homeLink'] = ['label'=>'Home', 'url'=>'/site/index'];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="row">
    <div class="col-12">
        <h1 class="display-5"><?= Html::encode($this->title) ?></h1>
    </div>
</div>
<div class="row">
    <div class="col-12 mb-2">
        <p class="lead">I dati del tuo profilo.</p>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <dl class="row">
            <dt class="col-2">Nome</dt>
            <dd class="col-9"><?= Html::encode($user->name); ?></dd>

            <dt class="col-2">Cognome</dt>
            <dd class="col-9"><?= Html::encode($user->surname); ?></dd>

            <dt class="col-2">E-mail</dt>
            <dd class="col-9"><?= Html::encode($user->email); ?></dd>
        </dl>
    </div>
</div>