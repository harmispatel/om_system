<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order_history extends Model
{
    use HasFactory;
    protected $guarded=[];
    protected $table='order_history';

    public $timestamps = true;
     public function receivePermission(){
        return $this->hasOne(Permission::class,'id','receive_switch');
    }
    public function issuePermission(){
        return $this->hasOne(Permission::class,'id','switch_type');
    }
}
