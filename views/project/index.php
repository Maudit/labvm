<?php

use app\models\Project;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var app\models\ProjectSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = Yii::t('app', 'Progetti');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <div class="col-12 mb-2">
        <h1 class="display-5">Progetti</h1>
    </div>
</div>

<div class="row">
    <div class="col-10 mb-2">
        <p class="lead">Elenco dei progetti</p>
    </div>
    <div class="col-2 mb-2 d-flex align-items-end justify-content-end">
        <?= Html::a('Nuovo progetto', ['create'], ['class' => 'btn btn-primary']) ?>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <?php Pjax::begin(); ?>
        <?php // echo $this->render('_search', ['model' => $searchModel]); 
        ?>

        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'layout' => "{items}\n{pager}",
            'tableOptions' => ['class' => 'table table-hover'],
            'pager' => [
                'nextPageLabel' => 'Next <span aria-hidden="true">&raquo;</span>',
                'prevPageLabel' => '<span aria-hidden="true">&laquo;</span> Prev.',
                //'firstPageLabel'=>'Inizio',
                //'lastPageLabel'=>'Fine',
                'listOptions' => ['class' => 'pagination justify-content-center']
            ],
            'columns' => [
                //['class' => 'yii\grid\SerialColumn'],

                //'id',
                [
                    'attribute' => 'name',
                    'filter' => Html::tag('div', Html::activeTextInput($searchModel, 'name', ['class' => 'form-control', 'placeholder' => 'Cerca...']) . Html::button('<i class="bi bi-search"></i>', ['class' => 'btn input-group-btn']), ['class' => 'input-group']),
                ],
                [
                    'class' => ActionColumn::class,
                    'options' => ['style' => 'width:1%;'],
                    'contentOptions' => ['class' => 'text-end'],
                    'template' => '{view}',
                    'buttons' => [
                        'view' => function ($url, $model, $key) {
                            return Html::a('<span class="d-none d-sm-inline">Dettagli</span>', $url, ['class' => 'btn btn-secondary text-nowrap', 'data-pjax' => '0', 'title' => 'Dettagli', 'aria-label' => 'Dettagli',]);
                        },
                    ],
                    'urlCreator' => function ($action, Project $model, $key, $index, $column) {
                        return Url::toRoute([$action, 'id' => $model->id]);
                    }
                ],
            ],
        ]); ?>

        <?php Pjax::end(); ?>
    </div>
</div>