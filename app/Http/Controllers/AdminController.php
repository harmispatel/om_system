<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\UserRequest;
use Spatie\Permission\Models\{Role, Permission};
use Yajra\DataTables\Facades\DataTables;
use App\Traits\ImageTrait;
use App\Models\{Admin, RoleHasPermissions};
use phpseclib3\Crypt\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;


class AdminController extends Controller
{
    use ImageTrait;

    function __construct()
    {
        $this->middleware('permission:users|users.create|users.edit|users.destroy', ['only' => ['index','store']]);
        $this->middleware('permission:users.create', ['only' => ['create','store']]);
        $this->middleware('permission:users.edit', ['only' => ['edit','update']]);
        $this->middleware('permission:users.destroy', ['only' => ['destroy']]);
    }

    // Display a listing of the resource.
    public function index()
    {
        return view('admin.users.users');
    }
    public function userProfile()
    {
        $userData = Admin::get();
        return view('admin.users.user-profile',['userdata'=> $userData ]);
    }

    // newly created record
    public function create()
    {
        $roles = Role::all();
        return view('admin.users.create_users', compact('roles'));
    }

    // Load All Users
    public function loadUsers(Request $request)
    {
        if ($request->ajax())
        {
            // Get all Admins
            $admins = Admin::get();
            return DataTables::of($admins)
            ->addIndexColumn()
            ->addColumn('name', function ($row)
            {
                $firstname = $row->firstname;
                $lastname = $row->lastname;
                $name = $firstname .' '.$lastname;
                return $name;
            })
            ->addColumn('image', function ($row)
            {
                $default_image = asset("public/images/demo_images/not-found/not-found4.png");
                $image = (isset($row->image) && !empty($row->image) && file_exists('public/images/uploads/user_images/'.$row->image)) ? asset('public/images/uploads/user_images/'.$row->image) : $default_image;
                $image_html = '';
                $image_html .= '<img class="me-2 rounded-circle" src="'.$image.'" width="65" height="65">';
                return $image_html;
            })
            ->addColumn('usertype', function ($row)
            {
                $usertype = $row->user_type;
                $role = Role::where('id',$usertype)->first();
                return (isset($role['name'])) ? $role['name'] : '';
            })
            ->addColumn('status', function ($row)
            {
                $status = $row->status;
                $checked = ($status == 1) ? 'checked' : '';
                $checkVal = ($status == 1) ? 0 : 1;
                $user_id = isset($row->id) ? $row->id : '';
                $diabled = ($user_id == 1) ? 'disabled' : '';

                return '<div class="form-check form-switch"><input class="form-check-input" type="checkbox" role="switch" onchange="changeStatus('.$checkVal.','.$user_id.')" id="statusBtn" '.$checked.' '.$diabled.'></div>';

            })
            ->addColumn('actions',function($row)
            {
                $user_id = isset($row->id) ? $row->id : '';
                $user_edit = Permission::where('name','users.edit')->first();
                $user_delete = Permission::where('name','users.destroy')->first();
                $user_type =  Auth::guard('admin')->user()->user_type;
                $roles = RoleHasPermissions::where('role_id',$user_type)->pluck('permission_id');
                foreach ($roles as $key => $value) {
                   $val[] = $value;
                  }
                $action_html = '';
                if(in_array($user_edit->id,$val)){

                    $action_html .= '<a href="'.route('users.edit',encrypt($user_id)).'" class="btn btn-sm custom-btn me-1"><i class="bi bi-pencil"></i></a>';
                }
                // if(in_array($user_delete->id,$val)){
                //     if($user_id != 1){
                //         $action_html .= '<a onclick="deleteUsers(\''.encrypt($user_id).'\')" class="btn btn-sm btn-danger me-1"><i class="bi bi-trash"></i></a>';
                //     }
                // }

                return $action_html;
            })
            ->rawColumns(['status','usertype','actions','image','name'])
            ->make(true);
        }
    }

    // Store a newly created record
    public function store(UserRequest $request)
    {

        try {

            $input = $request->except('_token','image','confirm_password','password');
            $input['password'] = Hash::make($request->password);

            if ($request->hasFile('image'))
            {
                $file = $request->file('image');
                $image_url = $this->addSingleImage('user','user_images',$file, $old_image = '',"300*300");
                $input['image'] = $image_url;
            }

            $user = Admin::create($input);

            $user_type = $user->user_type;
            $roles = Role::where('id',$user_type)->first();
            $user->assignRole($roles->name);

            // Mail::send('mail', $request->email, function($message) {
            //     $message->to($request->email, 'Tutorials Point')->subject
            //        ('Laravel HTML Testing Mail');
            //     $message->from('developers@harmistechnology.com','harmis technology');
            //  });

            return redirect()->route('users')->with('success','User has been Created Successfully..');

        } catch (\Throwable $th) {
            return redirect()->route('users')->with('error','Something went wrong!');
        }
    }

    // Change user status
    public function status(Request $request)
    {
        $status = $request->status;
        $id = $request->id;

        try {
            $input = Admin::find($id);
            $input->status =  $status;
            $input->update();

            return response()->json([
                'success' => 1,
                'message' => "User Status has been Changed Successfully..",
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => 0,
                'message' => "Internal Server Error!",
            ]);
        }
    }

    // Edit existing record
    public function edit(Request $request, $id)
    {
        $id = decrypt($id);
        $data = Admin::where('id',$id)->first();
        $roles = Role::all();

        return view('admin.users.edit_users',compact('data','roles'));
    }

    // Update existing record
    public function update(UserRequest $request)
    {

        try {

            $input = $request->except('_token','id','password','confirm_password','image');
            $id = decrypt($request->id);

            if(!empty($request->password) || $request->password != null)
            {
                $input['password'] = Hash::make($request->password);
            }

            if ($request->hasFile('image'))
            {
                $img = Admin::where('id',$id)->first();
                $old_image = $img->image;
                $file = $request->file('image');
                $image_url = $this->addSingleImage('user','user_images',$file, $old_image = '',"300*300");
                $input['image'] = $image_url;
            }

            $user = Admin::find($id);
            $user->update($input);
            DB::table('model_has_roles')->where('model_id',$id)->delete();

            $user_type = $user->user_type;
            $roles = Role::where('id',$user_type)->first();
            $user->assignRole($roles->name);

            return redirect()->route('users')->with('success','User has been Updated Successfully');

        } catch (\Throwable $th) {
            return redirect()->route('users')->with('error','Something went wrong!');
        }
    }

    // Delete Specific record
    public function destroy(Request $request)
    {
        try {
            //code...
            $id = decrypt($request->id);

            $user = Admin::where('id',$id)->first();

            $img = isset($user->image) ? $user->image : '';

            if (!empty($img) && file_exists('public/images/uploads/user_images/'.$img))
            {
                unlink('public/images/uploads/user_images/'.$img);
            }

            Admin::where('id',$id)->delete();

            return response()->json([
                'success' => 1,
                'message' => "User has been Deleted Successfully..",
            ]);

        } catch (\Throwable $th) {
            return response()->json([
                'success' => 0,
                'message' => "Something went wrong!",
            ]);
        }
    }

}
