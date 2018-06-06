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
use App\FollowUp;
use App\ProjectAndProduct;
use DB;


use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
	
	private $controller = 'report_project';


    public function __construct()
    {
        $this->middleware('auth');
    }

    private function title(){
        return "Report Project";
    }

    public function project_follow_up(){
    	$data['pages_title'] = $this->title();
        $data['page_active'] ="report";
        $data['controller']= $this->controller;
        $data['id_project'] ="P00005";
        return view('admin.'.$this->controller.'.list')->with($data);
    }


    public function get_project_follow_up(){
    	
    	$datax =new ProjectModel;
     	$data_project = $datax->get_data()->get();
     	$no=1;
   
      	foreach ($data_project as $res) {
        	$row = array();

        	$row[] =$no++;
        	$client ="<a href='javascript:void(0)' onclick='views_client(".$res->id.")' class='green'>".$res->nama_client."</a>";
         	$row[] =$client;
        	$row[] =$res->nama_project;
        	$row[] =$res->deskripsi_project;
         	$id_project =$res->id_project;

         	$get_followup_project = DB::table('ek_follow_up')
         							->where('status','Y')
         							->where('id_project',$id_project)->count();          	

         	$number_of_contacts ="<a href='javascript:void(0)' onclick='views_followup_detail(".$res->project_id.")' class='btn btn-grey btn-xs'><i class='ace-icon fa fa-eye bigger-160'></i>".$get_followup_project." X Follow-Up"."</a>";
         	$row[] =$number_of_contacts;

         	$print='<a href="javascript:;" onclick="javascript:createPopUp('.$res->project_id.')" class="btn btn-info btn-xs" title="Print" style="margin-bottom: 5px"><i class="ace-icon glyphicon glyphicon-print bigger-160"></i>Print</a>';
         	$row[] =$print;

        	$data[] = $row;
     	}

      $output = array(
                      "data" => $data,
                  );
      return json_encode($output);
    }


    public function print_project_follow_up($id){
    	$get_project_id = DB::table('ek_project')
    						->where('project_id',$id)->first();

    	$id_project =$get_project_id->id_project;
    	$datax =new FollowUp;
     	$data_followed = $datax->get_data_by_project($id_project)->get();

     	$data['nama_project'] =$get_project_id->nama_project;
     	$data['data_client'] = DB::table('ek_client_detail')->where('id_client',$get_project_id->id_client)->get();
     	$data['data_project'] =DB::table('ek_project_detail')->where('id_project',$id_project)->get();
     	$data['data_followed'] =$data_followed;
        $data['controller']= $this->controller;
     	$data['page_active'] ="report";
     	$data['pages_title'] = $this->title();
     	
     	return view('admin.'.$this->controller.'.print')->with($data);

    }
}
