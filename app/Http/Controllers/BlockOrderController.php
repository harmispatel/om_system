<?php

namespace App\Http\Controllers;

use App\Models\Block_reason;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Request;

class BlockOrderController extends Controller
{
   public function index(Request $request){

    if($request->ajax()){
        $block_reasons = Block_reason::get();
        return DataTables::of($block_reasons)
        ->rawColumns([])
        ->make(true);
    }
    return view('admin.blockReason.index');
   }
   public function create(){
     return view('admin.blockReason.create');
   }
   public function store(Request $request){
        $request->validate([
            'reason'  => 'required'
        ]);
     try{
        $input = $request->except('_token');
        $storeReason = Block_reason::create($input);
        return redirect()->route('block-reasons.create')->with('success','Block Reason Added SuccessFully');
     }catch(\Throwable $th){
        return redirect()->back()->with('error','internal server error!');
     }
   }
}
