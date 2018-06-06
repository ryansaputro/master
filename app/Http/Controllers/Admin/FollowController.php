<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use PeterColes\Countries\CountriesFacade;
use Calendar;
use DB;
use DateTime;
use App\User;
use App\FollowUp;
use App\Meeting;
use Illuminate\Support\Facades\Auth;
use URL;
use App\ProjectModel;
use App\Meeting_invit;
use Zizaco\Entrust\EntrustFacade as Entrust;


class FollowController extends Controller
{
    private $controller = 'follow_up';
    public function __construct()
    {
        $this->middleware('auth');
    }

    private function title(){
        return __('main.follow_up');
    }

    public function index(){
      $controller =$this->controller;
      $pages_title="Follow Up List";
      $page_active=$this->controller;
      return view('admin.'.$this->controller.'.list',compact('calendar','controller','page_active','pages_title'));
    }


    public function get_data(){
      $datax =new FollowUp;

      if(Entrust::can('all-data-follow-up')){

        $data_followed = $datax->get_data()->get();
      }else{
        $data_followed = $datax->get_data()->where('ek_follow_up.created_by',Auth::user()->id_users)->get();
      }
      
      // ek_follow_up
      $data = array();
      foreach ($data_followed as $res) {
        
          $row = array();
          $client ="<a href='javascript:void(0)' onclick='views_client(".$res->id.")' class='green'>".$res->nama_client."</a>";
          
          $row[] =$client." - ".$res->nama_project;
          
          $row[] =$res->follow_date;
          $row[] =$res->follow_date_next;
          if($res->status_name =='Meeting'){
            $meeting ="<a href='javascript:void(0)' onclick='views_meeting(".$res->id_follow_up.")' class='blue'>".$res->status_name."</a>";
            $row[] =$meeting;
          }else{
            $row[] =$res->status_name;
          }
          
          
          $number_of_contacts ="<a href='javascript:void(0)' onclick='views_followup_detail(".$res->project_id.")' class='btn btn-grey btn-xs'><i class='ace-icon fa fa-eye bigger-160'></i>".$res->number_of_contacts." X Follow-Up"."</a>";
          $row[] =$number_of_contacts;


          $edit ='';
          $addProcess='';
          $user = User::where('id_users', '=', Auth::user()->id_users)->first();

          $id=$res->id_follow_up;
          $date_follow_up=$res->follow_date_next;
          $date_now =date('Y-m-d');
          $date_fu=date_create($date_follow_up);
          $date_fu=date_format($date_fu,"Y-m-d");

          
                  if($date_now == $date_fu){
                      $addProcess= '<a href="'.LaravelLocalization::getLocalizedURL(LaravelLocalization::getCurrentLocale(),URL::to("dashboard/follow_up/".$res->id_project."/next")) .'" class="btn btn-xs btn-warning"><i class="glyphicon glyphicon-plus"></i>Followed Now</a>';
                  }elseif($date_now > $date_fu){
                      $addProcess= '<a href="'.LaravelLocalization::getLocalizedURL(LaravelLocalization::getCurrentLocale(),URL::to("dashboard/follow_up/".$res->id_project."/next")) .'" class="btn btn-xs btn-danger"><i class="glyphicon glyphicon-plus"></i>Late, Followed Now</a>';
                  }else{
                      $addProcess= '<a href="#" class="btn btn-xs btn-default"><i class="glyphicon glyphicon-plus"></i>Add Followed</a>';
                  }
              
                      
          $row[] =$addProcess;
          $data[] = $row;
      }

      $output = array(
                    "data" => $data,
                );
      return json_encode($output);

    }


    public function add(){

        $data['pages_title'] = __('main.add').' '.$this->title();
        $data['page_active'] =$this->controller;
        $data['countries'] = CountriesFacade::lookup(LaravelLocalization::getCurrentLocale());
        $data['controller']= $this->controller;

        if(Entrust::can('all-data-client')){
            $dataxy =DB::table("ek_client")
                    ->where('status','Y')
                    ->pluck("nama_client","id_client");
        }else{
            $dataxy =DB::table("ek_client")
                    ->where('status','Y')
                    ->where('created_by',Auth::user()->id_users)
                    ->pluck("nama_client","id_client");
        }

        $data['client'] = $dataxy;

        $data['setting_calling'] =DB::table('ek_setting_calling')
                            ->select('id_status_call','urutan','status_name','days_value','status_followed','description','project_ctg')
                            ->where('status','Y')
                            ->where('project_ctg','un_win')
                            ->orderBy('urutan','ASC')->get();
                            
        return view('admin.'.$this->controller.'.add')->with($data);
    }


