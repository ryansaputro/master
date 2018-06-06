<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class Org_role extends Model
{
    //
    protected $table = 'org_role';
    protected $primaryKey = 'id_divisi';
    public $incrementing = true;
    public $timestamps = true;

    protected $fillable = ['id_divisi', 'nama_org_divisi', 'deskripsi', 'status', 'additional', 'created_at', 'updated_at'];
}
