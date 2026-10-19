<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

class Setting extends Model
{
    protected $primaryKey = 'key';
    protected $keyType    = 'string';
    public    $incrementing = false;

    protected $fillable = ['key', 'value'];

    private static ?Collection $cache = null;

    public static function get(string $key, string $default = ''): string
    {
        if (static::$cache === null) {
            static::$cache = Schema::hasTable('settings')
                ? static::all()->pluck('value', 'key')
                : collect();
        }

        return (string) (static::$cache->get($key) ?? $default);
    }

    public static function flush(): void
    {
        static::$cache = null;
    }
}
