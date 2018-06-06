<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Role;
use App\Permission;
use DB;

use Datatables;


class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    private $controller = 'roles';

    private function title(){
        return __('main.user_role');
    }

    public function index(Request $request)
    {

        $controller =$this->controller;
        $page_active ="roles";
        $pages_title =$this->title();
        $roles = Role::orderBy('id','DESC')->paginate(5);
        return view('admin.role_index',compact('roles','pages_title','page_active','controller'))
            ->with('i', ($request->input('page', 1) - 1) * 5);
    }

    // public function getData()
    // {
    //     $users = DB::table('users')->select(['id', 'name', 'email', 'created_at', 'updated_at']);

    //     return Datatables::of($users)->make();
    // }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $controller =$this->controller;
        $pages_title =$this->title();
        $permission = Permission::orderBy('urutan','ASC')->get();
        $page_active ="roles";
        
        return view('admin.role_create',compact('permission','pages_title','page_active','controller'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|unique:roles,name',
            'display_name' => 'required',
            'description' => 'required',
            'permission' => 'required',
        ]);

        $role = new Role();
        $role->name = $request->input('name');
        $role->display_name = $request->input('display_name');
        $role->description = $request->input('description');
        $role->save();

        foreach ($request->input('permission') as $key => $value) {
            $role->attachPermission($value);
        }

        return redirect()->route('roles.index')
                        ->with('success','Role created successfully');
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $pages_title =$this->title();
        $page_active ="roles";
        $controller =$this->controller;
        $role = Role::find($id);
        $rolePermissions = Permission::join("permission_role","permission_role.permission_id","=","permissions.id")
            ->where("permission_role.role_id",$id)
            ->get();

        return view('admin.role_show',compact('role','rolePermissions','pages_title','page_active','controller'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $controller=$this->controller;
        $page_active ="roles";
        $pages_title =$this->title();
        $page_active ="roles";
        $role = Role::find($id);
        // $permission = Permission::get();
        $permission = Permission::orderBy('urutan','ASC')->get();
        $rolePermissions = DB::table("permission_role")->where("permission_role.role_id",$id)
            ->pluck('permission_role.permission_id','permission_role.permission_id')->all();

        return view('admin.role_edit',compact('role','permission','rolePermissions','pages_title','page_active','controller'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'display_name' => 'required',
            'description' => 'required',
            'permission' => 'required',
        ]);

        $role = Role::find($id);
        $role->display_name = $request->input('display_name');
        $role->description = $request->input('description');
        $role->save();

        DB::table("permission_role")->where("permission_role.role_id",$id)
            ->delete();

        foreach ($request->input('permission') as $key => $value) {
            $role->attachPermission($value);
        }

        return redirect()->route('roles.index')
                        ->with('success','Role updated successfully');
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        DB::table("roles")->where('id',$id)->delete();
        return redirect()->route('roles.index')
                        ->with('success','Role deleted successfully');
    }
}