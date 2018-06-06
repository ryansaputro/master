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
use App\Checklist;

class CheckListController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    private $controller = 'checklist';

    private function title(){
        return __('main.checklist-list');
    }

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $data['page_active'] = "checklist";
        $data['controller']  = $this->controller;
        $data['pages_title'] = $this->title();
        $data['kriteria']    = DB::table('kriteria')->where('status', 'Y')->get();
        return view('admin.'.$this->controller.'.list')->with($data);
    }

    private function _validate_data(Request $request){
        $data = array();
        $data['error_string'] = array();
        $data['inputerror'] = array();
        $data['status'] = TRUE;

        if($request->id_klausul == '')
        {
            $data['inputerror'][] = 'Klausul';
            $data['error_string'][] = 'Klausul is required';
            $data['status'] = FALSE;
        }

        if($request->pertanyaan == '')
        {
            $data['inputerror'][] = 'pertanyaan';
            $data['error_string'][] = 'pertanyaan is required';
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
        CheckListController::_validate_data($request);
        $id_org_role = Auth::user()->id_org_role;
        ($id_org_role == '') ? $id_org_role='null' : $id_org_role;
        $simpan = new Checklist;
        $simpan->id_kriteria = DB::table('klausul')->where('id_klausul', $request->id_klausul)->value('id_kriteria');
        $simpan->id_klausul = $request->id_klausul;
        $simpan->id_divisi = $id_org_role;
        $simpan->pertanyaan = $request->pertanyaan;
        $simpan->status = $request->status;

        $simpan->save();

        echo json_encode(array("status" => TRUE));
    }

    public function update_cheklist(Request $request){
        CheckListController::_validate_data($request);
        $pk = Checklist::find($request->id_ceklis);
        $pk->id_kriteria = $request->id_kriteria;
        $pk->id_klausul = $request->id_klausul;
        $pk->id_kriteria = $request->id_kriteria;
        $pk->pertanyaan = $request->pertanyaan;
        $pk->status = $request->status;
        $pk->save();

        echo json_encode(array("status" => TRUE));
    }

    public function get_checklist_data_byid(Request $request){
        $id = $request->id;
        $data_clousul = Checklist::where('id_ceklis', $id)->first();

        $data_return =array('data_clousul'=>$data_clousul);
        return response()->json($data_return);
    }


    public function get_checklist_data(){
        // $data_clousul = new Checklist;
        // $data_name_clousul = $data_clousul
        $data_klausul = Checklist::
        // select('ceklis.*','kriteria.nama_kriteria')
        select('ceklis.*','kriteria.nama_kriteria','klausul.deskripsi')
        ->join('kriteria','kriteria.id_kriteria','=','ceklis.id_kriteria')
        ->join('klausul','klausul.id_klausul','=','ceklis.id_klausul')
        ->get();

        $data = array();
        $no = 0;
        foreach ($data_klausul as $cr) {
            $no++;
            $row = array();
            $row[] = $no;
            $row[] = $cr->nama_kriteria;
            $row[] = $cr->deskripsi;
            $row[] = $cr->pertanyaan;
            $row[] = ($cr->status == "Y") ? __("main.active") : __("main.non-active");

            $option="<div class='hidden-sm hidden-xs action-buttons center'>";

            $user = User::where('id_users', '=', Auth::user()->id_users)->first();
            if($user->can(['klausul-edit'])){
                $option .="<a href='javascript:void(0)' onclick=edited('".$cr->id_ceklis."') class='green'><i class='ace-icon fa fa-pencil bigger-130'></i></a>";

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
