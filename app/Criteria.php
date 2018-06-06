<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class Criteria extends Model
{
    //
    protected $table = 'kriteria';
    protected $primaryKey = 'id_kriteria';
    public $incrementing = true;
    public $timestamps = true;

    protected $fillable = ['id_kriteria', 'nama_kriteria', 'singkatan', 'status', 'additional', 'created_at', 'updated_at'];
}
