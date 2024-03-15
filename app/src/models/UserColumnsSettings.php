<?php

namespace app\models;

class UserColumnsSettings
{
    public function get(User $user, string $modelClass): array
    {
        /** @var UserParam $param */
        $param = $user
            ->getParams()
            ->where(['par_type' => UserParam::PARAM_TYPE_COLUMNS_SETTINGS])
            ->one();

        $columnsSettingsJson = $param ? $param->par_value : '[]';
        $columnsSettings = json_decode($columnsSettingsJson, true);
        $listType = static::getListType($modelClass);
        if (!array_key_exists($listType, $columnsSettings)) {

        }
    }

    public function filterGridViewColumns(User $user, string $modelClass, array $columns): array
    {
        $settings = $this->get($user, $modelClass);

        return array_reduce(
            array_keys($settings),
            function ($carry, $columnKey) use ($settings, $columns) {
                if (!array_key_exists($columnKey, $columns)) {
                    return $carry;
                }
                if (!$settings[$columnKey]) {
                    return $carry;
                }
                $carry[] = $columns[$columnKey];
                return $carry;
            },
            []
        );
    }

    private static function getListType(string $modelClass): string
    {
        return (new \ReflectionClass($modelClass))->getShortName();
    }
}
