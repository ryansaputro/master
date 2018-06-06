<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use PeterColes\Countries\CountriesFacade;
use App\ClientModel;
use App\Product;
use App\ProjectModel;
use App\ProjectDetail;
use App\ProjectAndProduct;
use DB;
use App\User;
use Illuminate\Support\Facades\Auth;
use Zizaco\Entrust\EntrustFacade as Entrust;

class ProjectController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */

    private $controller = 'project';


    public function __construct()
    {
        $this->middleware('auth');
    }

    private function title(){
        return "Project";
    }


    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
       	$user = new User();
        $data['pages_title'] = $this->title();
        $data['page_active'] ="project";
        $data['countries'] = CountriesFacade::lookup(LaravelLocalization::getCurrentLocale());
        $datax =new ProjectModel;

        $q = $request->get('q') !== null ? $request->get('q') : '';
        if(Entrust::can('all-project')){

            if ($q !== ''){
                $dataxy =$datax->get_data()->where('nama_project','LIKE','%'.$q.'%')->paginate(15);
                $data['q'] = $q;
            }else{
                $dataxy =$datax->get_data()->paginate(15);
                $data['q'] = '';
            }
        }else{
            if ($q !== ''){
                $dataxy =$datax->get_data()
                        ->where('nama_project','LIKE','%'.$q.'%')
                        ->where('ek_project.created_by',Auth::user()->id_users)->paginate(15);
                $data['q'] = $q;
            }else{
                $dataxy =$datax->get_data()->where('ek_project.created_by',Auth::user()->id_users)->paginate(15);
                $data['q'] = '';
            }
            
        }

        $data['data_client'] =$dataxy;
        $data['i']=($request->input('page', 1) - 1) * 15;
        $data['controller']= $this->controller;
        return view('admin.'.$this->controller.'.list')->with($data);
    }


    public function create(){
        $data['pages_title'] = __('main.add').' '.$this->title();
        $data['page_active'] ="project";

        $data['product'] = Product::where('status','Y')->orderBy('urutan','ASC')->get();


        $data['countries'] = CountriesFacade::lookup(LaravelLocalization::getCurrentLocale());
        $data['controller']= $this->controller;
        $data['form_project'] =DB::table('ek_form_project')->where('status','Y')->orderBy('urutan','ASC')->get();

        if(Entrust::can('all-data-client')){
            $get_client =DB::table('ek_client')->where('status','Y')->get();
        }else{
            $get_client =DB::table('ek_client')
                        ->where('created_by',Auth::user()->id_users)
                        ->where('status','Y')->get();
        }

        $data['data_client'] =$get_client;
        $table="ek_project";
        $primary="id_project";
        $prefik="P";
        $Kode_unik=ProjectModel::autonumber($table,$primary,$prefik);
        $data['id_project'] = $Kode_unik;
        return view('admin.'.$this->controller.'.add')->with($data);
    }


   

    public function store(Request $request){
        if (!Auth::user()->can($this->controller.'-add')){
            return view('errors.403');
        }
        $data =$request->input();

        $id_project= $request->input('id_project');
        $id_client= $request->input('id_client');
        $project_name =$request->input('1');
        $deskripsi_project =$request->input('2');
        $nominal =$request->input('3');

        $b = str_replace( '.', '', $nominal );
        if( is_numeric( $b ) ) {
            $nominal = $b;
        }

        $this->validate($request, [
            'product' => 'required_without_all'
        ]);


        if(!empty($request->product)){
            foreach ($request->input('product') as $key => $value) {
                $id_product= $value;
                ProjectAndProduct::create([
                    'id_project'=>$id_project,
                    'id_client'=>$id_client,
                    'id_product'=>$id_product,
                ]);
            }
        }
        
        
        $row = new ProjectModel;
        $row->id_project =$id_project;
        $row->id_client = $id_client;
        $row->nama_project =$project_name;
        $row->nominal =$nominal;
        $row->deskripsi_project =$deskripsi_project;
        $row->status ='Y';
        $row->created_by =Auth::user()->id_users;
        $row->save();
        
		foreach ($data as $key => $value) {
            $id_form = $key;
            $value_form = $value;

            $rowDesc = new ProjectDetail(array(
                'id_form' => $id_form,
                'id_project' => $id_project,
                'nilai_form' => $value_form,
                'created_by'=>Auth::user()->id_users,
                'status' =>'Y' 
            ));

            if($id_form != 0){
                $rowDesc->save();
            }
        }
        return redirect(LaravelLocalization::getCurrentLocale().'/dashboard/'.$this->controller)->with('status', __( 'main.data_has_been_added', ['page' => $this->title()] ) );
    }


    public function show($id){
        $data['pages_title'] = 'Views '.$this->title();
        $data['page_active'] ="project";
        $data['id_project'] =$id;
        $rest_data =new ProjectModel;


        $data['product_list'] = Product::where('status','Y')->get();

        $data['product_checked'] = DB::table("ek_project_and_product")->where("ek_project_and_product.id_project",$id)
            ->pluck('ek_project_and_product.id_product','ek_project_and_product.id_product')->all();


        $data['client'] =$rest_data->get_data_client_on_project($id)->first();
        $data['form_project'] =$rest_data->get_data_project($id)->get();
        $data['controller']= $this->controller;
        return view('admin.'.$this->controller.'.show')->with($data);
    }


    public function edit($id){

        $data['pages_title'] = 'Edit '.$this->title();
        $data['page_active'] ="project";
        $data['id_project'] =$id;

        $data['product_list'] = Product::where('status','Y')->get();

        $data['product_checked'] = DB::table("ek_project_and_product")->where("ek_project_and_product.id_project",$id)
            ->pluck('ek_project_and_product.id_product','ek_project_and_product.id_product')->all();


        $rest_data =new ProjectModel;

        $data['client'] =$rest_data->get_data_client_on_project($id)->first();

        $data['form_project'] =$rest_data->get_data_project($id)->get();
        $data['data_client'] =DB::table('ek_client')->where('status','Y')->get();
        $data['controller']= $this->controller;
        return view('admin.'.$this->controller.'.edit')->with($data);
    }


    public function update(Request $request){
        if (!Auth::user()->can($this->controller.'-add')){
            return view('errors.403');
        }

        $data =$request->input();

        $id_project= $request->input('id_project');
        $id_client= $request->input('id_client');
        $project_name =$request->input('1');
        $deskripsi_project =$request->input('2');
        $nominal =$request->input('3');

        $b = str_replace( '.', '', $nominal );
        if( is_numeric( $b ) ) {
            $nominal = $b;
        }

        $pk = ProjectModel::find($id_project);
        $pk->id_client = $id_client;
        $pk->nama_project =$project_name;
        $pk->nominal =$nominal;
        $pk->deskripsi_project =$deskripsi_project;
        $pk->updated_by =Auth::user()->id_users;
        $pk->save();



        DB::table("ek_project_and_product")->where("ek_project_and_product.id_project",$id_project)->delete();
        if(!empty($request->product)){
            foreach ($request->input('product') as $key => $value) {
                $id_product= $value;
                ProjectAndProduct::create([
                    'id_project'=>$id_project,
                    'id_client'=>$id_client,
                    'id_product'=>$id_product,
                ]);
            }
        }


        foreach ($data as $key => $value) {
            $id_form = $key;
            $value_form = $value;
            
            if($id_form != 0){
                // $rowDesc->save();
                $data_upd=array(
                    'nilai_form'=>$value_form,
                    'updated_by'=>Auth::user()->id_users
                );

                DB::table('ek_project_detail')
                ->where('id_form',$id_form)
                ->where('id_project',$id_project)
                ->update($data_upd);
            }
        }

        return redirect(LaravelLocalization::getCurrentLocale().'/dashboard/'.$this->controller)->with('status', __( 'main.data_has_been_updated', ['page' => $this->title()] ) );
    }


    public function delete($id){

        if (!Auth::user()->can('project-delete')){
            return view('errors.403');
        }

        $pk = ProjectModel::find($id);
        $pk->status ="N";
        $pk->updated_by =Auth::user()->id_users;
        $pk->save();
        $data_upd=array(
            'status'=>'N',
            'updated_by'=>Auth::user()->id_users
        );


        DB::table('ek_project_detail')
        ->where('id_project',$id)
        ->update($data_upd);
        return redirect(LaravelLocalization::getCurrentLocale().'/dashboard/'.$this->controller)->with('status', __( 'main.data_has_been_unselect') );
    }

}
