<?php

namespace app\modules\account\models;

use Yii;
use yii\base\Model;

class ChangeUserPasswordForm extends Model
{
    
    public $password;
    public $password_repeat;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
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
            'password' => 'Nuova password',
            'password_repeat' => 'Riscrivi la nuova password'
        ];
    }

    /**
     * Aggiunge un utente.
     *
     * @return bool whether the creating new account was successful and email was sent
     */
    public function changeUserPassword()
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
