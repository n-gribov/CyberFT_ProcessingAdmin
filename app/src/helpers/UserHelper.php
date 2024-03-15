<?php

namespace app\helpers;
use Yii;
use yii\helpers\ArrayHelper;
use app\models\User;

class UserHelper
{
    public static function getRemoteId($username, $password)
    {
        try {
            $conn = new \PDO(Yii::$app->db->dsn, $username, $password);
            $sql = 'SELECT * FROM cyberft.w_users WHERE status=? and account_status=?';
            $stmt = $conn->prepare($sql);
            $stmt->execute([1,1]);
            $row = $stmt->fetch();

            return $row['user_id'];
        } catch(\Exception $e) {
            return null;
        }
    }

    public static function makeIdentityData($id, $username)
    {
        return [
            'id' => $id,
            'username' => $username
        ];
    }

    public static function cacheIdentityData($data)
    {
        $authKey = self::getAuthKey();

        self::deleteCachedIdentityData($authKey);

        Yii::$app->cache->add($authKey, $data);
    }

    public static function getCachedIdentityData()
    {
        $authKey = self::getAuthKey();

        if (\Yii::$app->cache->exists($authKey) === false) {
            return null;
        }

        return \Yii::$app->cache->get($authKey);
    }

    public static function deleteCachedIdentityData($authKey = null)
    {
        if ($authKey == null) {
            $authKey = self::getAuthKey();
        }

        if (Yii::$app->cache->exists($authKey)) {
            Yii::$app->cache->delete($authKey);
        }
    }

    public static function encryptPassword($password)
    {
        $authKey = self::getAuthKey();
        $hash = Yii::$app->security->encryptByPassword($password, $authKey);

        return bin2hex($hash);
    }

    public static function decryptPassword($passwordHash)
    {
        $authKey = self::getAuthKey();
        $binary = hex2bin($passwordHash);

        return Yii::$app->security->decryptByPassword($binary, $authKey);
    }

    public static function addPasswordToIdentityData($password, $identityData)
    {
        $encryptedPassword = UserHelper::encryptPassword($password);
        $identityData['password'] = $encryptedPassword;

        return $identityData;
    }

    public static function setDBData($userData)
    {
        $username = $userData['username'];
        $password = self::decryptPassword($userData['password']);

        Yii::$app->db->username = $username;
        Yii::$app->db->password = $password;
    }

    public static function getAuthKey()
    {
        return 'auth_' . Yii::$app->session->id;
    }

    public static function getUserRoles()
    {
        $result = Yii::$app->db->createCommand('SELECT * FROM cyberft.v_roles')->queryAll();
        $result = self::translateRoleName($result);
        $result = ArrayHelper::map($result, 'role_id', 'role_name');
        unset($result[User::ROLE_REGULAR_USER]);
        return $result;
    }

    public static function translateRoleName($roles)
    {
        $func = function($role) {
            $role['role_name'] = Yii::t('app/role', $role['role_name']);
            return $role;
        };

        return array_map($func, $roles);
    }
}
