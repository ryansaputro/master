<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use PeterColes\Countries\CountriesFacade;
use Calendar;
use DB;
use DateTime;
use URL;
use App\ProjectModel;
use App\Meeting;
use App\Meeting_invit;
use App\FollowUp;
use App\User;
use Illuminate\Support\Facades\Auth;
use Zizaco\Entrust\EntrustFacade as Entrust;

class ProjectwinController extends Controller
{
    private $controller = 'project_win';
    public function __construct()
    {
        $this->middleware('auth');
    }

    private function title(){
        return __('main.project_win');
    }

    public function index(){
      
      $controller =$this->controller;
      $pages_title="Project Win List";
      $page_active=$this->controller;
      return view('admin.'.$this->controller.'.list',compact('calendar','controller','page_active','pages_title'));
    }


    public function get_project_win(){
      $datax =new FollowUp;
      

      if(Entrust::can('all-data-win-project')){
        $data_followed = $datax->get_data_win()->get();
      }else{
        $data_followed = $datax->get_data_win()->where('ek_follow_up.created_by',Auth::user()->id_users)->get();
      }
      $data = array();
      foreach ($data_followed as $res) {
        
          $row = array();
          $client ="<a href='javascript:void(0)' onclick='views_client(".$res->id.")' class='green'>".$res->nama_client."</a>";
          $row[] =$client."-".$res->nama_project;
        
          
          $number_of_contacts ="<a href='javascript:void(0)' onclick='views_followup_detail(".$res->project_id.")' class='btn btn-grey btn-xs'><i class='ace-icon fa fa-eye bigger-160'></i>".$res->number_of_contacts." X Follow-Up"."</a>";
          $row[] =$number_of_contacts;



          $count_meeting =Meeting::where('id_project',$res->id_project)
                                  ->where('type_meeting','win')->count();


          $meeting_list ="<a href='javascript:void(0)' onclick='views_meeting_detail(".$res->id_follow_up.")' class='btn btn-grey btn-xs'><i class='ace-icon fa fa-eye bigger-160'></i>".$count_meeting." X Meeting"."</a>";;


          $row[] =$meeting_list;

          $edit ='';
          $addProcess='';
          $user = User::where('id_users', '=', Auth::user()->id_users)->first();

          $id=$res->id_follow_up;
          $date_follow_up=$res->follow_date_next;
          $date_now =date('Y-m-d');
          $date_fu=date_create($date_follow_up);
          $date_fu=date_format($date_fu,"Y-m-d");


          // $count_meeting_active =Meeting::where('id_project',$res->id_project)
                                  // ->where('type_meeting','win')
                                  // ->where('status_meeting','Y');

          // if($count_meeting_active->count() >= 1){
          //   $rest_meeting = $count_meeting_active->first();

          //     $meeting ="<a href='javascript:void(0)' onclick='views_meeting(".$rest_meeting->id_meeting.")' class='blue'>".$rest_meeting->start_meeting."</a>";


          //   $addProcess = $meeting;
          //   $addProcess= "<a href='javascript:void(0)' onclick='add_meeting(".$res->id_follow_up.")' class='btn btn-xs btn-warning'><i class='glyphicon glyphicon-plus'></i>Add Meeting</a>";
            
          // }else{
          //    $addProcess= "<a href='javascript:void(0)' onclick='add_meeting(".$res->id_follow_up.")' class='btn btn-xs btn-warning'><i class='glyphicon glyphicon-plus'></i>Add Meeting</a>";
          // }
          $addProcess= "<a href='javascript:void(0)' onclick='add_meeting(".$res->id_follow_up.")' class='btn btn-xs btn-warning'><i class='glyphicon glyphicon-plus'></i>Add Meeting</a> - ";

          $addProcess.= " <a href='javascript:void(0)' onclick='end_process(".$res->id_follow_up.")' class='btn btn-xs btn-danger'><i class='glyphicon glyphicon-remove'></i>End Proccess</a>";
                      
          $row[] =$addProcess;
          
          $data[] = $row;
      }

      $output = array(
                    "data" => $data,
                );
      return json_encode($output);
    }


    private function _validate_data(Request $request){
        $data = array();
        $data['error_string'] = array();
        $data['inputerror'] = array();
        $data['status'] = TRUE;



        if($request->start_meeting == '')
        {
            $data['inputerror'][] = 'start_meeting';
            $data['error_string'][] = 'Start Meeting is required';
            $data['status'] = FALSE;
        }

        if($request->end_meeting == '')
        {
            $data['inputerror'][] = 'end_meeting';
            $data['error_string'][] = 'End Meeting is required';
            $data['status'] = FALSE;
        }

        if($request->kendaraan == '')
        {
            $data['inputerror'][] = 'kendaraan';
            $data['error_string'][] = 'Vehicle is required';
            $data['status'] = FALSE;
        }

        if($request->supir == '')
        {
            $data['inputerror'][] = 'supir';
            $data['error_string'][] = 'Driver is required';
            $data['status'] = FALSE;
        }

        if($request->budget == '')
        {
            $data['inputerror'][] = 'budget';
            $data['error_string'][] = 'End Meeting is required';
            $data['status'] = FALSE;
        }

        if($data['status'] === FALSE)
        {
            echo json_encode($data);
            exit();
        }   
    }


