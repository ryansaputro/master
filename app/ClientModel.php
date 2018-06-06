<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
class ClientModel extends Model
{
    //
    protected $table = 'ek_client';
    protected $primaryKey = 'id_client';
    public $incrementing = false;

    protected $fillable = [
        'nama_client','alamat','telp','email', 'created_by','updated_by','status'
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
    	$data = DB::table('ek_client')
        ->select('ek_client.*')
        ->where('status', 'Y')
        ->orderBy('id_client','DESC');
        return $data;
    }

    public function get_data_client($id)
    {
        # code...
         $data = DB::table('ek_client_detail')
        ->select('ek_client_detail.*','ek_form_client.*')

        ->join('ek_form_client', 'ek_client_detail.id_form', '=', 'ek_form_client.id_form_client')
        ->where('ek_form_client.status','Y')
        ->where('ek_client_detail.id_client', $id);
        return $data;
    }
}