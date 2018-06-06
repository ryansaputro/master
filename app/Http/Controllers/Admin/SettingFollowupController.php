<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use App\SettingFollowup;
use App\Levelusers;
use Illuminate\Support\Facades\Auth;
use DB;
use App\User_role;
use App\Permission;
class SettingFollowupController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    private $controller = 'sett_followup';

    private function title(){
        return "List Setting Follow up";
    }

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {   

        $data['data_form'] = DB::table('ek_setting_calling')
        ->where('status','Y')
        ->orderBy('urutan','ASC')->get();

        
        $data['page_active'] ="sett_followup";
        $data['controller'] =$this->controller;
        $data['pages_title'] =$this->title();
        return view('admin.'.$this->controller.'.list')->with($data);
    }

    public function get_sett_followup(){
        $xxx = new SettingFollowup();
        $datax =$xxx->get_data()->get();
        $data = array();
        $no = 0;
        foreach ($datax as $datay) {
            $no++;
            $row = array();
            $row[] = $no;
            $row[] = $datay->status_name;
            $row[] = $datay->days_value." - Days";
            // $row[] = $datay->status_followed;
            $row[] = $datay->description;
            $option="<div class='hidden-sm hidden-xs action-buttons center'>";
            

            $user = User::where('id_users', '=', Auth::user()->id_users)->first();
            if($user->can(['sett_followup-edit'])){

                $id_status_call =$datay->id_status_call;

                if($id_status_call ==1 || $id_status_call ==2 || $id_status_call ==3 ||$id_status_call ==4 ||$id_status_call ==5){
                    $option .="";
                }else{
                    $option .="<a href='javascript:void(0)' onclick=edited('".$datay->id_status_call."') class='green'><i class='ace-icon fa fa-pencil bigger-130'></i></a>";
                }
            }

            if($user->can(['sett_followup-delete'])){

                if($id_status_call ==1 || $id_status_call ==2 || $id_status_call ==3 ||$id_status_call ==4 ||$id_status_call ==5){
                    $option .="";
                }else{
                    $option .="<a href='javascript:void(0)' onclick=removed('".$datay->id_status_call."') class='red'><i class='ace-icon fa fa-trash-o bigger-130'></i></a>";
                }
            }
           
            $option .="</div>";
            $row[] =$option;
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

        if($request->status_name == '')
        {
            $data['inputerror'][] = 'status_name';
            $data['error_string'][] = 'Status Name is required';
            $data['status'] = FALSE;
        }

        if($request->days_value == '')
        {
            $data['inputerror'][] = 'days_value';
            $data['error_string'][] = 'Days Value is required';
            $data['status'] = FALSE;
        }
        if($request->status_followed == '')
        {
            $data['inputerror'][] = 'status_followed';
            $data['error_string'][] = 'Status Followed is required';
            $data['status'] = FALSE;
        }



        if($request->description == '')
        {
            $data['inputerror'][] = 'description';
            $data['error_string'][] = 'Description is required';
            $data['status'] = FALSE;
        }

        if($data['status'] === FALSE)
        {
            echo json_encode($data);
            exit();
        }   
    }


    public function save_sett_followup(Request $request){
        SettingFollowupController::_validate_data($request);
        $status_name =$request->status_name;
        $days_value =$request->days_value;
        $status_followed =$request->status_followed;
        $description =$request->description;
        $pk = new SettingFollowup;
        $pk->status_name = $status_name;
        $pk->days_value = $days_value;
        $pk->status_followed = $status_followed;
        $pk->description =$description;
        $pk->created_by =Auth::user()->id_users;
        $pk->status ='Y';
        $pk->save();
        echo json_encode(array("status" => TRUE));
    }
    

    public function get_sett_followup_byid(Request $request){
        $id = $request->id;
        

        $data_form =DB::table('ek_setting_calling')->where('id_status_call',$id)->first();

        $data_return =array('data_form'=>$data_form);
        return response()->json($data_return);
    }

    public function update_sett_followup(Request $request){
        SettingFollowupController::_validate_data($request);

        $status_name =$request->status_name;
        $days_value =$request->days_value;
        $status_followed =$request->status_followed;
        $description =$request->description;

        $pk = SettingFollowup::find($request->id_status_call);
        $pk->status_name = $status_name;
        $pk->days_value = $days_value;
        $pk->status_followed = $status_followed;
        $pk->description = $description;
        $pk->updated_by =Auth::user()->id_users;
        $pk->save();
        echo json_encode(array("status" => TRUE));
    }

    public function deleted_sett_followup(Request $request){
        $pk = SettingFollowup::find($request->id);
        $pk->status ="N";
        $pk->updated_by =Auth::user()->id_users;
        $pk->save();
        $result=array(
                "data_post"=>array(
                    "status"=>TRUE,
                    "class" => "success",
                    "message"=>"Success ! Deleted data"
                )
            );
        echo json_encode($result);
    }
}
