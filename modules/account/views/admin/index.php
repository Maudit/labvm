<?php

use app\modules\account\models\User;
use app\modules\account\models\UserStatus;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\widgets\Pjax;
use kartik\daterange\DateRangePicker;

/** @var yii\web\View $this */
/** @var app\modules\account\models\UserSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Gestione utenti';
$this->params['homeLink'] = ['label'=>'Home', 'url'=>'/site/index'];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <div class="col-12">
        <h1 class="display-5"><?= Html::encode($this->title) ?></h1>
    </div>
</div>
<div class="row">
    <div class="col-10 mb-2">
        <p class="lead">Visualizza, aggiungi, modifica e disattiva gli utenti.</p>
    </div>
    <div class="col-2 mb-2 d-flex align-items-end justify-content-end">
        <?= Html::a('Nuovo utente', ['create-user'], ['class' => 'btn btn-primary']) ?>
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
                'listOptions' => ['class' => 'pagination pagination-sm justify-content-center']
            ],
            'columns' => [
                [
                    'attribute' => 'status',
                    // Genera un tag <colgroup><col style="">:
                    'options' => ['style' => 'width:130px;'],
                    'filter' => Html::activeDropDownList($searchModel, 'status', UserStatus::getList(), ['class' => 'form-select', 'prompt' => 'Tutti']),
                    'content' => function ($user, $key, $index, $column) {
                        return UserStatus::getBadge($user->status);
                    },
                ],

                [
                    'attribute' => 'name',
                    'filter' => Html::tag('div', Html::activeTextInput($searchModel, 'name', ['class' => 'form-control', 'placeholder' => 'Search...']) . Html::button('<i class="bi bi-search"></i>', ['class' => 'btn input-group-btn']), ['class' => 'input-group']),
                ],
                [
                    'attribute' => 'surname',
                    'filter' => Html::tag('div', Html::activeTextInput($searchModel, 'surname', ['class' => 'form-control', 'placeholder' => 'Search...']) . Html::button('<i class="bi bi-search"></i>', ['class' => 'btn input-group-btn']), ['class' => 'input-group']),
                ],
                [
                    'attribute' => 'email',
                    'format' => 'email',
                    'filter' => Html::tag('div', Html::activeTextInput($searchModel, 'email', ['class' => 'form-control', 'placeholder' => 'Search...']) . Html::button('<i class="bi bi-search"></i>', ['class' => 'btn input-group-btn']), ['class' => 'input-group']),

                ],

                [
                    'attribute' => 'created_at',
                    'format' => 'date',
                    'options' => ['style' => 'width:180px;'],
                    'filter' => DateRangePicker::widget([
                        'model'=>$searchModel,
                        'hideInput'=>true,
                        'attribute'=>'createTimeRange',
                        'presetDropdown'=>true,
                        'includeMonthsFilter'=>false,
                        'pluginOptions' => ['locale' => ['format' => 'DD-MM-YYYY']],
                        'options' => ['placeholder' => 'Seleziona'],
                        'pickerIcon'=>'<i class="bi bi-calendar-check"></i>',
                        'containerTemplate' => '<div class="input-group kv-drp-dropdown">
                        <span class="input-group-text kv-calendar">{pickerIcon}</span>
                        <input type="text" readonly class="form-control" value="{value}" aria-label="Intervallo di date">
                        <span class="input-group-text kv-clear" title="Pulisci">&nbsp;<i class="bi bi-x"></i>&nbsp;</span>
                      </div>
                    {input}',
                    ])
                ],
                [
                    'class' => ActionColumn::className(),
                    'header' => 'Azioni',
                    'template' => '{view-user}',
                    'buttons' => [
                        'view-user' => function ($url, $model, $key) {
                            return Html::a('<span class="d-none d-sm-inline">Dettagli</span>', $url, ['class' => 'btn btn-secondary text-nowrap', 'data-pjax' => '0', 'title' => 'Dettagli', 'aria-label' => 'Dettagli',]);
                        },
                    ],
                    'urlCreator' => function ($action, User $model, $key, $index, $column) {
                        return Url::toRoute([$action, 'id' => $model->id]);
                    }
                ],
            ],
        ]); ?>

        <?php Pjax::end(); ?>
    </div>
</div>
