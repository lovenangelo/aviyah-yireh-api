<?php

namespace App\Traits;

trait Exportable
{
    public function getExportData()
    {
        $fields = $this->getExportFields();
        $data = $this->only(array_keys($fields));

        return $this->renameKeys($data, $fields);

    }

    private function renameKeys(array $data, array $keys)
    {

        $newData = [];

        foreach ($data as $key => $value) {
            $newKey = $keys[$key] ?? $key;
            $newData[$newKey] = $value;
        }

        return $newData;

    }

    public static function getExportHeaders()
    {
        return array_values((new static)->getExportFields());
    }

    protected function getExportFields()
    {
        return static::$exportFields ?? [];
    }

    public static function getExportCollection($query = null)
    {
        $query = $query ?? static::query();
        return $query->get()->map(function ($item) {
            return $item->getExportData();
        });
    }
}
