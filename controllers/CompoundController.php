<?php

namespace app\controllers;

use app\models\Compound;
use app\models\CompoundSearch;
use app\models\Location;
use app\models\Manufacturer;
use app\models\PhysicalForm;
use Yii;
use yii\web\UploadedFile;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

/**
 * CompoundController implements the CRUD actions for Compound model.
 */
class CompoundController extends Controller
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
                            'roles' => ['deleteCompound'],
                        ],
                        [
                            'actions' => ['index', 'unavailable', 'view', 'create', 'update', 'upload-image', 'delete-image', 'mark-in', 'mark-out', 'derive'],
                            'allow' => true,
                            'roles' => ['@'],
                        ],
                    ],
                ],
                'verbs' => [
                    'class' => VerbFilter::class,
                    'actions' => [
                        'delete' => ['POST'],
                        //'delete-image' => ['POST'], 
                        //'upload-image' => ['POST'],
                    ],
                ],
            ]
        );
    }

    /**
     * Mostra tutti i compound disponibili.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new CompoundSearch();
        $dataProvider = $searchModel->search($this->request->queryParams, [], Compound::STATUS_AVAILABLE);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    // Mostra tutti i compound esauriti.
    public function actionUnavailable()
    {
        $searchModel = new CompoundSearch();
        $dataProvider = $searchModel->search($this->request->queryParams, [], Compound::STATUS_EXHAUSTED);

        return $this->render('index_unavailable', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Compound model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);

        // Se il composto è esaurito, carica la vista specifica
        if ($model->in_stock == Compound::STATUS_EXHAUSTED) {
            return $this->render('view_unavailable', [
                'model' => $model,
            ]);
        }

        // Altrimenti carica la vista standard per i disponibili
        return $this->render('view', [
            'model' => $model,
        ]);
    }

    /**
     * Creates a new Compound model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new Compound();
        $model->setScenario($model::SCENARIO_CREATE);

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
            'location_list' => Location::getList(),
            'manufacturer_list' => Manufacturer::getList(),
            'physical_form_list' => PhysicalForm::getList()
        ]);
    }

    /**
     * Updates an existing Compound model.
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
            'location_list' => Location::getList(),
            'manufacturer_list' => Manufacturer::getList(),
            'physical_form_list' => PhysicalForm::getList()
        ]);
    }

    public function actionUploadImage($id)
    {
        $model = $this->findModel($id);
        $model->setScenario($model::SCENARIO_UPLOAD_IMAGE);
        if ($this->request->isPost && $model->load($this->request->post())) {
            $model->file = UploadedFile::getInstance($model, 'file');
            if ($model->file) {
                $model->file_main_directory = date("Y");
                $model->file_sub_directory = "$model->id";
                $model->file_name = "$model->id";
                $model->file_ext = 'png';

                $transaction = $model->getDb()->beginTransaction();
                try {
                    if ($model->save() && $model->saveImage()) {
                        $transaction->commit();
                        Yii::$app->session->addFlash('success', 'Immagine aggiunta con successo!');
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
        return $this->render('upload-image', [
            'model' => $model,
        ]);
    }

    /**
     * 
     * @param mixed $id 
     * @return void 
     */
    public function actionDeleteImage($id)
    {
        $model = $this->findModel($id);
        $model->setScenario($model::SCENARIO_DELETE_IMAGE);

        // Rifiuta l'esecuzione se il composto non ha un immagine associata valida.
        if (!$model->hasImage()) {
            Yii::$app->session->addFlash('error', 'Errore: Il record non dispone di un\'immagine valida associata.');
            return $this->redirect(['view', 'id' => $model->id]);
        }

        //memorizza il percorso completo della cartella contenitore per l'immagine.
        $base_path = Yii::getAlias('@webroot/img/compounds/' . $model->file_main_directory . '/' . $model->file_sub_directory);

        //resetta i dati dell'immagine
        $model->file_main_directory = null;
        $model->file_sub_directory = null;
        $model->file_name = null;
        $model->file_ext = null;

        $transaction = $model->getDb()->beginTransaction();
        try {
            if ($model->save() && $model->deleteImageFolder($base_path)) {
                $transaction->commit();
                Yii::$app->session->addFlash('success', 'L\'immagine è stata rimossa.');
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

    public function actionMarkIn($id)
    {
        $model = $this->findModel($id);
        $model->setScenario(Compound::SCENARIO_MARK_IN);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            Yii::$app->session->addFlash('success', 'Il composto è stato segnato come disponibile.');
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('mark-in', [
            'model' => $model,
        ]);
    }

    public function actionMarkOut($id)
    {
        $model = $this->findModel($id);
        $model->setScenario($model::SCENARIO_MARK_OUT);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {;
            Yii::$app->session->addFlash('success', 'Il composto è stato segnato come esaurito.');
            return $this->redirect(['/compound/view', 'id' => $model->id]);
        }
        return $this->render('mark-out', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Compound model.
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

    public function actionDerive($id)
    {
        $parent = $this->findModel($id);
        $model = new Compound();
        $model->setScenario($model::SCENARIO_DERIVE);

        if ($this->request->isPost && $model->load($this->request->post())) {
            $model->parent_id = $parent->id;
            $model->name = $parent->name;
            $model->formula = $parent->formula;
            $model->smiles = $parent->smiles;
            $model->manufacturer_id = $parent->manufacturer_id;
            $model->in_stock = true;

            /* 
                Creare una funzione che copia l'immagine del parent e 
                la salva nella cartella dedicata del model.
                al momento il nuovo model viene inizializzato senza 
                immagine.
            */
            //$model->file_main_directory = $parent->file_main_directory;
            //$model->file_sub_directory = $parent->file_sub_directory;
            //$model->file_name = $parent->file_name;
            //$model->file_ext = $parent->file_ext;

            if ($model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        return $this->render('derive', [
            'model' => $model,
            'parent' => $parent,
            'location_list' => Location::getList(),
            'manufacturer_list' => Manufacturer::getList(),
            'physical_form_list' => PhysicalForm::getList()
        ]);
    }


    /**
     * Finds the Compound model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Compound the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Compound::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
