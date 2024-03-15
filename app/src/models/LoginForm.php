<?php

namespace app\models;

use app\helpers\UserHelper;
use Yii;
use yii\base\Model;

class LoginForm extends Model
{
    public $id;
    public $username;
    public $password;
    public $rememberMe = true;

    public function rules()
    {
        return [
            [[ 'id', 'username', 'password'], 'required'],
            ['rememberMe', 'boolean']
        ];
    }

    public function attributeLabels()
    {
        return [
            'username' => Yii::t('app', 'Username'),
            'password' => Yii::t('app', 'Password')
        ];
    }

    public function login()
    {
        $this->id = UserHelper::getRemoteId($this->username, $this->password);

        if ($this->validate()) {
            $identityData = UserHelper::makeIdentityData($this->id, $this->username);
            $user = new User($identityData);

            if (Yii::$app->user->login($user, $this->rememberMe ? 3600*24*30 : 0)) {
                $identityData = UserHelper::addPasswordToIdentityData($this->password, $identityData);
                UserHelper::cacheIdentityData($identityData);
                return true;
            }
        }

        return false;
    }
}
