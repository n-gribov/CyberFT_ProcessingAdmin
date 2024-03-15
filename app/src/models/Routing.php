<?php

namespace app\models;

use app\models\ActiveRecord;
use Yii;

/**
 * This is the model class for table "cyberft.v_routing_table".
 *
 * @property int $route_id
 * @property int $dst_node
 * @property string $dst_swift_code
 * @property string $dst_member_name
 * @property int $src_node
 * @property string $src_swift_code
 * @property string $src_member_name
 */
class Routing extends ActiveRecord
{
    const SCENARIO_CREATE = 'create';

    public static function tableName()
    {
        return 'cyberft.v_routing_table';
    }

    public static function primaryKey()
    {
        return ['route_id'];
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_CREATE] = ['dst_node', 'src_node'];
        return $scenarios;
    }

    public function rules()
    {
        return [
            [['route_id', 'dst_node', 'src_node'], 'default', 'value' => null],
            [['route_id', 'dst_node', 'src_node'], 'integer'],
            [['dst_swift_code', 'src_swift_code'], 'string', 'max' => 80],
            [['dst_member_name', 'src_member_name'], 'string', 'max' => 255],
        ];
    }

    public function attributeLabels()
    {
        return [
            'dst_node' => Yii::t('app/routing', 'Receiver'),
            'dst_swift_code' => Yii::t('app/routing', 'Receiver code'),
            'dst_member_name' => Yii::t('app/routing', 'Receiver'),
            'src_node' => Yii::t('app/routing', 'Original receiver'),
            'src_swift_code' => Yii::t('app/routing', 'Original receiver code'),
            'src_member_name' => Yii::t('app/routing', 'Original receiver'),
        ];
    }

    protected function executeInsert()
    {
        $query = '
            select
                piIsErrorOut as "hasError",
                pcErrCodeOut as "errorCode",
                pcErrMsgOut as "errorMessage",
                piRouteOut as "id"
            from cyberft.p_member_api_add_route(
                :piDst,
                :piSrc
            )
        ';

        $result = Yii::$app->db
            ->createCommand(
                $query,
                [
                    ':piDst' => $this->dst_node,
                    ':piSrc' => $this->src_node,
                ]
            )
            ->queryOne();

        return $this->processInsertQueryResult($result);
    }

    protected function executeUpdate()
    {
        return false;
    }

    protected function executeDelete()
    {
        $query = '
            select
                piiserrorout as "hasError",
                pcerrcodeout as "errorCode",
                pcerrmsgout as "errorMessage"
            from cyberft.p_member_api_del_route(
                :piRoute
            )
        ';

        $result = Yii::$app->db
            ->createCommand(
                $query,
                [
                    ':piRoute' => $this->route_id,
                ]
            )
            ->queryOne();

        return $this->processDeleteQueryResult($result);
    }
}
