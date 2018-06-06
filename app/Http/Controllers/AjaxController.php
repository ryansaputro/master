<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use PeterColes\Countries\CountriesFacade;

class AjaxController extends Controller
{

  public function index(){

      $data['propinsi'] = DB::table("ek_local_provinces")
                    ->pluck("name","id");
      return view('dropdown')->with($data);;
  }

   public function kabupaten($id)
    {
        $cities = DB::table("ek_local_regencies")
                    ->where("province_id",$id)
                    ->pluck("name","id");
        return json_encode($cities);
    }

    public function get_workers($id)
    {
        $info = DB::table('ek_worker')
                ->select('ek_worker.*','ek_local_regencies.name as regensi','ek_local_provinces.name as propinsi','ek_code_experience.code')
                ->join('ek_local_regencies', 'ek_worker.id_regensi', '=', 'ek_local_regencies.id')
                ->join('ek_local_provinces', 'ek_worker.id_provinsi', '=', 'ek_local_provinces.id')
                ->leftjoin('ek_code_experience','ek_worker.code_experience','=','ek_code_experience.id')
                ->where('id_worker',$id)->first();

        $countries =CountriesFacade::lookup(LaravelLocalization::getCurrentLocale());
        $data=array('data_worker'=>$info,'data_tujuan'=>$countries[$info->country]);
        // echo "<pre>";
        // print_r($data);
        // echo "</pre>";
        return response()->json($data);
    }

    public function Kecamatan($id)
     {
         $cities = DB::table("ek_local_districts")
                     ->where("regency_id",$id)
                     ->pluck("name","id");
         return json_encode($cities);
     }
     public function kelurahan($id)
      {
          $cities = DB::table("ek_local_villages")
                      ->where("district_id",$id)
                      ->pluck("name","id");
          return json_encode($cities);
      }

    public function source($id){
      $cities = DB::table("ek_link_sosmed")
                    ->join('ek_sosmed','ek_sosmed.id_sosmed','=','ek_link_sosmed.id_sosmed')
                    ->where("id_worker",$id)
                    ->pluck("ek_sosmed.nama_sosmed","link_sosmed");
        return json_encode($cities);
    }


    function get_client_detail(){
      $id = $request->id;
        $workers = new Workers();
        $source = new LinkSosmed();
        $data_source = $source->get_data_source_byid($id)->get();
        $data_worker =$workers->get_data_worker_byid($id)->first();


        $countries =CountriesFacade::lookup(LaravelLocalization::getCurrentLocale());
        $data_return =array('data_worker'=>$data_worker,'data_source'=>$data_source,'data_negara'=>$countries[$data_worker->country]);
        return response()->json($data_return);
    }


    


}