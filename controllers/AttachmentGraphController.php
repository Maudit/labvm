<?php

namespace app\controllers;

use Yii;
use app\models\Test;
use app\models\AttachmentGraph;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;

class AttachmentGraphController extends Controller
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

    public function actionCreate($test_id)
    {
        $model = new AttachmentGraph();
        $testModel = $this->findTestModel($test_id);

        if ($this->request->isPost && $model->load($this->request->post())) {
            $model->file = UploadedFile::getInstance($model, 'file');
            if ($model->file) {
                $model->test_id = $testModel->id;
                $model->file_main_directory = date("Y");
                $model->file_sub_directory = "$model->test_id";
                $model->file_name = "$model->test_id-graph";
                $model->file_ext = $model->file->extension;

                $transaction = $model->getDb()->beginTransaction();
                try {
                    if ($model->save() && $model->saveFile()) {
                        $transaction->commit();
                        Yii::$app->session->addFlash('success', 'Documento aggiunto con successo!');
                        return $this->redirect(['test/view', 'id' => $model->test_id]);
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
            'testModel' => $testModel,
        ]);
    }

    public function actionDownload($test_id)
    {
        $model = $this->findModel($test_id);
        //$base_path = Yii::getAlias('@app/uploads/attachment-graph/' . $model->file_main_directory . '/' . $model->file_sub_directory);
        //$file_path = $base_path . '/' . $model->file_name . '.' . $model->file_ext;
        return Yii::$app->response->sendFile($model->getFullFilePath(), $model->generateFileName());
    }

    /**
     * Deletes an existing AttachmentGraph model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $test_id Test ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($test_id)
    {
        $model = $this->findModel($test_id);

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

        return $this->redirect(['test/view', 'id' => $test_id]);
    }

    /**
     * Finds the AttachmentGraph model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $test_id Test ID
     * @return AttachmentGraph the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($test_id)
    {
        if (($model = AttachmentGraph::findOne(['test_id' => $test_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }


    /**
     * Finds the Test model based on its primary key value.n
     * @return Test the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findTestModel($id)
    {
        if (($model = Test::find()->where(['id' => $id])->one()) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
