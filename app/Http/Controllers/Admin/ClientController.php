<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use PeterColes\Countries\CountriesFacade;
use App\ClientModel;
use App\Client_detail;
use DB;
use Illuminate\Support\Facades\Auth;
use App\User;
use Zizaco\Entrust\EntrustFacade as Entrust;

class ClientController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */

    private $controller = 'client';


    public function __construct()
    {
        $this->middleware('auth');
    }

    private function title(){
        return "Client";
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
        $data['page_active'] ="client_list";
        $data['countries'] = CountriesFacade::lookup(LaravelLocalization::getCurrentLocale());
        $datax =new ClientModel;
        // 
        $q = $request->get('q') !== null ? $request->get('q') : '';
        if(Entrust::can('all-data-client')){
            

            if ($q !== ''){
                $dataxy =$datax->get_data()->where('nama_client','LIKE','%'.$q.'%')->paginate(15);
                $data['q'] = $q;
            }else{
                $dataxy =$datax->get_data()->paginate(15);
                $data['q'] = '';
            }

        }else{

            if ($q !== ''){
                $dataxy =$datax->get_data()->where('nama_client','LIKE','%'.$q.'%')->where('created_by',Auth::user()->id_users)->paginate(15);
                $data['q'] = $q;
            }else{
                $dataxy =$datax->get_data()->where('created_by',Auth::user()->id_users)->paginate(15);
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
        $data['page_active'] ="client_list";
        $data['countries'] = CountriesFacade::lookup(LaravelLocalization::getCurrentLocale());
        $data['controller']= $this->controller;
        

        $data['form_client'] =DB::table('ek_form_client')->where('status','Y')->orderBy('urutan','ASC')->get();

        $table="ek_client";
        $primary="id_client";
        $prefik="C";
        $Kode_unik=ClientModel::autonumber($table,$primary,$prefik);

        $data['id_client'] = $Kode_unik;

        return view('admin.'.$this->controller.'.add')->with($data);
    }


    public  function store(Request $request){

        $data =$request->input();

        $id_client= $request->input('id_client');
        $nama_client =$request->input('1');
        $datax =new ClientModel;

        $client =$datax->get_data()->where('nama_client','LIKE','%'.$nama_client.'%')->first();

        if($client){
            return redirect(LaravelLocalization::getCurrentLocale().'/dashboard/'.$this->controller.'/create')->with('status', __( 'Client Name is exists', ['page' => $this->title()] ) );

        }else{
            $telp =$request->input('2');
            $alamat =$request->input('3');
            $email =$request->input('4');

            $row = new ClientModel;
            $row->id_client = $id_client;
            $row->nama_client =$nama_client;
            $row->telp =$telp;
            $row->email =$email;
            $row->alamat =$alamat;
            $row->status ='Y';
            $row->created_by =Auth::user()->id_users;
            $row->save();


            foreach ($data as $key => $value) {
                $id_form = $key;
                $value_form = $value;
                $rowDesc = new Client_detail(array(
                    'id_form' => $id_form,
                    'id_client' => $id_client,
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
    }

    public function show($id){
                
        $data['pages_title'] = 'Views '.$this->title();
        $data['page_active'] ="client_list";
        $data['id_client'] =$id;

        $rest_data =new ClientModel;
        $data['form_client'] =$rest_data->get_data_client($id)->get();
        
        $data['controller']= $this->controller;
        return view('admin.'.$this->controller.'.show')->with($data);

    }

    public  function edit($id){
        $data['pages_title'] = 'Edit '.$this->title();
        $data['page_active'] ="client_list";
        $data['id_client'] =$id;

        $rest_data =new ClientModel;
        $data['form_client'] =$rest_data->get_data_client($id)->get();
        
        $data['controller']= $this->controller;
        return view('admin.'.$this->controller.'.edit')->with($data);
    }


    public function update(Request $request){
        $data =$request->input();

        $id_client =$request->id_client;

        $nama_client =$request->input('1');
        $telp =$request->input('2');
        $alamat =$request->input('3');
        $email =$request->input('4');


        $pk = ClientModel::find($id_client);        
        $pk->nama_client =$nama_client ;
        $pk->telp =$telp;
        $pk->email =$email;
        $pk->alamat =$alamat;
        $pk->updated_by =Auth::user()->id_users;
        $pk->save();


        foreach ($data as $key => $value) {
            $id_form = $key;
            $value_form = $value;

            
            if($id_form != 0){
                // $rowDesc->save();
                $data_upd=array(
                    'nilai_form'=>$value_form,
                    'updated_by'=>Auth::user()->id_users
                );

                DB::table('ek_client_detail')
                ->where('id_form',$id_form)
                ->where('id_client',$id_client)
                ->update($data_upd);
            }
        }

        return redirect(LaravelLocalization::getCurrentLocale().'/dashboard/'.$this->controller)->with('status', __( 'main.data_has_been_updated', ['page' => $this->title()] ) );


    }

    public function delete($id){
        if (!Auth::user()->can('client-delete')){
            return view('errors.403');
        }

        $pk = ClientModel::find($id);
        $pk->status ="N";
        $pk->updated_by =Auth::user()->id_users;
        $pk->save();
        $data_upd=array(
            'status'=>'N',
            'updated_by'=>Auth::user()->id_users
        );


        DB::table('ek_client_detail')
        ->where('id_client',$id)
        ->update($data_upd);
        return redirect(LaravelLocalization::getCurrentLocale().'/dashboard/'.$this->controller)->with('status', __( 'main.data_has_been_unselect') );
    }


    private function _validate_data(Request $request){
        $data = array();
        $data['error_string'] = array();
        $data['inputerror'] = array();
        $data['status'] = TRUE;





        if($request->id_client == '')
        {
            $data['inputerror'][] = 'id_client';
            $data['error_string'][] = 'Clinet is required';
            $data['status'] = FALSE;
        }

        if($request->id_users == '')
        {
            $data['inputerror'][] = 'id_users';
            $data['error_string'][] = 'Users Not be selected';
            $data['status'] = FALSE;
        }
        

        if($data['status'] === FALSE)
        {
            echo json_encode($data);
            exit();
        }   
    }


    public function delegate(Request $request){
        $id_client = $request->id_client;
        $id_users = $request->id_users;
        
        ClientController::_validate_data($request);

        $pk = ClientModel::find($request->id_client);
        $pk->created_by = $id_users;
        $pk->updated_by =Auth::user()->id_users;
        $pk->save();
        echo json_encode(array("status" => TRUE));


    }

}