    public function add_meeting(Request $request){

      ProjectwinController::_validate_data($request);

      $id_follow_up = $request->id_follow_up;
      $id_project =$request->id_project;
      $start_meeting = $request->start_meeting;
      $end_meeting = $request->end_meeting;
      $kendaraan = $request->kendaraan;
      $supir = $request->supir;
      $budget = $request->budget;
      $description = $request->description;

      $budget = $request->budget;
      $b = str_replace( '.', '', $budget );
      if( is_numeric( $b ) ) {
          $budget = $b;
      }

      $ps = FollowUp::find($id_follow_up);
      $ps->status_meeting_on_win ='Y';
      $ps->updated_by =Auth::user()->id_users;
      $ps->save();




      $meeting=Meeting::create([
        'id_project'=>$id_project,
        'id_follow_up'=>$id_follow_up,
        'id_kendaraan'=>$kendaraan,
        'id_supir'=>$supir,
        'start_meeting'=>$start_meeting,
        'end_meeting'=>$end_meeting,
        'description'=>$description,
        'budget_meeting'=>$budget,
        'type_meeting'=>'win',
        'status_meeting'=>'Y',
        'created_by'=>Auth::user()->id_users,
        'status'=>'Y',
      ]);

      //save to invite meeting
      $invite_users=explode(',',$request->invite_users);
      $id_meeting = $meeting->id_meeting;
      foreach ($invite_users as $key => $value) {
        if(!empty($value)){
            Meeting_invit::create([
              'id_users'=>$value,
              'id_meeting'=>$id_meeting,
              'created_by'=>Auth::user()->id_users,
              'status'=>'Y',
            ]);
        }
        
      }


      echo json_encode(array("status" => TRUE));


    }

    public function meeting_reschedule(Request $request){
      ProjectwinController::_validate_data($request);
      $id_meeting =$request->id_meeting;
      $id_follow_up = $request->id_follow_up;
      $start_meeting = $request->start_meeting;
      $end_meeting = $request->end_meeting;
      $kendaraan = $request->kendaraan;
      $supir = $request->supir;
      $budget = $request->budget;

      $budget = $request->budget;
      $b = str_replace( '.', '', $budget );
      if( is_numeric( $b ) ) {
          $budget = $b;
      }

      $pk = FollowUp::find($id_follow_up);
      $pk->follow_date_next = $start_meeting;
      $pk->updated_by =Auth::user()->id_users;
      $pk->save();


      $data=array(
            'id_kendaraan'=>$kendaraan,
            'id_supir'=>$supir,
            'start_meeting'=>$start_meeting,
            'end_meeting'=>$end_meeting,
            'budget_meeting'=>$budget,
            'updated_by'=>Auth::user()->id_users
          );
      DB::table('ek_meeting')->where('id_meeting',$id_meeting)->update($data);

      $invite_users=explode(',',$request->invite_users);

      DB::table("ek_meeting_invitation")->where("id_meeting",$id_meeting)->delete();
      foreach ($invite_users as $key => $value) {
        if(!empty($value)){
            Meeting_invit::create([
              'id_users'=>$value,
              'id_meeting'=>$id_meeting,
              'created_by'=>Auth::user()->id_users,
              'status'=>'Y',
            ]);
        }
      }


      echo json_encode(array("status" => TRUE));

    }


    public function get_data_followup_byid(Request $request){
      $id =$request->id;
      $datax =new FollowUp;
      $data_followed = $datax->get_data_win()->where('id_follow_up',$id)->first();

      $data=array('data_followup'=>$data_followed);
      return response()->json($data);
    }

    public function act_end_process(Request $request){

      $id_follow_up = $request->id_follow_up;
      $id_status_call= $request->id_status_call;

      if(empty($id_status_call)){
        echo json_encode(array("status"=>"failed","message"=>"Status Project Is Required"));
      }else{
        //check meeting actived

        $check_meeting = DB::table('ek_meeting')
                          ->where('id_follow_up',$id_follow_up)
                          ->where('status_meeting','Y')->get();
        $meeting= count($check_meeting);
        if(count($check_meeting) >=1){
          echo json_encode(array("status"=>"failed","message"=>"There are ".$meeting." active meetings"));
        }else{
          //bisa melakukan end project
          //update win jadi finish or failed
          echo json_encode(array("status"=>"success","message"=>"Success ! Finished Project"));

        }


      }


      
      // echo json_encode(array("status" =>$request->id_follow_up."-".$id_status_call));

    }
}
