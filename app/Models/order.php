<?php

namespace App\Models;

use Spatie\Permission\Models\{Role};
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class order extends Model
{
    use HasFactory,SoftDeletes;

    protected $guarded = [];

    protected $table="orders";
    protected $dates = ['deleted_at'];

    function department()
    {
        return $this->hasOne(Role::class, 'id', 'order_status');
    }

}
