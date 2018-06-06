<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
class ProjectDetail extends Model
{
    //
    protected $table = 'ek_project_detail';
    protected $primaryKey = 'id_project_detail';
    public $incrementing = false;

    protected $fillable = [
        'id_project','id_form','nilai_form','status', 'created_by','updated_by','status'
    ];



    
}