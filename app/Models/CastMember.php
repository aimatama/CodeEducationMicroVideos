<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Ramsey\Uuid\Uuid;

class CastMember extends Model
{
    use SoftDeletes, Traits\Uuid;
    protected $fillable = ['name', 'type', 'is_active'];
    protected $dates = ['deleted_at']; 
    protected $casts = [
        'id' => 'string',
        'is_active' => 'boolean'
    ];
    public $incrementing = false;
}
