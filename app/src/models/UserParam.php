<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "cyberft.v_usr_params".
 *
 * @property int $par_id
 * @property int $user_id
 * @property string $user_code
 * @property int $par_type
 * @property string $par_value
 */
class UserParam extends ActiveRecord
{
    const PARAM_TYPE_COLUMNS_SETTINGS = 1;

    const DB_ACTION_INSERT = 1;
    const DB_ACTION_UPDATE = 2;
    const DB_ACTION_DELETE = 3;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'cyberft.v_usr_params';
    }

    public static function primaryKey()
    {
        return ['par_id'];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['par_id', 'user_id', 'par_type'], 'default', 'value' => null],
            [['par_id', 'user_id', 'par_type'], 'integer'],
            [['par_value'], 'string'],
            [['user_code'], 'string', 'max' => 80],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'par_id' => Yii::t('app/user', 'ID'),
            'user_id' => Yii::t('app/user', 'User ID'),
            'user_code' => Yii::t('app/user', 'User code'),
            'par_type' => Yii::t('app/user', 'Type'),
            'par_value' => Yii::t('app/user', 'Value'),
        ];
    }

    protected function executeInsert()
    {
        $result = $this->executeDbAction(static::DB_ACTION_INSERT);

        if ($result['hasError'] == 1) {
            $this->setLastDbError($result['errorCode'], $result['errorMessage']);

            $tableName = static::tableName();
            Yii::info("Failed to insert record into {$tableName}, {$result['errorMessage']}");

            return false;
        } else {
            $param = static::findOne(['par_type' => $this->par_type, 'user_id' => $this->user_id]);
            return ['par_id' => $param->par_id];
        }
    }

    protected function executeUpdate()
    {
        $result = $this->executeDbAction(static::DB_ACTION_UPDATE);

        return $this->processUpdateQueryResult($result);
    }

    protected function executeDelete()
    {
        $result = $this->executeDbAction(static::DB_ACTION_DELETE);

        return $this->processDeleteQueryResult($result);
    }

    private function executeDbAction($actionType)
    {
        $query = '
          select
            piiserrorout as "hasError",
            pcerrcodeout as "errorCode",
            pcerrmsgout as "errorMessage"
          from cyberft.p_user_api_set_usr_param(
            :user_id,
            :type,
            :value,
            :action
          )
        ';

        return Yii::$app->db
            ->createCommand(
                $query,
                [
                    ':user_id' => $this->user_id,
                    ':type'    => $this->par_type,
                    ':value'   => $this->par_value,
                    ':action'  => $actionType,
                ]
            )
            ->queryOne();
    }
}
