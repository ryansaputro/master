<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
class ProjectModel extends Model
{
    //
    protected $table = 'ek_project';
    protected $primaryKey = 'id_project';
    public $incrementing = false;

    protected $fillable = [
        'id_client','nama_project', 'created_by','updated_by','status_followed','status'
    ];



    public static function autonumber($table,$primary,$prefix){

        $q=DB::table($table)->select(DB::raw('MAX(RIGHT('.$primary.',5)) as kd_max'));
        
        if($q->count()>0)
        {
            foreach($q->get() as $k)
            {
                $tmp = ((int)$k->kd_max)+1;
                $kd = $prefix.sprintf("%05s", $tmp);
            }
        }
        else
        {
            $kd = $prefix."00001";
        }
        return $kd;
    }

    public function get_data(){
    	$data = DB::table('ek_project')
        ->select('ek_project.*','ek_client.id_client','ek_client.id','ek_client.nama_client','ek_client.telp','ek_client.alamat')
        ->join('ek_client', 'ek_project.id_client', '=', 'ek_client.id_client')
        ->where('ek_project.status', 'Y')
        ->orderBy('id_project','DESC');
        return $data;
    }

    public function get_data_client_on_project($id){
        $data = DB::table('ek_project')
        ->select('ek_project.id_project','ek_project.nama_project','ek_project.deskripsi_project','ek_client.id_client','ek_client.nama_client','ek_client.telp','ek_client.alamat','ek_client.email')
        ->join('ek_client', 'ek_project.id_client', '=', 'ek_client.id_client')
        ->where('ek_project.status', 'Y')
        ->where('ek_project.id_project',$id);
        return $data;
    }

    public function get_data_project($id)
    {
        # code...
         $data = DB::table('ek_project_detail')
        ->select('ek_project_detail.*','ek_form_project.*')

        ->join('ek_form_project', 'ek_project_detail.id_form', '=', 'ek_form_project.id_form_project')
        ->where('ek_project_detail.id_project', $id);
        return $data;
    }
}