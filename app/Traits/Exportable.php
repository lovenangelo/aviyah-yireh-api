<?php

namespace App\Traits;

trait Exportable
{
    public function getExportData()
    {
        $fields = $this->getExportFields();
        $data = $this->only(array_keys($fields));

        // Apply any transformations defined in the model
        if (method_exists($this, 'transformExportData')) {
            $data = $this->transformExportData($data);
        }

        return $data;
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
