<?php

namespace app\controllers;

use app\models\Experiment;
use app\models\ExperimentProject;
use app\models\ExperimentSearch;
use app\models\Protocol;
use app\models\Project;
use app\models\TestSearch;
use Exception as GlobalException;
use Yii;
use yii\base\InvalidConfigException;
use yii\db\StaleObjectException;
use yii\db\Exception;
use yii\base\InvalidArgumentException;
use yii\base\NotSupportedException;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\web\Response;

/**
 * ExperimentController implements the CRUD actions for Experiment model.
 */
class ExperimentController extends Controller
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
                            'roles' => ['deleteExperiment'],
                        ],
                        [
                            'actions' => ['index', 'view', 'create', 'update', 'assign-tag','delete-tag'],
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
     * Lists all Experiment models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new ExperimentSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Experiment model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id, $tab = '1')
    {
        $model = $this->findModel($id);

        // Validazione manuale di $tab
        if (!in_array($tab, [1, 2, 3])) {
            throw new \yii\web\BadRequestHttpException('Valore di "tab" non valido.');
        }

        $searchModel = new TestSearch();
        $searchModel->experiment_id = $model->id;
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('view', [
            'model' => $model,
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'tab' => $tab,
        ]);
    }

    /**
     * Creates a new Experiment model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new Experiment();
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
            'protocol_list' => Protocol::getList(),
        ]);
    }

    /**
     * Updates an existing Experiment model.
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
            'protocol_list' => Protocol::getList(),
        ]);
    }

    /**
     * Deletes an existing Experiment model.
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
     * Assegna un tag di tipo Progetto all'Esperimento
     * @param mixed $experiment_id 
     * @return Response|string 
     * @throws NotFoundHttpException 
     * @throws InvalidConfigException 
     * @throws StaleObjectException 
     * @throws Exception 
     * @throws InvalidArgumentException 
     * @throws NotSupportedException 
     * @throws GlobalException 
     */

    public function actionAssignTag($experiment_id)
    {
        $experiment = $this->findModel($experiment_id);
        $experimentProjectModel = new ExperimentProject();
        $experimentProjectModel->experiment_id = $experiment->id;

        if ($experimentProjectModel->load(Yii::$app->request->post()) && $experimentProjectModel->save()) {
            Yii::$app->session->setFlash('success', 'Tag assegnato con successo.');
            return $this->redirect(['view', 'id' => $experiment->id, 'tab'=>'3']);
        }

        $assignedTagIds = $experiment->getExperimentProjects()->select('project_id')->column();
        $availableTags = Project::find()->where(['NOT IN', 'id', $assignedTagIds])->all();

        return $this->render('assign-tag', [
            'experiment' => $experiment,
            'experimentProjectModel' => $experimentProjectModel,
            'availableTags' => $availableTags,
        ]);
    }

    public function actionDeleteTag($experiment_id, $project_id)
    {
        $experimentProjectTag = ExperimentProject::findOne(['experiment_id' => $experiment_id, 'project_id' => $project_id]);

        if ($experimentProjectTag === null) {
            throw new NotFoundHttpException('Associazione tag esperimento non trovata.');
        }

        if ($experimentProjectTag->delete()) {
            Yii::$app->session->setFlash('success', 'Tag rimosso con successo.');
        } else {
            Yii::$app->session->setFlash('error', 'Impossibile rimuovere il tag.');
        }

        return $this->redirect(['view', 'id' => $experiment_id, 'tab'=>'3']);
    }

    /**
     * Finds the Experiment model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Experiment the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Experiment::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
