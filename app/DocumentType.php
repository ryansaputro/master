<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
class DocumentType extends Model
{
    //
    protected $table = 'tipe_dokumen';
    protected $primaryKey = 'id_tipe_dokumen';


    public function get_data(){
    	$data = DB::table('tipe_dokumen')
        ->orderBy('urutan','ASC');
        return $data;
    }
}

