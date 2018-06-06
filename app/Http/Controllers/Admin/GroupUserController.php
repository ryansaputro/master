<?php

namespace App\Http\Controllers\Admin;

use App\GroupUser;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class GroupUserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
     /**
      * Create a new controller instance.
      *
      * @return void
      */
     private $controller = 'group-user';

     private function title(){
         return "Departemen User List";
     }

     public function __construct()
     {
         $this->middleware('auth');
     }

     public function index()
     {
         $data['page_active'] ="group-user";
         $data['controller'] =$this->controller;
         $data['pages_title'] =$this->title();
         // $data['users'] = \App\User::all()->where('status', 'Y');
         $data['users'] = DB::table('ek_users')
         ->select('*', 'ek_users.id_users as id')
         ->leftJoin('tr_group_user', 'ek_users.id_users', '=', 'tr_group_user.id_users')
         ->leftJoin('tb_group', 'tr_group_user.id_group', '=', 'tb_group.id_group')
         ->where('ek_users.status', 'Y')
         ->get();
         $data['group'] = \App\Group::where('status', 'Y')->orderBy('display_name', 'asc')->get();
         $data['n'] = 1;
         return view('admin.group-user')->with($data);
     }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
      // dd($request);
      $data = new GroupUser;
      $data->id_users = $request->add;
      $data->id_group = $request->id_group;
      $data->save();
      // dd($data);
      return redirect('dashboard/group-user');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\GroupUser  $groupUser
     * @return \Illuminate\Http\Response
     */
    public function show(GroupUser $groupUser)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\GroupUser  $groupUser
     * @return \Illuminate\Http\Response
     */
    public function edit(GroupUser $groupUser)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\GroupUser  $groupUser
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
      $data = \App\GroupUser::find($request->update);
      $data->id_group = $request->id_group;
      $data->save();

        // dd($data);
        return redirect('dashboard/group-user');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\GroupUser  $groupUser
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
      \App\GroupUser::where('id_users', $request->id_users)
        ->delete();

        // dd($data);
        return redirect('dashboard/group-user');
    }
}
