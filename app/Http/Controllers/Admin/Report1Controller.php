<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Charts;
use App\Fnr;

class Report1Controller extends Controller
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
   private $controller = 'report1';

   private function title(){
       return "Report";
   }

   public function __construct()
   {
       $this->middleware('auth');
   }

   public function index(){
    $data['page_active'] ="report1";
    $data['controller'] =$this->controller;
    $data['pages_title'] =$this->title();

    $data['group'] = \App\Group::where('status', 'Y')->get();
    $data['users'] = \App\User::where('status', 'Y')->get();
    return view('admin.report_target.report1')->with($data);
  }
  public function report1(Request $request){
    // dd($request);

    $tahun = $request->tahun;
    $group = $request->group;
    $users = $request->users;

    $fnr = new Fnr;
    if($group == ''){
      if($users == ''){
        $data = "group & users no";
        for($x = 1; $x <= 12; $x++){
          $hasil['f'][$x] = $fnr->select('nominal')->where('bulan', $x)->where('tahun', $tahun)->where('fnr', 'f')->sum('nominal');
          $hasil['r'][$x] = $fnr->select('nominal')->where('bulan', $x)->where('tahun', $tahun)->where('fnr', 'r')->sum('nominal');
        }
      } else {
        $data = "users oke";
        $users = DB::table('ek_users')
        ->where('id_users', $users)
        ->get();
        foreach ($users as $user) {
          for($x = 1; $x <= 12; $x++){
            $hasil['f'][$x] = $fnr->select('nominal')->where('bulan', $x)->where('tahun', $tahun)->where('fnr', 'f')->where('id_users', $user->id_users)->sum('nominal');
            $hasil['r'][$x] = $fnr->select('nominal')->where('bulan', $x)->where('tahun', $tahun)->where('fnr', 'r')->where('id_users', $user->id_users)->sum('nominal');
          }
        }
      }
    } else {
      $data = "group oke";
      $users = DB::table('tr_group_user')
      ->select('tr_group_user.id_users', 'tr_group_user.id_group', 'name')
      ->leftJoin('ek_users', 'tr_group_user.id_users', '=', 'ek_users.id_users')
      ->where('ek_users.status', 'Y')
      ->where('tr_group_user.id_group', $group)
      ->get();
      // dd($users);
      foreach ($users as $user) {
        for($x = 1; $x <= 12; $x++){
          $hasil['f'][$x] = $fnr->select('nominal')->where('bulan', $x)->where('tahun', $tahun)->where('fnr', 'f')->where('id_users', $user->id_users)->sum('nominal');
          $hasil['r'][$x] = $fnr->select('nominal')->where('bulan', $x)->where('tahun', $tahun)->where('fnr', 'r')->where('id_users', $user->id_users)->sum('nominal');
        }
      }
    }

    $data = array('isi' => $hasil);
    return response()->json($data);
  }

  public function index2(){
    $data['group'] = \App\Group::where('status', 'Y')->get();
    $data['users'] = \App\User::where('status', 'Y')->get();
    $data['page_active'] ="report2";
    $data['controller'] =$this->controller;
    $data['pages_title'] =$this->title();

    return view('admin.report_target.report2')->with($data);}

 public function report2(Request $request){

   $tahun = $request->tahun;
   $group = $request->group;
   $users = $request->users;

   $fnr = new Fnr;
   if($group == ''){
     if($users == ''){
       $data = "group & users no";
       for($x = 1; $x <= 12; $x++){
         $hasil['f'][$x] = $fnr->select('nominal')->where('bulan', $x)->where('tahun', $tahun)->where('fnr', 'f')->sum('nominal');
         // $hasil['r'][$x] = $fnr->select('nominal')->where('bulan', $x)->where('tahun', $tahun)->where('fnr', 'r')->sum('nominal');
       }
     } else {
       $data = "users oke";
       $users = DB::table('ek_users')
       ->where('id_users', $users)
       ->get();
       foreach ($users as $user) {
         for($x = 1; $x <= 12; $x++){
           $hasil['f'][$x] = $fnr->select('nominal')->where('bulan', $x)->where('tahun', $tahun)->where('fnr', 'f')->where('id_users', $user->id_users)->sum('nominal');
           // $hasil['r'][$x] = $fnr->select('nominal')->where('bulan', $x)->where('tahun', $tahun)->where('fnr', 'r')->where('id_users', $user->id_users)->sum('nominal');
         }
       }
     }
   } else {
     $data = "group oke";
     $users = DB::table('tr_group_user')
     ->select('tr_group_user.id_users', 'tr_group_user.id_group', 'name')
     ->leftJoin('ek_users', 'tr_group_user.id_users', '=', 'ek_users.id_users')
     ->where('ek_users.status', 'Y')
     ->where('tr_group_user.id_group', $group)
     ->get();
     // dd($users);
     foreach ($users as $user) {
       for($x = 1; $x <= 12; $x++){
         $hasil['f'][$x] = $fnr->select('nominal')->where('bulan', $x)->where('tahun', $tahun)->where('fnr', 'f')->where('id_users', $user->id_users)->sum('nominal');
         // $hasil['r'][$x] = $fnr->select('nominal')->where('bulan', $x)->where('tahun', $tahun)->where('fnr', 'r')->where('id_users', $user->id_users)->sum('nominal');
       }
     }
   }

    $n = 1;
    for($x = 1; $x <= 12; $x++){

    if($x <= 9){ $n = '0'.$x; } else { $n = $x; }
    $project = DB::select(
    "select sum(nominal) as nominalx from (
    select nominal, status, created_at, SUBSTRING(created_at, 1, 4) as tahun, SUBSTRING(created_at, 6, 2) as bulan from ek_project) as innerTable
    where bulan = '".$n."' AND tahun = ".$tahun);
    $p = $project[0]->nominalx;
    if($p == null){ $p = 0; } else { $p = $p; }
    $hasil['n'][$x] = $p;
    $n++;
    }
    // dd($hasil);

    $data = array('isi' => $hasil);
    return response()->json($data);
  }

   public function index3(){
     $data['group'] = \App\Group::where('status', 'Y')->get();
     $data['users'] = \App\User::where('status', 'Y')->get();
     $data['page_active'] ="report3";
     $data['controller'] =$this->controller;
     $data['pages_title'] =$this->title();

     return view('admin.report_target.report3')->with($data);
   }

   public function report3(Request $request){

     $tahun = $request->tahun;
     $group = $request->group;
     $users = $request->users;

     $fnr = new Fnr;
     if($group == ''){
       if($users == ''){
         $data = "group & users no";
         for($x = 1; $x <= 12; $x++){
           // $hasil['f'][$x] = $fnr->select('nominal')->where('bulan', $x)->where('tahun', $tahun)->where('fnr', 'f')->sum('nominal');
           $hasil['r'][$x] = $fnr->select('nominal')->where('bulan', $x)->where('tahun', $tahun)->where('fnr', 'r')->sum('nominal');
         }
       } else {
         $data = "users oke";
         $users = DB::table('ek_users')
         ->where('id_users', $users)
         ->get();
         foreach ($users as $user) {
           for($x = 1; $x <= 12; $x++){
             // $hasil['f'][$x] = $fnr->select('nominal')->where('bulan', $x)->where('tahun', $tahun)->where('fnr', 'f')->where('id_users', $user->id_users)->sum('nominal');
             $hasil['r'][$x] = $fnr->select('nominal')->where('bulan', $x)->where('tahun', $tahun)->where('fnr', 'r')->where('id_users', $user->id_users)->sum('nominal');
           }
         }
       }
     } else {
       $data = "group oke";
       $users = DB::table('tr_group_user')
       ->select('tr_group_user.id_users', 'tr_group_user.id_group', 'name')
       ->leftJoin('ek_users', 'tr_group_user.id_users', '=', 'ek_users.id_users')
       ->where('ek_users.status', 'Y')
       ->where('tr_group_user.id_group', $group)
       ->get();
       // dd($users);
       foreach ($users as $user) {
         for($x = 1; $x <= 12; $x++){
           // $hasil['f'][$x] = $fnr->select('nominal')->where('bulan', $x)->where('tahun', $tahun)->where('fnr', 'f')->where('id_users', $user->id_users)->sum('nominal');
           $hasil['r'][$x] = $fnr->select('nominal')->where('bulan', $x)->where('tahun', $tahun)->where('fnr', 'r')->where('id_users', $user->id_users)->sum('nominal');
         }
       }
     }

    $n = 1;
    for($x = 1; $x <= 12; $x++){
      if($x <= 9){ $n = '0'.$x; } else { $n = $x; }
      $pn = 0;

      $project = DB::table('ek_project')->select('id_project')->get();
      foreach ($project as $key => $value) {
        $sql = "
        SELECT
        	sum( nominal ) as nominalx
        FROM
        	(
        SELECT
        	nominal,
        	a.created_at,
        	SUBSTRING( b.created_at, 1, 4 ) as tahun,
        	SUBSTRING( b.created_at, 6, 2 ) as bulan
        FROM
        	ek_project as a
        	INNER JOIN ek_follow_up as b ON b.id_project = a.id_project
        WHERE
        	a.id_project = '".$value->id_project."'
        	AND
        	b.id_status_call = 1
        ORDER BY
        	b.created_at DESC
        	LIMIT 1
        	) as innerTable
        WHERE bulan = '".$n."' AND tahun = '".$tahun."'
        ";
        $projectx = DB::select($sql);
        $project_tmp[$pn] = $projectx[0]->nominalx;
        $pn++;
      }
      $p = array_sum($project_tmp);
      if($p == null){ $p = 0; } else { $p = $p; }
      $hasil['n'][$x] = $p;
      $n++;
    }
    // dd($hasil);
    $data = array('isi' => $hasil);
    return response()->json($data);
   }
   public function index4(){
     $data['page_active'] ="report4";
     $data['controller'] =$this->controller;
     $data['pages_title'] =$this->title();

     return view('admin.report_target.report4')->with($data);
   }

   public function report4(Request $request){

     $tahun     = $request->tahun;
     $group     = $request->group;
     $users     = $request->users;
     $nominal   = $request->nominal;

     // $fnr = new Fnr;

     $n = 1;
     for($x = 1; $x <= 12; $x++){
       if($x <= 9){ $n = '0'.$x; } else { $n = $x; }

       $follow = DB::table('ek_setting_calling')->select('id_status_call', 'status_name')->get();
       foreach ($follow as $keys => $values) {

         $pn = 0;
         $project = DB::table('ek_project')->select('id_project')->get();
         foreach ($project as $key => $value) {
           $sql = "
           SELECT
           	sum( nominal ) as nominalx
           FROM
           	(
           SELECT
           	nominal,
           	a.created_at,
           	SUBSTRING( b.created_at, 1, 4 ) as tahun,
           	SUBSTRING( b.created_at, 6, 2 ) as bulan
           FROM
           	ek_project as a
           	INNER JOIN ek_follow_up as b ON b.id_project = a.id_project
           WHERE
           	a.id_project = '".$value->id_project."'
           	AND
           	b.id_status_call = '".$values->id_status_call."'
           ORDER BY
           	b.created_at DESC
           	LIMIT 1
           	) as innerTable
           WHERE bulan = '".$n."' AND tahun = '".$tahun."'
           ";
           // print $sql."<br>";
           $projectx = DB::select($sql);
           $project_tmp[$pn] = $projectx[0]->nominalx;

           $sqlx = "
           SELECT
           	*
           FROM
           	(
           SELECT
           	nominal,
           	a.created_at,
           	SUBSTRING( b.created_at, 1, 4 ) as tahun,
           	SUBSTRING( b.created_at, 6, 2 ) as bulan
           FROM
           	ek_project as a
           	INNER JOIN ek_follow_up as b ON b.id_project = a.id_project
           WHERE
           	b.id_status_call = '".$values->id_status_call."'
           ORDER BY
           	b.created_at DESC
           	) as innerTable
           WHERE bulan = '".$n."' AND tahun = '".$tahun."'
           ";

           $project_cTmp = DB::select($sqlx);
           $pc = 0;
           foreach ($project_cTmp as $key => $value) {
             $pc++;
           }
           $pn++;
         }
         $p = array_sum($project_tmp);
         if($p == null){ $p = 0; } else { $p = $p; }

         if($nominal == "1"){
           $hasil["x".$values->id_status_call][$x] = $p;
         } else {
           $hasil["x".$values->id_status_call][$x] = $pc;
         }
         $hasil["x".$values->id_status_call][13] = $values->status_name;
         $p = 0;
       }
     }
     // dd($hasil);
     $data = array('isi' => $hasil);
     return response()->json($data);
   }

   public function index5(){
     $data['page_active'] ="report5";
     $data['controller'] =$this->controller;
     $data['pages_title'] =$this->title();

     return view('admin.report_target.report5')->with($data);
   }

   public function report5(Request $request){

      $tahun     = $request->tahun;
      $group     = $request->group;
      $users     = $request->users;

      $fnr = new Fnr;

      $n = 1;
      for($x = 1; $x <= 12; $x++){
        $hasil['n'][$x] = $fnr->where('bulan', $x)->where('tahun', $tahun)->where('fnr', 'r')->where('nominal', '!=', '0')->count();
        if($x <= 9){ $n = '0'.$x; } else { $n = $x; }
        $pn = 0;

        $project = DB::table('ek_project')->select('id_project')->get();
        foreach ($project as $key => $value) {
          $sql = "
          SELECT
          	sum( nominal ) as nominalx
          FROM
          	(
          SELECT
          	nominal,
          	a.created_at,
          	SUBSTRING( b.created_at, 1, 4 ) as tahun,
          	SUBSTRING( b.created_at, 6, 2 ) as bulan
          FROM
          	ek_project as a
          	INNER JOIN ek_follow_up as b ON b.id_project = a.id_project
          WHERE
          	a.id_project = '".$value->id_project."'
          	AND
          	b.id_status_call = 1
          ORDER BY
          	b.created_at DESC
          	LIMIT 1
          	) as innerTable
          WHERE bulan = '".$n."' AND tahun = '".$tahun."'
          ";
          $projectx = DB::select($sql);
          $project_tmp[$pn] = $projectx[0]->nominalx;
          $pn++;
        }
        $p = array_sum($project_tmp);
        if($p == null){ $p = 0; } else { $p = $p; }
        $hasil['r'][$x] = $p;
        $n++;
      }
      // dd($hasil);
      $data = array('isi' => $hasil);
      return response()->json($data);
   }
}
