<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\bootstrap5\ButtonDropdown;
use app\modules\account\models\UserStatus;
use app\modules\account\models\User;

/** @var yii\web\View $this */
/** @var app\modules\user\models\User $model */
$this->title = 'Dettagli utente';
$this->params['homeLink'] = ['label' => 'Home', 'url' => '/site/index'];
$this->params['breadcrumbs'][] = ['label' => 'Utenti', 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->getFullName();
\yii\web\YiiAsset::register($this);
?>
<div class="row">
    <div class="col-12 mb-2">
        <h1 class="display-5"><?= Html::encode($this->title) ?></h1>
        <p class="lead">Visualizza i dati di un singolo utente, cambia la password e il gruppo.</p>
    </div>
</div>

<div class="card">
    <div class="card-header">
    <div class="row">
            <div class="col-8">
                Dettaglio
            </div>
            <div class="col-4 text-end">
                <?php
                echo ButtonDropdown::widget([
                    'label' => '<span class="bi bi-gear-fill"></span> Azioni',
                    'encodeLabel' => false,
                    'buttonOptions' => ['class' => 'btn btn-sm btn-secondary'],
                    'tagName' => 'a',
                    'dropdown' => [
                        'items' => [
                            [
                                'label' => 'Cambia password',
                                'url' => [
                                    'change-user-password',
                                    'id' => $model->id,
                                ],
                                'visible' => true,
                            ],
                            /*
                            [
                                'label' => 'Cambia gruppo',
                                'url' => [
                                    'update',
                                    'id' => $model->id,
                                ],
                                'visible' => true,
                            ],
                            */
                            '-',
                            [
                                'label' => 'Disattiva',
                                'url' => [
                                    'disable-user',
                                    'id' => $model->id,
                                ],
                                'visible' => $model->status == User::STATUS_ENABLED,
                                'linkOptions' => [
                                    'data' => [
                                        'confirm' => 'Confermi di voler disattivare l\'accesso di questo utente?',
                                        'method' => 'post',
                                    ],
                                ],
                            ],
                            [
                                'label' => 'Attiva',
                                'url' => [
                                    'enable-user',
                                    'id' => $model->id,
                                ],
                                'visible' => $model->status == User::STATUS_DISABLED,
                                'linkOptions' => [
                                    'data' => [
                                        'confirm' => 'Confermi di voler attivare l\'accesso di questo utente?',
                                        'method' => 'post',
                                    ],
                                ],
                            ],
                        ],
                        'options' => ['class' => 'dropdown-menu-end'],
                    ],
                ]);
                ?>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-lg-9">
                <?= DetailView::widget([
                    'model' => $model,
                    'options' => ['class' => 'table table-hover'],
                    'attributes' => [
                        'name',
                        'surname',
                        'username',
                        'email:email',
                        [
                            'format' => 'raw',
                            'attribute' => 'status',
                            'value' => UserStatus::getBadge($model->status, false),
                        ],
                    ],
                ]) ?>
            </div>
            <div class="col-lg-3">
            <div class="card">
                    <div class="card-body">
                        <?= DetailView::widget([
                            'model' => $model,
                            'options' => ['class' => 'table table-hover'],
                            'attributes' => [
                                [
                                    'attribute' => 'created_at',
                                    'format' => 'datetime',
                                    'captionOptions' => ['class' => 'fs-7'],
                                    'contentOptions' => ['class' => 'fs-7'],
                                ],
                                [
                                    'attribute' => 'updated_at',
                                    'format' => 'datetime',
                                    'captionOptions' => ['class' => 'fs-7'],
                                    'contentOptions' => ['class' => 'fs-7'],
                                ],
                            ],
                        ]) ?>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

