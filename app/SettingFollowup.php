<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
class SettingFollowup extends Model
{
    //
    protected $table = 'ek_setting_calling';
    protected $primaryKey = 'id_status_call';

   

    

    public function get_data(){
    	$data = DB::table('ek_setting_calling')
        ->select('ek_setting_calling.*')
        ->where('ek_setting_calling.status', 'Y')
        ->orderBy('urutan','ASC');
        return $data;
    }
}