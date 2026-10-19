<?php

namespace App\Traits;

use App\Models\Setting;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Schema;

trait FindByIdentifier
{
    public static function findByIdentifier(string $value)
    {
        $preferred = Setting::get('pos.identifier', 'uuid');
        $table     = (new static)->getTable();
        $query     = static::query();

        if ($preferred !== 'id' && Schema::hasColumn($table, $preferred)) {
            $query->where($preferred, $value);
        } else {
            $query->where('id', $value);
        }

        return $query->firstOr(function () {
            throw (new ModelNotFoundException)->setModel(static::class);
        });
    }
}
