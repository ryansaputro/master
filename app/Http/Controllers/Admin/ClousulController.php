<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use App\Criteria;
use App\Clousul;
use App\Levelusers;
use Illuminate\Support\Facades\Auth;
use DB;
use App\User_role;
use App\Permission;

class ClousulController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    private $controller = 'klausul';

    private function title(){
        return __('main.klausul-list');
    }

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {   
        $data['page_active'] = "klausul";
        $data['controller']  = $this->controller;
        $data['pages_title'] = $this->title();
        $data['kriteria']    = DB::table('kriteria')->where('status', 'N')->get();
        return view('admin.'.$this->controller.'.list')->with($data);
    }

    private function _validate_data(Request $request){
        $data = array();
        $data['error_string'] = array();
        $data['inputerror'] = array();
        $data['status'] = TRUE;

        if($request->id_kriteria == '')
        {
            $data['inputerror'][] = 'kriteria';
            $data['error_string'][] = 'kriteria is required';
            $data['status'] = FALSE;
        }

        if($request->no_klausul == '')
        {
            $data['inputerror'][] = 'no_klausul';
            $data['error_string'][] = 'no_klausul is required';
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
        ClousulController::_validate_data($request);
        $simpan = Clousul::create($input);
        echo json_encode(array("status" => TRUE));
    }

    public function update_criteria(Request $request){
        ClousulController::_validate_data($request);
        $pk = Clousul::find($request->id_klausul);
        $pk->id_kriteria = $request->id_kriteria;
        $pk->no_klausul = $request->no_klausul;
        $pk->deskripsi = $request->deskripsi;
        $pk->status = $request->status;
        $pk->save();

        echo json_encode(array("status" => TRUE));
    }

    public function get_klausul_data_byid(Request $request){
        $id = $request->id;
        $data_clousul = Clousul::where('id_klausul', $id)->first();

        $data_return =array('data_clousul'=>$data_clousul);
        return response()->json($data_return);
    }


    public function get_clousul_data(){
        $data_clousul =Clousul::orderBy('id_kriteria','DESC');
        $data_name_clousul = $data_clousul->select('klausul.*','kriteria.nama_kriteria')->join('kriteria','klausul.id_kriteria','=','kriteria.id_kriteria');
        $data_klausul = $data_name_clousul->get();
        $data = array();
        $no = 0;
        foreach ($data_klausul as $cr) {
            $no++;
            $row = array();
            $row[] = $no;
            $row[] = $cr->nama_kriteria;
            $row[] = $cr->no_klausul;
            $row[] = $cr->deskripsi;
            $row[] = ($cr->status == "Y") ? __("main.active") : __("main.non-active");

            $option="<div class='hidden-sm hidden-xs action-buttons center'>";
            
            $user = User::where('id_users', '=', Auth::user()->id_users)->first();
            if($user->can(['klausul-edit'])){
                $option .="<a href='javascript:void(0)' onclick=edited('".$cr->id_klausul."') class='green'><i class='ace-icon fa fa-pencil bigger-130'></i></a>";

            }
           
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
