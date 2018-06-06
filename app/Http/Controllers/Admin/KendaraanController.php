<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use App\Levelusers;
use Illuminate\Support\Facades\Auth;
use DB;
use App\FormClient;
use App\Mobil;
class KendaraanController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    private $controller = 'kendaraan';

    private function title(){
        return "Vehicle List";
    }

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {   
        $data['page_active'] ="kendaraan";
        $data['controller'] =$this->controller;
        $data['pages_title'] =$this->title();

        $data['data_form'] = DB::table('ek_form_client')
        ->where('status','Y')
        ->whereNotIn('name_form',['client-name','phone','address'])
        ->orderBy('urutan','ASC')->get();

        $data['dt_level'] = DB::table('roles')->get();
        return view('admin.'.$this->controller.'.list')->with($data);
    }

    public function get_kendaraan(){
        $user = new User();
        $data_form =DB::table('ek_mobil')
        			->where('status','Y')->get();
        $data = array();
        $no = 0;
        foreach ($data_form as $form) {
            $no++;
            $row = array();
            $row[] = $no;
            $row[] = $form->nama_mobil;
            $row[] = $form->type;
            $row[] = $form->no_polisi;
            $option="<div class='hidden-sm hidden-xs action-buttons center'>";
            
            $user = User::where('id_users', '=', Auth::user()->id_users)->first();
            if($user->can(['kendaraan-edit'])){

                
                $option .="<a href='javascript:void(0)' onclick=edited('".$form->id_mobil."') class='green'><i class='ace-icon fa fa-pencil bigger-130'></i></a>";
                
            }
            if($user->can(['kendaraan-delete'])){
                
                $option .="<a href='javascript:void(0)' onclick=removed('".$form->id_mobil."') class='red'><i class='ace-icon fa fa-trash-o bigger-130'></i></a>";
                
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



        if($request->nama_mobil == '')
        {
            $data['inputerror'][] = 'nama_mobil';
            $data['error_string'][] = 'Vehicle Name is required';
            $data['status'] = FALSE;
        }

        if($request->type == '')
        {
            $data['inputerror'][] = 'type';
            $data['error_string'][] = 'Type is required';
            $data['status'] = FALSE;
        }
        if($request->no_polisi == '')
        {
            $data['inputerror'][] = 'no_polisi';
            $data['error_string'][] = 'Number Police is required';
            $data['status'] = FALSE;
        }

        if($data['status'] === FALSE)
        {
            echo json_encode($data);
            exit();
        }   
    }

    public function save_kendaraan(Request $request){
        KendaraanController::_validate_data($request);
        $pk = new Mobil;
        $pk->nama_mobil = $request->nama_mobil;
        $pk->type = $request->type;
        $pk->no_polisi = $request->no_polisi;
        $pk->created_by =Auth::user()->id_users;
        $pk->status ='Y';
        $pk->save();
        echo json_encode(array("status" => TRUE));
    }


    public function update_kendaraan(Request $request){
        KendaraanController::_validate_data($request);

        $pk = Mobil::find($request->id_mobil);
        $pk->nama_mobil = $request->nama_mobil;
        $pk->type = $request->type;
        $pk->no_polisi = $request->no_polisi;
        $pk->updated_by =Auth::user()->id_users;
        $pk->save();
        echo json_encode(array("status" => TRUE));
    }

    public function deleted_kendaraan(Request $request){
        $pk = Mobil::find($request->id);
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

    public function get_kendaraan_byid(Request $request){
        $id = $request->id;

        $data_form =DB::table('ek_mobil')->where('id_mobil',$id)->first();

        $data_return =array('data_form'=>$data_form);
        return response()->json($data_return);
    }
}
