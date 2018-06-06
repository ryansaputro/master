<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

use DB;

class ProjectAndProduct extends Model
{
    //
    protected $table = 'ek_project_and_product';
    public $timestamps = false;
    public $incrementing = false;

    protected $fillable = ['id_project','id_client','id_product'];

}
