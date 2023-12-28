<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reason extends Model
{
    use HasFactory;

    protected $table = 'reasons';
    protected $guarded =[];

    function departments()
    {
        return $this->hasOne(Role::class, 'id', 'department');
    }
}
