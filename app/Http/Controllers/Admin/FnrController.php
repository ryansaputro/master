<?php

namespace App\Http\Controllers\Admin;

use App\Fnr;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class FnrController extends Controller
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
     private $controller = 'fnr';

     private function title(){
         return "Target Prospect & Revenue";
     }

     public function __construct()
     {
         $this->middleware('auth');
     }

     public function index()
     {
         $data['page_active'] ="fnr";
         $data['controller'] =$this->controller;
         $data['pages_title'] =$this->title();
         $data['n'] = 1;
         $data['r'] = 1;
         $data['total_month'] = "12";
         $data['month'] = array('', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember');
         $data['group'] = \App\Group::where('status', 'Y')->get();
         return view('admin.fnr')->with($data);
     }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        // dd($request);
        $data['tahun'] = $request->tahun;
        if($request->group == ''){
          $users = DB::table('tr_group_user')
          ->select('tr_group_user.id_users', 'tr_group_user.id_group', 'name')
          ->leftJoin('ek_users', 'tr_group_user.id_users', '=', 'ek_users.id_users')
          ->where('ek_users.status', 'Y')
          ->get();
        } else {
        $users = DB::table('tr_group_user')
          ->select('tr_group_user.id_users', 'tr_group_user.id_group', 'name')
          ->leftJoin('ek_users', 'tr_group_user.id_users', '=', 'ek_users.id_users')
          ->where('ek_users.status', 'Y')
          ->where('tr_group_user.id_group', $request->group)
          ->get();
        }

        // $users = \App\User::where('status', "Y")->orderBy('name', 'asc')->get();
        foreach ($users as $user) {
          for($x = 1; $x <= 12; $x++){
            $data['f'][$user->id_users]['c'.$x] = $request->input('f'.$user->id_users.'c'.$x);
            $data['r'][$user->id_users]['c'.$x] = $request->input('r'.$user->id_users.'c'.$x);

            $check = \App\Fnr::where('id_users', $user->id_users)->where('tahun', $data['tahun'])->where('bulan', $x)->where('fnr', "f")->count();

            if($check >= 1){
              \App\Fnr::where('id_users', $user->id_users)->where('tahun', $data['tahun'])->where('bulan', $x)->where('fnr', "f")
              ->update(['nominal' => $data['f'][$user->id_users]['c'.$x]]);
            } else {
              $isi = new \App\Fnr;
              $isi->id_users    = $user->id_users;
              $isi->tahun       = $data['tahun'];
              $isi->bulan       = $x;
              $isi->fnr         = "f";
              $isi->nominal     = $data['f'][$user->id_users]['c'.$x];
              $isi->save();
            }

            $check = \App\Fnr::where('id_users', $user->id_users)->where('tahun', $data['tahun'])->where('bulan', $x)->where('fnr', "r")->count();

            if($check >= 1){
              \App\Fnr::where('id_users', $user->id_users)->where('tahun', $data['tahun'])->where('bulan', $x)->where('fnr', "r")
              ->update(['nominal' => $data['r'][$user->id_users]['c'.$x]]);
            } else {
              $isi = new \App\Fnr;
              $isi->id_users    = $user->id_users;
              $isi->tahun       = $data['tahun'];
              $isi->bulan       = $x;
              $isi->fnr         = "r";
              $isi->nominal     = $data['r'][$user->id_users]['c'.$x];
              $isi->save();
            }
          }
        }
        // dd($data);
        // return $data['r']['K00002']['c1'];
        return redirect('dashboard/fnr');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
      // dd($request);
      if($request->group == ''){
        $users = DB::table('tr_group_user')
        ->select('tr_group_user.id_users', 'tr_group_user.id_group', 'name')
        ->leftJoin('ek_users', 'tr_group_user.id_users', '=', 'ek_users.id_users')
        ->where('ek_users.status', 'Y')
        ->get();
      } else {
      $users = DB::table('tr_group_user')
        ->select('tr_group_user.id_users', 'tr_group_user.id_group', 'name')
        ->leftJoin('ek_users', 'tr_group_user.id_users', '=', 'ek_users.id_users')
        ->where('ek_users.status', 'Y')
        ->where('tr_group_user.id_group', $request->group)
        ->get();
      }

      $fnr = \App\Fnr::select('id_users', 'bulan', 'fnr', 'nominal')
      ->where('tahun', $request->tahun)
      ->orderBy('fnr', 'asc')
      ->orderBy('bulan', 'asc')
      ->orderBy('id_users', 'asc')->get();

      $data = array('fnr' => $fnr, 'users' => $users);
      return response()->json($data);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Fnr  $fnr
     * @return \Illuminate\Http\Response
     */
    public function show(Fnr $fnr)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Fnr  $fnr
     * @return \Illuminate\Http\Response
     */
    public function edit(Fnr $fnr)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Fnr  $fnr
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Fnr $fnr)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Fnr  $fnr
     * @return \Illuminate\Http\Response
     */
    public function destroy(Fnr $fnr)
    {
        //
    }
}
