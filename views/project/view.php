<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var app\models\Project $model */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Progetti', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="row">
    <div class="col-12 mb-2">
        <h1 class="display-5"><?= Html::encode($this->title) ?></h1>
    </div>
</div>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                Dettagli progetto
            </div>
            <div class="card-body">
                <?= DetailView::widget([
                    'model' => $model,
                    'options' => ['class' => 'table table-hover'],
                    'attributes' => [
                        //'id',
                        [
                            'attribute' => 'name',
                            'captionOptions' => ['style' => 'width:200px;'],
                        ],
                        'created_at:datetime',
                        [
                            'attribute' => 'created_by',
                            'value' => $model->createdBy->fullName,
                        ],
                        'updated_at:datetime',
                        [
                            'attribute' => 'updated_by',
                            'value' => $model->updatedBy->fullName,
                        ],
                    ],
                ]) ?>
            </div>
            <div class="card-footer">
                <?= Html::a('Modifica', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
                <?php if (Yii::$app->user->can('deleteProject')): ?>
                    <?= Html::a('Elimina', ['delete', 'id' => $model->id], [
                        'class' => 'btn btn-danger',
                        'data' => [
                            'confirm' => 'Are you sure you want to delete this item?',
                            'method' => 'post',
                        ],
                    ]) ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<!--
<h2>Experiments Collegati</h2>

<?= GridView::widget([
    'dataProvider' => new \yii\data\ActiveDataProvider([
        'query' => $model->getExperiments(), // Utilizzo della relazione via('experimentProjects')
    ]),
    'columns' => [
        ['class' => 'yii\grid\SerialColumn'],

        'id',
        'name',
        [
            'attribute' => 'protocolName',
            'label' => 'Protocollo',
            'value' => function ($model) {
                return $model->protocol->name; // Accesso diretto, sicuro che protocol non sia null
            },
        ],
        [
            'attribute' => 'testCompounds',
            'label' => 'Composti Testati',
            'value' => function ($model) {
                $compoundNames = [];
                foreach ($model->tests as $test) {
                    if ($test->compound) {
                        $compoundNames[] = $test->compound->name;
                    }
                }
                return implode(', ', array_unique($compoundNames)); // Mostra nomi unici separati da virgola
            },
        ],

        [
            'class' => 'yii\grid\ActionColumn',
            'controller' => 'experiment', // Assicurati che sia il controller corretto
            'template' => '{view} {update}', // Esempio di template con meno azioni
        ],
    ],
]) ?>
-->