<?php

namespace app\controllers;

use Yii;
use app\models\Experiment;
use app\models\AttachmentAnalysis;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;

class AttachmentAnalysisController extends Controller
{
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'access' => [
                    'class' => AccessControl::class,
                    'rules' => [
                        [
                            'actions' => [
                                'create',
                                'download',
                                'delete',
                            ],
                            'allow' => true,
                            'roles' => ['@'],
                        ],
                    ],
                ],
                'verbs' => [
                    'class' => VerbFilter::class,
                    'actions' => [
                        'delete' => ['POST'],
                    ],
                ],
            ]
        );
    }

    public function actionCreate($experiment_id)
    {
        $model = new AttachmentAnalysis();
        $experimentModel = $this->findExperimentModel($experiment_id);

        if ($this->request->isPost && $model->load($this->request->post())) {
            $model->file = UploadedFile::getInstance($model, 'file');
            if ($model->file) {
                $model->experiment_id = $experimentModel->id;
                $model->file_main_directory = date("Y");
                $model->file_sub_directory = "$model->experiment_id";
                $model->file_name = "$model->experiment_id-analysis";
                $model->file_ext = $model->file->extension;

                $transaction = $model->getDb()->beginTransaction();
                try {
                    if ($model->save() && $model->saveFile()) {
                        $transaction->commit();
                        Yii::$app->session->addFlash('success', 'Documento aggiunto con successo!');
                        return $this->redirect(['experiment/view', 'id' => $model->experiment_id, 'tab'=>'2']);
                    } else {
                        Yii::$app->session->addFlash('error', 'Creazione record fallita. Controlla eventali errori e riprova.');
                        $transaction->rollBack();
                    }
                } catch (\Throwable $t) {
                    $transaction->rollBack();
                    throw $t;
                }
            }
        }

        return $this->render('create', [
            'model' => $model,
            'experimentModel' => $experimentModel,
        ]);
    }

    public function actionDownload($experiment_id)
    {
        $model = $this->findModel($experiment_id);
        return Yii::$app->response->sendFile($model->getFullFilePath(), $model->generateFileName());
    }

    /**
     * Deletes an existing AttachmentGraph model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $experiment_id Experiment ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($experiment_id)
    {
        $model = $this->findModel($experiment_id);

        $transaction = $model->getDb()->beginTransaction();

        try {
            if ($model->delete() && $model->deleteFile()) {
                $transaction->commit();
                Yii::$app->session->addFlash('success', 'Il documento è stato rimosso.');
            } else {
                $transaction->rollBack();
                Yii::$app->session->addFlash('error', 'C\'è un problema: non è stato possibile rimuovere l\'immagine. Riprova o contatta l\'assistenza tecnica.');
            }
        } catch (\Throwable $t) {
            $transaction->rollBack();
            throw $t;
        }

        return $this->redirect(['experiment/view', 'id' => $experiment_id, 'tab'=>'2']);
    }

    /**
     * Finds the AttachmentGraph model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $experiment_id Experiment ID
     * @return AttachmentGraph the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($experiment_id)
    {
        if (($model = AttachmentAnalysis::findOne(['experiment_id' => $experiment_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }


    /**
     * Finds the Test model based on its primary key value.n
     * @return Test the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findExperimentModel($id)
    {
        if (($model = Experiment::find()->where(['id' => $id])->one()) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
