<?php

namespace app\controllers;

use app\models\Compound;
use app\models\CompoundSolutionSearch; //eliminare!
use app\models\CompoundSearch;
use app\models\ResultSearch;
use app\models\Experiment;
use app\models\Test;
use app\models\TestSearch;
use Yii;
use yii\web\UploadedFile;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

/**
 * TestController implements the CRUD actions for Test model.
 */
class TestController extends Controller
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
                            'actions' => ['delete',],
                            'allow' => true,
                            'roles' => ['deleteTest'],
                        ],
                        [
                            'actions' => [
                                'view',
                                'add-multiple',
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

    
    /**
     * Displays a single Test model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        $searchModel = new ResultSearch();
        $searchModel->test_id = $model->id;
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('view', [
            'model' => $model,
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }


    public function actionAddMultiple($experiment_id)
    {
        $experiment = $this->findExperimentModel($experiment_id);
        $searchModel = new CompoundSearch();


        // 1. Recuperiamo gli ID dei composti già associati a questo esperimento
        $excludeIds = Test::find()
            ->select('compound_id')
            ->where(['experiment_id' => $experiment_id])
            ->column();

        $dataProvider = $searchModel->search($this->request->queryParams, $excludeIds);

        // Gestione del salvataggio massivo (POST)
        if ($this->request->isPost) {
            $selection = $this->request->post('selection_list');
            if (!empty($selection)) {
                $ids = explode(',', $selection);
                $transaction = Yii::$app->db->beginTransaction();
                try {
                    foreach ($ids as $cId) {
                        $test = new Test();
                        $test->scenario = Test::SCENARIO_CREATE;
                        $test->experiment_id = $experiment_id;
                        $test->compound_id = $cId;
                        if (!$test->save()) {
                            throw new \Exception("Errore nel salvataggio del Test per Compound ID: $cId");
                        }
                    }
                    $transaction->commit();
                    Yii::$app->session->setFlash('success', "Aggiunti " . count($ids) . " test con successo.");
                    return $this->redirect(['experiment/view', 'id' => $experiment_id]);
                } catch (\Exception $e) {
                    $transaction->rollBack();
                    Yii::$app->session->setFlash('error', $e->getMessage());
                }
            }
        }

        return $this->render('add-multiple', [
            'experiment' => $experiment,
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }


    /**
     * Deletes an existing Test model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $experiment_id = $model->experiment_id;
        $model->delete();

        return $this->redirect(['experiment/view', 'id' => $experiment_id]);
    }

    /**
     * Finds the Test model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Test the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Test::find()->where(['id' => $id])->one()) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * Finds the Test model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Test the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findExperimentModel($id)
    {
        if (($model = Experiment::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
