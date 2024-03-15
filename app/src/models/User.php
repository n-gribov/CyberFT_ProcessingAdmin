<?php

namespace app\models;

use app\helpers\UserHelper;
use Yii;
use yii\web\IdentityInterface;

class User extends ActiveRecord implements IdentityInterface
{
    public $id;
    public $username;
    public $password;
    public $authKey;
    public $accessToken;
    private $_role;
    public $re_password;

    const SCENARIO_CREATE = 'create';
    const SCENARIO_UPDATE = 'update';
    const ROLE_REGULAR_USER = '60';
    const TITLE = '';

    public static function tableName()
    {
        return 'cyberft.v_users';
    }

    public function getRole()
    {
        return array_column($this->executeGetRoles(), 'role_id');
    }

    public function setRole($value)
    {
        $this->_role = $value;
        $this->_role[] = self::ROLE_REGULAR_USER;
    }

    public static function primaryKey()
    {
        return ['user_id'];
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_CREATE] = ['role', 'password', 're_password', 'user_code', 'user_name', 'send_mail', 'e_mail', 'comm'];
        $scenarios[self::SCENARIO_UPDATE] = ['role', 'user_id', 'password', 're_password', 'user_name', 'comm', 'account_status', 'send_mail', 'e_mail'];
        return $scenarios;
    }

    public function attributeLabels()
    {
        return [
            'user_id' => Yii::t('app/user', 'ID'),
            'user_code' => Yii::t('app/user', 'User code'),
            'status' => Yii::t('app/user', 'Status'),
            'user_name' => Yii::t('app/user', 'User name'),
            'comm' => Yii::t('app/user', 'Comment'),
            'i_date' => Yii::t('app/user', 'Institution date'),
            'd_date' => Yii::t('app/user', 'Date of deletion'),
            'i_user' => Yii::t('app/user', 'User creator'),
            'd_user' => Yii::t('app/user', 'Initiator of deletion'),
            'send_mail' => Yii::t('app/user', 'Need to send messages'),
            'e_mail' => Yii::t('app/user', 'Email'),
            'account_status' => Yii::t('app/user', 'Lock status'),
            'role' => Yii::t('app/user', 'Role'),
            'password' => Yii::t('app/user', 'Password'),
            're_password' => Yii::t('app/user', 'Repeat password'),
        ];
    }

    public function rules()
    {
        return [
            [[
                'password', 're_password', 'user_code', 'user_name', 'send_mail', 'e_mail'],
                'required', 'on' => self::SCENARIO_CREATE
            ],
            [[
                'user_id', 'user_name', 'account_status', 'send_mail', 'e_mail'],
                'required', 'on' => self::SCENARIO_UPDATE
            ],
            ['password', 'validateNewPassword'],
            ['password', 'default', 'value' => null],
            ['e_mail', 'email'],
            [['send_mail', 'account_status'], 'boolean'],
            ['comm', 'safe'],
            ['password', 'safe', 'on' => self::SCENARIO_UPDATE]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentity($id)
    {
        $cachedData = UserHelper::getCachedIdentityData();

        if (is_null($cachedData)) {
            return null;
        }

        if (isset($cachedData['id']) && $cachedData['id'] == $id) {
            return new static($cachedData);
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return null;
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        $cachedData = UserHelper::getCachedIdentityData();

        if (is_null($cachedData)) {
            return null;
        }

        if (isset($cachedData['username']) && $cachedData['username'] == $username) {
            return new static($cachedData);
        }

        return null;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getAuthKey()
    {
        return $this->authKey;
    }

    public function validateAuthKey($authKey)
    {
        return $this->authKey === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return $this->password === $password;
    }

    public function getParams()
    {
        return $this->hasMany(UserParam::class, ['user_id' => 'user_id']);
    }

    protected function executeInsert()
    {
        $query = '
          select
            piiserrorout as "hasError",
            pcerrcodeout as "errorCode",
            pcerrmsgout as "errorMessage",
            piuserout as "id"
          from cyberft.p_user_api_add(
            :pccode,
            :pcpasswd,
            :pcsname,
            :pclname,
            :pirole,
            :pisendmail,
            :pcemail
          )
        ';

        $result = Yii::$app->db
            ->createCommand(
                $query,
                [
                    ':pccode'     => $this->user_code,
                    ':pcpasswd'   => $this->password,
                    ':pcsname'    => $this->user_name,
                    ':pclname'    => $this->comm,
                    ':pirole'     => $this->_role[0],
                    ':pisendmail' => $this->send_mail,
                    ':pcemail'    => $this->e_mail,
                ]
            )
            ->queryOne();

        if (($result['hasError'] != true)&&(count($this->_role) > 1)) {
            $this->user_id = $result['id'];
            foreach ($this->_role as $role) {
                if (!$this->checkRole($role)) {
                    $roleResult = $this->executeAddRole($role);
                    if ((is_array($roleResult)) && ($roleResult['hasError'] == true)) {
                        return $this->processInsertQueryResult($roleResult);
                    }
                }
            }
        }

        return $this->processInsertQueryResult($result);
    }

    protected function executeUpdate()
    {
        $query = '
          select
            piiserrorout as "hasError",
            pcerrcodeout as "errorCode",
            pcerrmsgout as "errorMessage"
          from cyberft.p_user_api_edit(
            :piuser,
            :pcpasswd,
            :pcsname,
            :pclname,
            :pilock,
            :pisendmail,
            :pcemail
          )
        ';

        $result = Yii::$app->db
            ->createCommand(
                $query,
                [
                    ':piuser'     => $this->user_id,
                    ':pcpasswd'   => $this->password,
                    ':pcsname'    => $this->user_name,
                    ':pclname'    => $this->comm,
                    ':pilock'     => $this->account_status ? 0 : 1,
                    ':pisendmail' => $this->send_mail,
                    ':pcemail'    => $this->e_mail,
                ]
            )
            ->queryOne();

        if ($result['hasError'] != true) {
            $resultUpdateRole = $this->executeUpdateRoles($this->_role);
            if (is_array($resultUpdateRole) && $resultUpdateRole['hasError'] == true) {
               return $this->processUpdateQueryResult($resultUpdateRole);
            }
        }

        return $this->processUpdateQueryResult($result);
    }

    protected function executeDelete()
    {
        $query = '
          select
            piiserrorout as "hasError",
            pcerrcodeout as "errorCode",
            pcerrmsgout as "errorMessage"
          from cyberft.p_user_api_del(
            :piuser
          )
        ';

        $result = Yii::$app->db
            ->createCommand($query, [':piuser' => $this->user_id])
            ->queryOne();

        return $this->processDeleteQueryResult($result);
    }

    protected function executeAddRole($role)
    {
        $query = '
          select
            piiserrorout as "hasError",
            pcerrcodeout as "errorCode",
            pcerrmsgout as "errorMessage"
          from cyberft.p_user_api_add_role(
            :piuser,
            :pirole
          )
        ';

        $result = Yii::$app->db
            ->createCommand($query, [':piuser' => $this->user_id, ':pirole' => $role])
            ->queryOne();

        return $result;
    }

    protected function executeDeleteRole($role)
    {
        $query = '
          select
            piiserrorout as "hasError",
            pcerrcodeout as "errorCode",
            pcerrmsgout as "errorMessage"
          from cyberft.p_user_api_del_role(
            :piuser,
            :pirole
          )
        ';

        $result = Yii::$app->db
            ->createCommand($query, [':piuser' => $this->user_id, ':pirole' => $role])
            ->queryOne();

        return $result;
    }

    protected function executeUpdateRoles($newRoles)
    {
        $oldRoles = array_column($this->executeGetRoles(), 'role_id');

        $addRoles = array_diff($newRoles, $oldRoles);
        $delRoles = array_diff($oldRoles, $newRoles);

        $results = [];
        if ($addRoles == true) {
            $this->setRole($addRoles);
            foreach ($this->_role as $role) {
                if (!$this->checkRole($role)) {
                    $results[] = $this->executeAddRole($role);
                }
            }
        }

        if ($delRoles == true) {
            foreach ($delRoles as $role) {
                if ($this->checkRole($role)) {
                    $results[] = $this->executeDeleteRole($role);
                }
            }
        }

        foreach ($results as $result) {
            if (is_array($result) && $result['hasError'] == true) {
                return $result;
            }
        }

        return true;
    }

    public function executeGetRoles()
    {
        $query = '
          select
            role_id as "role_id",
            role_code as "role_code",
            role_name as "role_name",
            comm as "comm"
          from cyberft.p_user_api_get_user_roles(
            :pcUser
          )
        ';

        $result = Yii::$app->db
            ->createCommand($query, [':pcUser' => $this->user_code])
            ->queryAll();

        $result = UserHelper::translateRoleName($result);

        return $result;
    }

    protected function checkRole($role)
    {
        $roles = array_column($this->executeGetRoles(), 'role_id');
        return in_array($role, $roles);
    }

    public function validateNewPassword($attribute)
    {
        if ($this->password != $this->re_password) {
            $this->addError($attribute, Yii::t('app/user', 'Password and re-password do not match'));
        }
    }
}
