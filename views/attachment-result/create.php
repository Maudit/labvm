<?php

use yii\bootstrap5\Html;
use yii\bootstrap5\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\AttachmentResult $model */

$this->title = 'Carica il file risultati';
$this->params['breadcrumbs'][] = ['label' => 'Esperimenti', 'url' => ['/experiment/index']];
$this->params['breadcrumbs'][] = ['label' => $experimentModel->name, 'url' => ['/experiment/view', 'id' => $experimentModel->id]];
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
        <?php $form = ActiveForm::begin(); ?>
        <div class="card">
            <div class="card-header">
                Seleziona
            </div>
            <div class="card-body">
                <div class="row">
                    <?= $form->field($model, 'file', ['options' => ['class' => 'col-12']])->fileInput()->hint('Formati ammessi: doc(x), pdf, xls(x). Dimensione massima: 10MB.'); ?>
                </div>
            </div>
            <div class="card-footer">
                    <?= Html::submitButton('Salva', ['class' => 'btn btn-success']) ?>
            </div>
        </div>
        <?php ActiveForm::end(); ?>
    </div>

</div>
<?php echo $form->errorSummary($model);