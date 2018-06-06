<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use App\Org_role;
use App\Levelusers;
use Illuminate\Support\Facades\Auth;
use DB;
use App\User_role;
use App\Permission;

class OrganisationRoleController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    private $controller = 'org_role';

    private function title(){
        return __('main.org_role-list');
    }

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {   
        $data['page_active'] = "org_role";
        $data['controller']  = $this->controller;
        $data['pages_title'] = $this->title();
        $data['org_role']    = DB::table('org_role')->get();
        return view('admin.'.$this->controller.'.list')->with($data);
    }

    private function _validate_data(Request $request){
        $data = array();
        $data['error_string'] = array();
        $data['inputerror'] = array();
        $data['status'] = TRUE;

        if($request->nama_org_divisi == '')
        {
            $data['inputerror'][] = 'nama_org_divisi';
            $data['error_string'][] = 'nama is required';
            $data['status'] = FALSE;
        }

        if($request->deskripsi == '')
        {
            $data['inputerror'][] = 'deskripsi';
            $data['error_string'][] = 'deskripsi is required';
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
        OrganisationRoleController::_validate_data($request);
        $simpan = Org_role::create($input);
        echo json_encode(array("status" => TRUE));
    }

    public function update_org_role(Request $request){
        OrganisationRoleController::_validate_data($request);
        $pk = Org_role::find($request->id_divisi);
        $pk->nama_org_divisi = $request->nama_org_divisi;
        $pk->deskripsi = $request->deskripsi;
        $pk->status = $request->status;
        $pk->save();

        echo json_encode(array("status" => TRUE));
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

    public function get_org_role_data_byid(Request $request){
        $id = $request->id;
        $data_role_organisasi = Org_role::where('id_divisi', $id)->first();

        $data_return =array('data_role_organisasi'=>$data_role_organisasi);
        return response()->json($data_return);
    }


    public function get_org_role_data(){
        $data_role_organisasi =Org_role::orderBy('id_divisi','DESC')->get();
        $data = array();
        $no = 0;
        foreach ($data_role_organisasi as $cr) {
            $no++;
            $row = array();
            $row[] = $no;
            $row[] = $cr->nama_org_divisi;
            $row[] = $cr->deskripsi;
            $row[] = ($cr->status == "Y") ? __('main.active') : __('main.non-active');

            $option="<div class='hidden-sm hidden-xs action-buttons center'>";
            
            $user = User::where('id_users', '=', Auth::user()->id_users)->first();
            if($user->can(['org_role-edit'])){
                $option .="<a href='javascript:void(0)' onclick=edited('".$cr->id_divisi."') class='green'><i class='ace-icon fa fa-pencil bigger-130'></i></a>";

            }
            // if($user->can(['org_role-delete'])){
            //     $option .="<a href='javascript:void(0)' onclick=removed('".$cr->id_divisi."') class='red'><i class='ace-icon fa fa-trash-o bigger-130'></i></a>";
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
}
