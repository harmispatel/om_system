<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\order;
use App\Models\orderimage;
use PDF;

class DompdfController extends Controller
{
   public function getPdf($id){

        $orderdetail = order::where('orderno',$id)->first();
        $orderimages = orderimage::where('order_id',$orderdetail->id)->get();
    
         $pdf= PDF::loadview('admin.orders.printform',compact('orderdetail','orderimages'));
         return $pdf->download('orderdetail-'.$id.'.pdf');
    }
}
