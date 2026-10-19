<?php

namespace App\Models;

use App\Models\Setting;
use App\Traits\FindByIdentifier;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BaseModel extends Model
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasUuid, FindByIdentifier, HasFactory;

    public function getRouteKeyName(): string
    {
        $col = Setting::get('pos.identifier', 'uuid');
        if (! in_array($col, ['uuid', 'slug', 'id'])) {
            return 'id';
        }

        if ($col === 'slug' && ! in_array('slug', $this->getFillable())) {
            return 'id';
        }
        return $col;
    }
}
