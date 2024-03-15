<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord as YiiActiveRecord;
use yii\db\StaleObjectException;

abstract class ActiveRecord extends YiiActiveRecord
{
    protected $lastDbErrorCode;
    protected $lastDbErrorMessage;

    /**
     * Executes query to insert new record in database
     * Returns associative array with primary keys values of a newly created record.
     * Return false insertion has failed.
     *
     * @return array|false
     */
    abstract protected function executeInsert();

    /**
     * Executes query to update a record in database
     * Returns number successfully updated rows or false in case of failure.
     *
     * @return int|false
     */
    abstract protected function executeUpdate();

    /**
     * Executes query to delete a record in database
     * Returns number successfully deleted rows or false in case of failure.
     *
     * @return int|false
     */
    abstract protected function executeDelete();

    public function getLastDbErrorCode()
    {
        return $this->lastDbErrorCode;
    }

    public function getLastDbErrorMessage()
    {
        return $this->lastDbErrorMessage;
    }

    protected function setLastDbError($code = null, $message = null)
    {
        $this->lastDbErrorCode = $code;
        $this->lastDbErrorMessage = $message;
    }

    protected function insertInternal($attributes = null)
    {
        $this->setLastDbError();

        if (!$this->beforeSave(true)) {
            return false;
        }

        $primaryKeys = $this->executeInsert();

        if (!$primaryKeys) {
            return false;
        }

        $tableSchema = static::getTableSchema();

        foreach ($primaryKeys as $name => $value) {
            $id = $tableSchema->columns[$name]->phpTypecast($value);
            $this->setAttribute($name, $id);
            $values[$name] = $id;
        }

        $changedAttributes = array_fill_keys(array_keys($values), null);
        $this->setOldAttributes($values);
        $this->afterSave(true, $changedAttributes);

        return true;
    }

    protected function updateInternal($attributes = null)
    {
        $this->setLastDbError();

        if (!$this->beforeSave(false)) {
            return false;
        }
        $values = $this->getDirtyAttributes($attributes);
        if (empty($values)) {
            $this->afterSave(false, $values);
            return 0;
        }
        $condition = $this->getOldPrimaryKey(true);
        $lock = $this->optimisticLock();
        if ($lock !== null) {
            $values[$lock] = $this->$lock + 1;
            $condition[$lock] = $this->$lock;
        }

        $updateResult = $this->executeUpdate();

        if ($lock !== null && !$updateResult) {
            throw new StaleObjectException('The object being updated is outdated.');
        }

        if (isset($values[$lock])) {
            $this->$lock = $values[$lock];
        }

        $changedAttributes = [];
        foreach ($values as $name => $value) {
            $changedAttributes[$name] = isset($this->oldAttributes[$name]) ? $this->oldAttributes[$name] : null;
            $this->setOldAttribute($name, $value);
        }
        $this->afterSave(false, $changedAttributes);

        return $updateResult;
    }

    protected function deleteInternal($attributes = null)
    {
        $this->setLastDbError();

        if (!$this->beforeDelete()) {
            return false;
        }

        $condition = $this->getOldPrimaryKey(true);
        $lock = $this->optimisticLock();
        if ($lock !== null) {
            $condition[$lock] = $this->$lock;
        }
        $deleteResult = $this->executeDelete();

        if ($lock !== null && !$deleteResult) {
            throw new StaleObjectException('The object being deleted is outdated.');
        }
        $this->setOldAttributes(null);
        $this->afterDelete();

        return $deleteResult;

    }

    /**
     * Interprets result of insert query execution, sets primary key or error message.
     * Accepts associative array with keys:
     *   - hasError - 0 or 1
     *   - id - id of newly created record
     *   - errorCode
     *   - errorMessage
     *
     * @param array $result
     * @return array|false
     */
    protected function processInsertQueryResult(array $result)
    {
        if ($result['hasError'] == 1) {
            $this->setLastDbError($result['errorCode'], $result['errorMessage']);

            $tableName = static::tableName();
            Yii::info("Failed to insert record into {$tableName}, {$result['errorMessage']}");

            return false;
        } else {
            if (count(static::primaryKey()) !== 1) {
                throw new \LogicException('Model must have exactly one primary key');
            }
            $primaryKey = static::primaryKey()[0];
            $id = $result['id'];
            $this->$primaryKey = $id;
            return [$primaryKey => $id];
        }
    }

    /**
     * Interprets result of update query execution, sets error message.
     * Accepts associative array with keys:
     *   - hasError - 0 or 1
     *   - errorCode
     *   - errorMessage
     *
     * @param array $result
     * @return int|false
     */
    protected function processUpdateQueryResult(array $result)
    {
        return $this->processRecordChangeQueryResult($result, 'update');
    }

    /**
     * Interprets result of delete query execution, sets error message.
     * Accepts associative array with keys:
     *   - hasError - 0 or 1
     *   - errorCode
     *   - errorMessage
     *
     * @param array $result
     * @return int|false
     */
    protected function processDeleteQueryResult(array $result)
    {
        return $this->processRecordChangeQueryResult($result, 'delete');
    }

    protected function processRecordChangeQueryResult(array $result, string $action)
    {
        if ($result['hasError'] == 1) {
            $this->setLastDbError($result['errorCode'], $result['errorMessage']);

            $tableName = static::tableName();
            Yii::info("Failed to perform $action on $tableName record, error: {$result['errorMessage']}");

            return false;
        } else {
            return 1;
        }
    }
}
