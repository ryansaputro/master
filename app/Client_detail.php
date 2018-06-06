<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
class Client_detail extends Model
{
    //
    protected $table = 'ek_client_detail';
    protected $primaryKey = 'id_client_detail';
    public $incrementing = false;

    protected $fillable = [
        'id_client','id_form','nilai_form','status', 'created_by','updated_by'
    ];



    
}