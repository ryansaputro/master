<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use App\Product;
use App\Levelusers;
use Illuminate\Support\Facades\Auth;
use DB;
use App\User_role;
use App\Permission;
class SettingProductController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    private $controller = 'sett_product';

    private function title(){
        return "List Setting Follow up";
    }

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {   

        $data['data_form'] = DB::table('ek_product')
        ->where('status','Y')
        ->orderBy('urutan','ASC')->get();

        
        $data['page_active'] ="sett_product";
        $data['controller'] =$this->controller;
        $data['pages_title'] =$this->title();
        return view('admin.'.$this->controller.'.list')->with($data);
    }

    public function get_sett_product(){
        $xxx = new Product();
        $datax =$xxx->get_data()->get();
        $data = array();
        $no = 0;
        foreach ($datax as $datay) {
            $no++;
            $row = array();
            $row[] = $no;
            $row[] = $datay->product_slug;
            $row[] = $datay->product_name;
            $option="<div class='hidden-sm hidden-xs action-buttons center'>";
            

            $user = User::where('id_users', '=', Auth::user()->id_users)->first();
            if($user->can(['sett_product-edit'])){

                $option .="<a href='javascript:void(0)' onclick=edited('".$datay->id_product."') class='green'><i class='ace-icon fa fa-pencil bigger-130'></i></a>";
            }

            if($user->can(['sett_product-delete'])){
                $option .="<a href='javascript:void(0)' onclick=removed('".$datay->id_product."') class='red'><i class='ace-icon fa fa-trash-o bigger-130'></i></a>";
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


        if($request->product_name == '')
        {
            $data['inputerror'][] = 'product_name';
            $data['error_string'][] = 'Product name Value is required';
            $data['status'] = FALSE;
        }
        

        if($data['status'] === FALSE)
        {
            echo json_encode($data);
            exit();
        }   
    }


    public function save_sett_product(Request $request){

        SettingProductController::_validate_data($request);
        $product_slug =$request->product_slug;
        $product_name =$request->product_name;
        
        $pk = new Product;
        $pk->product_slug = $product_slug;
        $pk->product_name = $product_name;
        $pk->urutan =0;
        $pk->created_by =Auth::user()->id_users;
        $pk->status ='Y';
        $pk->save();
        echo json_encode(array("status" => TRUE));
    }
    

    public function get_sett_product_byid(Request $request){
        $id = $request->id;
        

        $data_form =DB::table('ek_product')->where('id_product',$id)->first();

        $data_return =array('data_form'=>$data_form);
        return response()->json($data_return);
    }

    public function update_sett_product(Request $request){
        SettingProductController::_validate_data($request);

        $product_slug =$request->product_slug;
        $product_name =$request->product_name;

        $pk = Product::find($request->id_product);
        $pk->product_slug = $product_slug;
        $pk->product_name = $product_name;
        $pk->updated_by =Auth::user()->id_users;
        $pk->save();
        echo json_encode(array("status" => TRUE));
    }

    public function deleted_sett_product(Request $request){
        $pk = Product::find($request->id);
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
