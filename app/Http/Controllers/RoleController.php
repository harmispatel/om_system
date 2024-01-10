<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use App\Traits\ImageTrait;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use DB;
use App\Models\{Admin, RoleHasPermissions};
use Illuminate\Support\Facades\Auth;

use function PHPUnit\Framework\throwException;

class RoleController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    function __construct()
    {
         $this->middleware('permission:roles|roles.create|roles.edit|roles.destroy', ['only' => ['index','store']]);
         $this->middleware('permission:roles.create', ['only' => ['create','store']]);
         $this->middleware('permission:roles.edit', ['only' => ['edit','update']]);
         $this->middleware('permission:roles.destroy', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {

        if ($request->ajax())
        {
            $roles = Role::with(['permissions'])->get();

            return DataTables::of($roles)
            ->addIndexColumn()

            ->addColumn('switches',function($row){
                $permissions_html = '';
               $permissions = (isset($row['permissions'])) ? $row['permissions'] : [];
               if(count($permissions) > 0)
               {
                    foreach($permissions as $perm){

                        if($perm->id == 1){
                            $permissions_html .= '<span class="badge bg-light text-dark">All Privilege</span>';
                            return $permissions_html;
                        }else{
                            $permissions_html .= '<span class="badge bg-light text-dark me-2">'.$perm['name'].'</span>';

                        }
                    }
               }
               return $permissions_html;
            })
            // ->editColumn('working_hours', function ($roles) {
            //     return $roles->working_hours.' : '.$roles->working_minutes;
            //      })
            // ->addColumn('actions',function($row)
            // {
            //     $role_id = isset($row->id) ? encrypt($row->id) : '';
            //     $action_html = '';
            //     $role_edit = Permission::where('name','roles.edit')->first();
            //     $role_delete = Permission::where('name','roles.destroy')->first();
            //     $user_type =  Auth::guard('admin')->user()->user_type;
            //     $roles = RoleHasPermissions::where('role_id',$user_type)->pluck('permission_id');
            //     foreach ($roles as $key => $value) {
            //        $val[] = $value;
            //       }
            //     if(in_array($role_edit->id,$val)){
            //         if(decrypt($role_id) != 1){
            //             $action_html .= '<a href="'.route('roles.edit',$role_id).'" class="btn btn-sm custom-btn me-1 "><i class="bi bi-pencil"></i></a>';
            //         }else{
            //             $action_html .= '-';
            //         }
            //     }else{
            //         $action_html .= '<a class="btn btn-sm btn-danger me-1 disabled" ><i class="bi bi-pencil"></i></a>';
            //     }
            //     if(in_array($role_delete->id,$val)){
            //         if(decrypt($role_id) != 1){
            //             $action_html .= '<a  onclick="deleteRole(\''.$role_id.'\')" class="btn btn-sm btn-danger me-1"><i class="bi bi-trash"></i></a>';
            //         }else{
            //             $action_html .= '-';
            //         }
            //     }else{
            //         $action_html .= '<a class="btn btn-sm btn-danger me-1 disabled"><i class="bi bi-trash"></i></a>';
            //     }
            //     return $action_html;
            // })
            ->rawColumns(['switches'])
            ->make(true);
        }
        return view('admin.roles.index');
    }

    public function create()
    {
        $permission = Permission::get();

        $is_counter = Role::get();

        return view('admin.roles.create_roles',compact('permission','is_counter'));
    }

    public function store(Request $request)
    {

        $this->validate($request, [
            'name' => 'required|unique:roles,name',
            'permission' => 'required',
            'working_hours'=>'required|max:24|min:1',
            'working_minutes'=>'required|max:59|min:0',
        ],
        [
            'working_hours.required|max:24|min:1' => 'please enter valid working Hours',
        ]);
    try{
        $is_counter= isset($request->is_counter) ? 1 : 0 ;

        $role = Role::create(
            [
             'name' => $request->input('name'),
             'working_hours' => $request->working_hours,
             'working_minutes' => $request->working_minutes,
             'is_counter'=>$is_counter,
             'order_value' => $request->input('order_value'),
            ]
        );

        // $isChecked = $request->has('is_counter');

        // $checkboxValue = $isChecked ? 1 : 0;

        // $role = Role::create([
        //     'name' => $request->input('name'),
        //     'is_counter' => $checkboxValue,

        // ]);
        $role->syncPermissions($request->input('permission'));


        return redirect()->route('roles')
                        ->with('success','Role created successfully');
        }catch(\Throwable $th){
            return redirect()->route('roles')
            ->with('error','Role Not Created ! Some Error');
        }
    }

    public function edit(Request $request, $id)
    {

        $id = decrypt($id);
        $role = Role::find($id);
        $permission = Permission::get();
        $rolePermissions = DB::table("role_has_permissions")->where("role_has_permissions.role_id",$id)
            ->pluck('role_has_permissions.permission_id','role_has_permissions.permission_id')
            ->all();

        return view('admin.roles.edit_roles',compact('role','permission','rolePermissions'));
    }

    public function update(Request $request)
    {

        $this->validate($request, [
            'name' => 'required',
            'permission' => 'required',
            'working_hours'=>'required|max:24|min:1',
            'working_minutes'=>'required|max:59|min:0',
        ]);

    try{
        $is_counter= isset($request->is_counter) ? 1 : 0 ;
        $id = decrypt($request->id);

        $role = Role::find($id);
        $role->name = $request->input('name');
        $role->working_hours = $request->working_hours;
        $role->working_minutes = $request->working_minutes;
        $role->is_counter =  $is_counter;
        $role->save();

        $role->syncPermissions($request->input('permission'));

        return redirect()->route('roles')
                        ->with('success','Role updated successfully');
         }catch(\Throwable $th){
             return redirect()->route('roles')
             ->with('error','Role Not Updated ! Some Error');
         }
    }

    public function destroy(Request $request)
    {
        try
        {
            $id = decrypt($request->id);
            $role = Role::where('id',$id)->delete();


            return response()->json(
            [
                'success' => 1,
                'message' => "Role delete Successfully..",
            ]);
        }
        catch (\Throwable $th)
        {
            return response()->json(
            [
                'success' => 0,
                'message' => "Something with wrong",
            ]);
        }

    }


}
