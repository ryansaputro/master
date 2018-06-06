<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use App\DocumentType;
use App\Levelusers;
use Illuminate\Support\Facades\Auth;
use DB;
use App\User_role;
use App\Permission;
class DocumentTypeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    private $controller = 'tipe_dokumen';

    private function title(){
         return __('main.tipe_dokumen-list');
    }

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(){   

        $data['data_form'] = DB::table('tipe_dokumen')
        ->orderBy('urutan','ASC')->get();
        $data['page_active'] ="tipe_dokumen";
        $data['controller'] =$this->controller;
        $data['pages_title'] =$this->title();
        return view('admin.'.$this->controller.'.list')->with($data);
    }

    private function _validate_data(Request $request){
        $data = array();
        $data['error_string'] = array();
        $data['inputerror'] = array();
        $data['status'] = TRUE;

        if($request->tipe_dokumen == '')
        {
            $data['inputerror'][] = 'tipe_dokumen';
            $data['error_string'][] = 'Tipe Dokumen is required';
            $data['status'] = FALSE;
        }

        

        if($data['status'] === FALSE)
        {
            echo json_encode($data);
            exit();
        }   
    }

    public function store(Request $request){

        DocumentTypeController::_validate_data($request);
        $tipe_dokumen =$request->tipe_dokumen;
        
        $pk = new DocumentType;
        $pk->tipe_dokumen = $tipe_dokumen;
        $pk->urutan =0;
        // $pk->created_by =Auth::user()->id_users;
        $pk->status ='Y';
        $pk->save();
        echo json_encode(array("status" => TRUE));
    }
    
    public function update_tipe_dokumen(Request $request){
        DocumentTypeController::_validate_data($request);

        $tipe_dokumen =$request->tipe_dokumen;

        $pk = DocumentType::find($request->id_tipe_dokumen);
        $pk->tipe_dokumen = $tipe_dokumen;
        $pk->status = $request->status;
        // $pk->updated_by =Auth::user()->id_users;
        $pk->save();
        echo json_encode(array("status" => TRUE));
    }

    public function get_tipe_dokumen(){
        $xxx = new DocumentType();
        $datax =$xxx->get_data()
        ->where('status', 'Y')
        ->get();
        $data = array();
        $no = 0;
        foreach ($datax as $datay) {
            $no++;
            $row = array();
            $row[] = $no;
            $row[] = $datay->tipe_dokumen;
            $row[] = $datay->status == "Y" ?  __("main.active") :  __("main.non-active");
            $option="<div class='hidden-sm hidden-xs action-buttons center'>";
            

            $user = User::where('id_users', '=', Auth::user()->id_users)->first();
            if($user->can(['tipe_dokumen-edit'])){
                $option .="<a href='javascript:void(0)' onclick=edited('".$datay->id_tipe_dokumen."') class='green'><i class='ace-icon fa fa-pencil bigger-130'></i></a>";
            }

            if($user->can(['tipe_dokumen-delete'])){
                $option .="<a href='javascript:void(0)' onclick=removed('".$datay->id_tipe_dokumen."') class='red'><i class='ace-icon fa fa-trash-o bigger-130'></i></a>";
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

    public function get_tipe_dokumen_byid(Request $request){
        $id = $request->id;
        

        $data_form =DB::table('tipe_dokumen')->where('id_tipe_dokumen',$id)->first();

        $data_return =array('data_form'=>$data_form);
        return response()->json($data_return);
    }

    public function deleted_tipe_dokumen(Request $request){
        $pk = DocumentType::find($request->id);
        $pk->delete();
        $pk->status ='N';
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
