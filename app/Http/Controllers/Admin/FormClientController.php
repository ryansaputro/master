<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use App\Levelusers;
use Illuminate\Support\Facades\Auth;
use DB;
use App\FormClient;
class FormClientController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    private $controller = 'form_client';

    private function title(){
        return "Setting Form Client";
    }

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {   
        $data['page_active'] ="form_client";
        $data['controller'] =$this->controller;
        $data['pages_title'] =$this->title();

        $data['data_form'] = DB::table('ek_form_client')
        ->where('status','Y')
        ->whereNotIn('name_form',['client-name','phone','address'])
        ->orderBy('urutan','ASC')->get();

        $data['dt_level'] = DB::table('roles')->get();
        return view('admin.'.$this->controller.'.list')->with($data);
    }

    public function get_form_clinet(){
        $user = new User();
        $data_form =DB::table('ek_form_client')
        			->where('status','Y')
        			->orderBy('urutan','asc')->get();
        $data = array();
        $no = 0;
        foreach ($data_form as $form) {
            $no++;
            $row = array();
            $row[] = $no;
            $row[] = $form->label_form;
            $row[] = $form->name_form;
            $row[] = $form->type;
            $required =$form->required;
            if($required =='Y'){
                $row[]  =$required ="Required";
            }else{
                $row[] = $required ="Not Required";

            }
            $option="<div class='hidden-sm hidden-xs action-buttons center'>";
            
            $user = User::where('id_users', '=', Auth::user()->id_users)->first();
            if($user->can(['form_client-edit'])){

                if($form->id_form_client ==1 || $form->id_form_client ==2 || $form->id_form_client ==3 || $form->id_form_client ==4 ){
                    $option .="";
                }else{
                    $option .="<a href='javascript:void(0)' onclick=edited('".$form->id_form_client."') class='green'><i class='ace-icon fa fa-pencil bigger-130'></i></a>";
                }
                
                
            }
            if($user->can(['form_client-delete'])){
                if($form->id_form_client ==1 || $form->id_form_client ==2 || $form->id_form_client ==3 || $form->id_form_client ==4 ){
                    $option .="";
                }else{
                    $option .="<a href='javascript:void(0)' onclick=removed('".$form->id_form_client."') class='red'><i class='ace-icon fa fa-trash-o bigger-130'></i></a>";
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



        if($request->label_form == '')
        {
            $data['inputerror'][] = 'label_form';
            $data['error_string'][] = 'Label is required';
            $data['status'] = FALSE;
        }

        if($request->name_form == '')
        {
            $data['inputerror'][] = 'name_form';
            $data['error_string'][] = 'Name is required';
            $data['status'] = FALSE;
        }
        if($request->type == '')
        {
            $data['inputerror'][] = 'type';
            $data['error_string'][] = 'Type is required';
            $data['status'] = FALSE;
        }

        if($data['status'] === FALSE)
        {
            echo json_encode($data);
            exit();
        }   
    }

    public function save_form_clinet(Request $request){

        FormClientController::_validate_data($request);


        if($request->mandatory =='Y'){
            $required ='Y';
        }else{
            $required='N';
        }
        
        $pk = new FormClient;
        $pk->label_form = $request->label_form;
        $pk->name_form = $request->name_form;
        $pk->type = $request->type;
        $pk->required = $required;
        $pk->created_by =Auth::user()->id_users;
        $pk->status ='Y';
        $pk->urutan =0;
        $pk->save();

        echo json_encode(array("status" => TRUE));
    }


    public function update_form_clinet(Request $request){
        FormClientController::_validate_data($request);

        if($request->mandatory =='Y'){
            $required ='Y';
        }else{
            $required='N';
        }

        $pk = FormClient::find($request->id_form_client);
        $pk->label_form = $request->label_form;
        $pk->name_form = $request->name_form;
        $pk->type = $request->type;
        $pk->required = $required;
        $pk->updated_by =Auth::user()->id_users;
        $pk->save();
        echo json_encode(array("status" => TRUE));
    }

    public function deleted_form_clinet(Request $request){
        $pk = FormClient::find($request->id);
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

    public function get_form_clinet_byid(Request $request){
        $id = $request->id;
        $FormClient = new FormClient();

        $data_form =DB::table('ek_form_client')->where('id_form_client',$id)->first();

        $data_return =array('data_form'=>$data_form);
        return response()->json($data_return);
    }
}