    public function action_add(Request $request){

      $id_client = $request->id_client;
      $telp = $request->telp;
      $alamat = $request->alamat;
      $telp_date = $request->telp_date;
      $id_project = $request->id_project;
      $id_status_call = $request->id_status_call;
      $follow_date_next = $request->follow_date_next;
      $follow_date_next_booking = $request->follow_date_next_booking;
      $remarks = $request->remarks;
      $booking = $request->booking;
      $follow_date =date('Y-m-d h:i:s');
      $order_telp = DB::table('ek_follow_up')->where('id_project',$id_project)->count();
      $number_of_contacts =$order_telp +1;

      
      if($id_status_call == 3){
        //meeting 
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $kendaraan = $request->kendaraan;
        $supir = $request->supir;

        $budget = $request->budget;
        $b = str_replace( '.', '', $budget );
        if( is_numeric( $b ) ) {
            $budget = $b;
        }


        $follow_up = FollowUp::create([
            'number_of_contacts' =>$number_of_contacts,
            'id_client'=>$id_client,
            'id_project'=>$id_project,
            'id_status_call' =>$id_status_call,
            'follow_date' => $follow_date,
            'follow_date_next'  => $start_date,
            'followed_by'=>Auth::user()->id_users,
            'remarks'  => $remarks,
            'status_hide'  => 'Y',
            'created_by'  => Auth::user()->id_users,
        ]);

        $id_follow_up = $follow_up->id_follow_up;

        $meeting =Meeting::create([
          'id_project'=>$id_project,
          'id_follow_up'=>$id_follow_up,
          'id_kendaraan'=>$kendaraan,
          'id_supir'=>$supir,
          'start_meeting'=>$start_date,
          'end_meeting'=>$end_date,
          'budget_meeting'=>$budget,
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
      }else{
        //not meeting
        if($booking){
          //jika waktu di ddivert, maka ambil waktu sendiri
          $date_next = $follow_date_next_booking;
        }else{
          //ambil waktu berdasrkan setingan
          $date_next = $follow_date_next;
        }

        FollowUp::create([
            'number_of_contacts' =>$number_of_contacts,
            'id_client'=>$id_client,
            'id_project'=>$id_project,
            'id_status_call' =>$id_status_call,
            'follow_date' => $follow_date,
            'follow_date_next'  => $date_next,
            'followed_by'=>Auth::user()->id_users,
            'remarks'  => $remarks,
            'status_hide'  => 'Y',
            'created_by'  => Auth::user()->id_users,
        ]);
      }

      $pk = ProjectModel::find($id_project);        
      $pk->status_followed ='Y';
      $pk->updated_by =Auth::user()->id_users;
      $pk->save();

      return redirect(LaravelLocalization::getCurrentLocale().'/dashboard/'.$this->controller)->with('status', __( 'main.data_has_been_added', ['page' => $this->title()] ) );

    }


    public function next($id){
      $data['pages_title'] = 'Next '.$this->title();
      $data['page_active'] =$this->controller;
      $data['countries'] = CountriesFacade::lookup(LaravelLocalization::getCurrentLocale());
      $data['controller']= $this->controller;
      

      $data['setting_calling'] =DB::table('ek_setting_calling')
                          ->select('id_status_call','urutan','status_name','days_value','status_followed','description','project_ctg')
                          ->where('status','Y')
                          ->where('project_ctg','un_win')
                          ->orderBy('urutan','ASC')->get();

      $rest_data =new ProjectModel;
      $data['client'] =$rest_data->get_data_client_on_project($id)->first();


      return view('admin.'.$this->controller.'.next')->with($data);
    }


    public function action_next(Request $request){

      $id_client = $request->id_client;
      $telp = $request->telp;
      $alamat = $request->alamat;
      $telp_date = $request->telp_date;
      $id_project = $request->id_project;
      $id_status_call = $request->id_status_call;
      $follow_date_next = $request->follow_date_next;
      $follow_date_next_booking = $request->follow_date_next_booking;
      $remarks = $request->remarks;
      $booking = $request->booking;
      $follow_date =date('Y-m-d h:i:s');

      $order_telp = DB::table('ek_follow_up')->where('id_project',$id_project)->count();
      $number_of_contacts =$order_telp +1;
      DB::table('ek_follow_up')->where('id_project',$id_project)->update(array('status_hide'=>'N','updated_by'=>Auth::user()->id_users));


      if($id_status_call == 3){
        //meeting 
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $kendaraan = $request->kendaraan;
        $supir = $request->supir;

        $budget = $request->budget;
        $b = str_replace( '.', '', $budget );
        if( is_numeric( $b ) ) {
            $budget = $b;
        }
        $follow_up = FollowUp::create([
            'number_of_contacts' =>$number_of_contacts,
            'id_client'=>$id_client,
            'id_project'=>$id_project,
            'id_status_call' =>$id_status_call,
            'follow_date' => $follow_date,
            'follow_date_next'  => $start_date,
            'followed_by'=>Auth::user()->id_users,
            'remarks'  => $remarks,
            'status_hide'  => 'Y',
            'created_by'  => Auth::user()->id_users,
        ]);

        $id_follow_up = $follow_up->id_follow_up;
        $meeting=Meeting::create([
          'id_project'=>$id_project,
          'id_follow_up'=>$id_follow_up,
          'id_kendaraan'=>$kendaraan,
          'id_supir'=>$supir,
          'start_meeting'=>$start_date,
          'end_meeting'=>$end_date,
          'budget_meeting'=>$budget,
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

      }else{
        if($booking){
          $date_next = $follow_date_next_booking;
        }else{
          $date_next = $follow_date_next;
        }
        
        FollowUp::create([
            'number_of_contacts'   =>$number_of_contacts,
            'id_client'=>$id_client,
            'id_project'=>$id_project,
            'id_status_call' =>$id_status_call,
            'follow_date' => $follow_date,
            'follow_date_next'  => $date_next,
            'followed_by'=>Auth::user()->id_users,
            'remarks'  => $remarks,
            'status_hide'  => 'Y',
            'created_by'  => Auth::user()->id_users,
        ]);
      }

      return redirect(LaravelLocalization::getCurrentLocale().'/dashboard/'.$this->controller)->with('status', __( 'main.data_has_been_follow_up', ['page' => $this->title()] ) );
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

    public function save_reschedule(Request $request){
      FollowController::_validate_data($request);


        $id_follow_up = $request->id_follow_up;
        $start_meeting = $request->start_meeting;
        $end_meeting = $request->end_meeting;
        $kendaraan = $request->kendaraan;
        $supir = $request->supir;
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


        DB::table('ek_meeting')->where('id_follow_up',$id_follow_up)->update($data);


        

        $invite_users=explode(',',$request->invite_users);
        $id_meeting = $request->id_meeting;
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
}
