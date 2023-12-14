<?php

namespace App\Models;

use Spatie\Permission\Models\{Role};
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class order extends Model
{
    use HasFactory;

    protected $guarded = [];
    
    protected $table="orders";

}
