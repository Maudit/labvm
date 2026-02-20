<?php

namespace app\controllers;

use app\models\Protocol;
use app\models\ProtocolSearch;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\web\UploadedFile;

/**
 * ProtocolController implements the CRUD actions for Protocol model.
 */
class ProtocolController extends Controller
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
                            'actions' => ['delete'],
                            'allow' => true,
                            'roles' => ['deleteProtocol'],
                        ],
                        [
                            'actions' => ['index','view','create','update','upload','download', 'delete-file'],
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

    /**
     * Lists all Protocol models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new ProtocolSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Protocol model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Protocol model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new Protocol();
        $model->setScenario($model::SCENARIO_CREATE);

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            }
        } 

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Protocol model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->setScenario($model::SCENARIO_UPDATE);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    public function actionUpload($id)
    {
        $model = $this->findModel($id);
        $model->setScenario($model::SCENARIO_UPLOAD_FILE);
        if ($this->request->isPost && $model->load($this->request->post())) {
            $model->file = UploadedFile::getInstance($model, 'file');
            if ($model->file) {
                $model->file_main_directory = date("Y");
                $model->file_sub_directory = "$model->id";
                $model->file_name = "$model->id";
                $model->file_ext = $model->file->extension;

                $transaction = $model->getDb()->beginTransaction();
                try {
                    if ($model->save() && $model->saveFile()) {
                        $transaction->commit();
                        Yii::$app->session->addFlash('success', 'Documento aggiunto con successo!');
                        return $this->redirect(['view', 'id' => $model->id]);
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
        return $this->render('upload', [
            'model' => $model,
        ]);
    }

    /**
     * 
     * @param mixed $id 
     * @return void 
     */
    public function actionDeleteFile($id)
    {
        $model = $this->findModel($id);
        $model->setScenario($model::SCENARIO_DELETE_FILE);

        //ToDO: fare un check sulla presenza dell'allegato.
        
        //memorizza il percorso completo della cartella contenitore per l'immagine.
        $base_path = Yii::getAlias('@app/uploads/protocol/' . $model->file_main_directory . '/' . $model->file_sub_directory);

        //resetta i dati dell'immagine
        $model->file_main_directory = null;
        $model->file_sub_directory = null;
        $model->file_name = null;
        $model->file_ext = null;

        $transaction = $model->getDb()->beginTransaction();
        try {
            if ($model->save() && $model->deleteFileFolder($base_path)) {
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

        return $this->redirect(['view', 'id' => $model->id]);
    }

    public function actionDownload($id)
    {
        $model = $this->findModel($id);
        $base_path = Yii::getAlias('@app/uploads/protocol/' . $model->file_main_directory . '/' . $model->file_sub_directory);
        $file_path = $base_path . '/' . $model->file_name . '.' . $model->file_ext;
        return Yii::$app->response->sendFile($file_path, $model->name);
    }

    /**
     * Deletes an existing Protocol model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Protocol model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Protocol the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Protocol::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
