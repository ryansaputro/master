<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use App\Levelusers;
use Illuminate\Support\Facades\Auth;
use DB;
use App\FormClient;
use App\Driver;
class DriverController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    private $controller = 'driver';

    private function title(){
        return "Driver List";
    }

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {   
        if (!Auth::user()->can($this->controller.'-list')){
            return view('errors.403')->with(['url' => '/admin']);

        }
        $data['page_active'] ="driver";
        $data['controller'] =$this->controller;
        $data['pages_title'] =$this->title();
        return view('admin.'.$this->controller.'.list')->with($data);
    }

    public function get_driver(){
        $user = new User();
        $data_form =DB::table('ek_supir')
        			->where('status','Y')->get();
        $data = array();
        $no = 0;
        foreach ($data_form as $form) {
            $no++;
            $row = array();
            $row[] = $no;
            $row[] = $form->nama_supir;
            $row[] = $form->telp;
            $row[] = $form->email;
            $option="<div class='hidden-sm hidden-xs action-buttons center'>";
            
            $user = User::where('id_users', '=', Auth::user()->id_users)->first();
            if($user->can(['driver-edit'])){

                
                $option .="<a href='javascript:void(0)' onclick=edited('".$form->id_supir."') class='green'><i class='ace-icon fa fa-pencil bigger-130'></i></a>";
                
            }
            if($user->can(['driver-delete'])){
                
                $option .="<a href='javascript:void(0)' onclick=removed('".$form->id_supir."') class='red'><i class='ace-icon fa fa-trash-o bigger-130'></i></a>";
                
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



        if($request->nama_supir == '')
        {
            $data['inputerror'][] = 'nama_supir';
            $data['error_string'][] = 'Driver Name is required';
            $data['status'] = FALSE;
        }

        if($request->telp == '')
        {
            $data['inputerror'][] = 'telp';
            $data['error_string'][] = 'telp is required';
            $data['status'] = FALSE;
        }
        if($request->email == '')
        {
            $data['inputerror'][] = 'email';
            $data['error_string'][] = 'Email is required';
            $data['status'] = FALSE;
        }

        if($data['status'] === FALSE)
        {
            echo json_encode($data);
            exit();
        }   
    }

    public function save_driver(Request $request){
        DriverController::_validate_data($request);
        $pk = new Driver;
        $pk->nama_supir = $request->nama_supir;
        $pk->telp = $request->telp;
        $pk->email = $request->email;
        $pk->created_by =Auth::user()->id_users;
        $pk->status ='Y';
        $pk->save();
        echo json_encode(array("status" => TRUE));
    }


    public function update_driver(Request $request){
        DriverController::_validate_data($request);

        $pk = Driver::find($request->id_supir);
        $pk->nama_supir = $request->nama_supir;
        $pk->telp = $request->telp;
        $pk->email = $request->email;
        $pk->updated_by =Auth::user()->id_users;
        $pk->save();
        echo json_encode(array("status" => TRUE));
    }

    public function deleted_driver(Request $request){
        $pk = Driver::find($request->id);
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

    public function get_driver_byid(Request $request){
        $id = $request->id;

        $data_form =DB::table('ek_supir')->where('id_supir',$id)->first();

        $data_return =array('data_form'=>$data_form);
        return response()->json($data_return);
    }
}
