<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
class Product extends Model
{
    //
    protected $table = 'ek_product';
    protected $primaryKey = 'id_product';



    public function get_data(){
    	$data = DB::table('ek_product')
        ->where('status', 'Y')
        ->orderBy('urutan','ASC');
        return $data;
    }
}