<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use App\Criteria;
use App\Levelusers;
use Illuminate\Support\Facades\Auth;
use DB;
use App\User_role;
use App\Permission;

class CriteriaController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    private $controller = 'kriteria';

    private function title(){
        return __('main.kriteria-list');
    }

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index() {   
        $data['kriteria']    = DB::table('kriteria')->get();
        $data['page_active'] = "kriteria";
        $data['controller']  = $this->controller;
        $data['pages_title'] = $this->title();
        return view('admin.'.$this->controller.'.list')->with($data);
    }

    private function _validate_data(Request $request){
        $data = array();
        $data['error_string'] = array();
        $data['inputerror'] = array();
        $data['status'] = TRUE;

        if($request->nama_kriteria == '')
        {
            $data['inputerror'][] = 'nama_kriteria';
            $data['error_string'][] = 'nama is required';
            $data['status'] = FALSE;
        }

        if($request->singkatan == '')
        {
            $data['inputerror'][] = 'singkatan';
            $data['error_string'][] = 'singkatan is required';
            $data['status'] = FALSE;
        }
        if($request->status == '')
        {
            $data['inputerror'][] = 'status';
            $data['error_string'][] = 'status is required';
            $data['status'] = FALSE;
        }

        if($data['status'] === FALSE)
        {
            echo json_encode($data);
            exit();
        }   
    }

    public function store(Request $request){
        $input  = $request->all();
        CriteriaController::_validate_data($request);
        $simpan = Criteria::create($input);
        echo json_encode(array("status" => TRUE));
    }

    public function update_criteria(Request $request){
        CriteriaController::_validate_data($request);
        $pk = Criteria::find($request->id_kriteria);
        $pk->nama_kriteria = $request->nama_kriteria;
        $pk->singkatan = $request->singkatan;
        $pk->status = $request->status;
        $pk->save();

        echo json_encode(array("status" => TRUE));
    }

    public function get_criteria_data(){
        $data_criteria =Criteria::orderBy('id_kriteria','DESC')->get();
        $data = array();
        $no = 0;
        foreach ($data_criteria as $cr) {
            $no++;
            $row = array();
            $row[] = $no;
            $row[] = $cr->nama_kriteria;
            $row[] = $cr->singkatan;
            $row[] = ($cr->status == "Y") ? __("main.active") : __("main.non-active");

            $option="<div class='hidden-sm hidden-xs action-buttons center'>";
            
            $user = User::where('id_users', '=', Auth::user()->id_users)->first();
            if($user->can(['kriteria-edit'])){
                $option .="<a href='javascript:void(0)' onclick=edited('".$cr->id_kriteria."') class='green'><i class='ace-icon fa fa-pencil bigger-130'></i></a>";

            }
            // if($user->can(['kriteria-delete'])){
            //     $option .="<a href='javascript:void(0)' onclick=removed('".$cr->id_kriteria."') class='red'><i class='ace-icon fa fa-trash-o bigger-130'></i></a>";
            // }
           
            $option .= "</div>";
            $row[]   = $option;
            $data[]  = $row;
        }
 
        $output = array(
                        "data" => $data,
                    );
        return json_encode($output);
    }

    public function get_criteria_data_byid(Request $request){
        $id = $request->id;
        $data_criteria = Criteria::where('id_kriteria', $id)->first();

        $data_return =array('data_criteria'=>$data_criteria);
        return response()->json($data_return);
    }


    // public function deleted_users(Request $request){
    //     $pk = User::find($request->id);
    //     $pk->status ="N";
    //     $pk->updated_by =Auth::user()->id_users;
    //     $pk->save();
    //     $result=array(
    //             "data_post"=>array(
    //                 "status"=>TRUE,
    //                 "class" => "success",
    //                 "message"=>"Success ! Deleted data"
    //             )
    //         );
    //     echo json_encode($result);
    // }
}
