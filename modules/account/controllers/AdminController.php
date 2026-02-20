<?php

namespace app\modules\account\controllers;

use app\modules\account\models\ChangeUserPasswordForm;
use Yii;
use app\modules\account\models\User;
use app\modules\account\models\UserSearch;
use app\modules\account\models\CreateUserForm;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

/**
 * UserController implements the CRUD actions for User model.
 */
class AdminController extends Controller
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
                            'actions' => ['index', 'view-user', 'create-user', 'disable-user', 'enable-user', 'change-user-password'],
                            'allow' => true,
                            'roles' => ['manageUsers'],
                        ],
                    ],
                ],
                'verbs' => [
                    'class' => VerbFilter::class,
                    'actions' => [
                        'disable-user' => ['POST'],
                        'enable-user' => ['POST'],
                    ],
                ],
            ]
        );
    }

    /**
     * Lists all User models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new UserSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single User model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionViewUser($id)
    {
        return $this->render('view-user', [
            'model' => $this->findModel($id),
        ]);
    }


    public function actionCreateUser()
    {
        $model = new CreateUserForm();
        if ($model->load($this->request->post()) && $model->createUser()) {
            Yii::$app->session->setFlash('success', 'L\'utente è stato creato.');
            return $this->redirect(['index']);
        }

        return $this->render('create-user', [
            'model' => $model,
        ]);
    }

    /**
     * Disable an existing User.
     * If disable is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDisableUser($id)
    {
        $model = $this->findModel($id);
        $model->status = User::STATUS_DISABLED;

        if ($model->save()) {
            Yii::$app->session->addFlash('success', 'Utente disattivato.');
        } else {
            Yii::$app->session->addFlash('error', 'Errore: impossibile procedere.');
        }
        return $this->redirect(['admin/view-user', 'id' => $model->id]);
    }

    /**
     * Enable an existing User.
     * If enable is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionEnableUser($id)
    {
        $model = $this->findModel($id);
        $model->status = User::STATUS_ENABLED;

        if ($model->save()) {
            Yii::$app->session->addFlash('success', 'Utente attivato.');
        } else {
            Yii::$app->session->addFlash('error', 'Errore: impossibile procedere.');
        }
        return $this->redirect(['admin/view-user', 'id' => $model->id]);
    }

    public function actionChangeUserPassword($id)
    {
        $userModel = $this->findModel($id);
        $model = new ChangeUserPasswordForm();

        if ($this->request->isPost && $model->load($this->request->post()) && $model->validate()) {

            $userModel->setPassword($model->password);
            if ($userModel->save()) {
                Yii::$app->session->addFlash('success', 'La password dell\'utente è stata cambiata.');
                return $this->redirect(['view-user', 'id' => $userModel->id]);
            }
            Yii::$app->session->addFlash('error', 'Errore: impossibile procedere.');
        }

        return $this->render('change-user-password', [
            'model' => $model,
            'userModel' => $userModel,
        ]);
    }

    /**
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = User::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
