<?php

namespace App\Http\Controllers\Admin;

use App\Group;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class GroupController extends Controller
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
    private $controller = 'group';

    private function title(){
        return "Departemen List";
    }

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $data['page_active'] ="group";
        $data['controller'] =$this->controller;
        $data['pages_title'] =$this->title();
        $data['group'] = \App\Group::all()->where('status', 'Y');
        $data['n'] = 1;
        return view('admin.group')->with($data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        // dd($request);
        $data = new Group;
        $data->group_name = $request->group_name;
        $data->display_name = $request->display_name;
        $data->save();
        return redirect('dashboard/group');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
      $id = $request->id;
      $group = \App\Group::find($id);
      $data = array('group'=>$group);
      return response()->json($data);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Group  $group
     * @return \Illuminate\Http\Response
     */
    public function show(Group $group)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Group  $group
     * @return \Illuminate\Http\Response
     */
    public function edit(Group $group)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Group  $group
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
      // dd($request);
      $data = \App\Group::find($request->update);
      $data->group_name = $request->group_name;
      $data->display_name = $request->display_name;
      $data->save();

        // dd($data);
        return redirect('dashboard/group');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Group  $group
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
      \App\Group::where('id_group', $request->id_group)
        ->update(['status' => 'N']);

        // dd($data);
        return redirect('dashboard/group');
    }
}
