<?php

namespace app\modules\account\models;

use Yii;
use yii\base\Model;

class CreateUserForm extends Model
{
    public $name;
    public $surname;
    public $email;
    public $username;
    public $password;
    public $password_repeat;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['name', 'trim'],
            ['name', 'required'],
            ['name', 'string', 'max' => 255],

            ['surname', 'trim'],
            ['surname', 'required'],
            ['surname', 'string', 'max' => 255],

            ['email', 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'string', 'max' => 255],
            ['email', 'unique', 'targetClass' => 'app\modules\account\models\User', 'message' => 'Questo indirizzo e-mail è già usato da un utente.'],

            ['username', 'trim'],
            ['username', 'required'],
            ['username', 'unique', 'targetClass' => 'app\modules\account\models\User', 'message' => 'Questo username è già stato usato da un altro utente.'],
            ['username', 'string', 'min' => 2, 'max' => 255],

            ['password', 'required'],
            ['password', 'string', 'min' => 5],

            ['password_repeat', 'required'],
            ['password_repeat', 'string', 'min' => 5],

            ['password_repeat', 'compare', 'compareAttribute' => 'password', 'message' => 'Le due password non coincidono.'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'name' => 'Nome',
            'surname' => 'Cognome',
            'email' => 'E-mail',
            'username' => 'Username',
            'password' => 'Password',
            'password_repeat' => 'Riscrivi la password'
        ];
    }

    /**
     * Aggiunge un utente.
     *
     * @return bool whether the creating new account was successful and email was sent
     */
    public function createUser()
    {
        if (!$this->validate()) {
            return false;
        }

        $user = new User();
        $user->name = $this->name;
        $user->surname = $this->surname;
        $user->username = $this->username;
        $user->email = $this->email;
        $user->setPassword($this->password);
        $user->status = $user::STATUS_ENABLED;
        $user->generateAuthKey();
        $user->generateEmailVerificationToken();
        return $user->save();
    }

    /**
     * Sends confirmation email to user
     * @param User $user user model to with email should be send
     * @return bool whether the email was sent
     */
    protected function sendEmail($user)
    {
        return Yii::$app
            ->mailer
            ->compose(
                ['html' => 'emailVerify-html', 'text' => 'emailVerify-text'],
                ['user' => $user]
            )
            ->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name . ' robot'])
            ->setTo($this->email)
            ->setSubject('Account registration at ' . Yii::$app->name)
            ->send();
    }
}
