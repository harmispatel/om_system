<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Role;

class Order_history extends Model
{
    use HasFactory;
    protected $guarded=[];
    protected $table='order_history';

    public $timestamps = true;

    public function receivePermission()
    {
        return $this->hasOne(Permission::class,'id','receive_switch');
    }

    public function issuePermission()
    {
        return $this->hasOne(Permission::class,'id','switch_type');
    }

    function department()
    {
        return $this->hasOne(Role::class, 'id', 'user_type');
    }

    function order()
    {
        return $this->hasOne(order::class, 'id', 'order_id');
    }

    function user()
    {
        return $this->hasOne(Admin::class, 'id', 'user_id');
    }
}
