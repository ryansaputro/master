<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class Clousul extends Model
{
    //
    protected $table = 'klausul';
    protected $primaryKey = 'id_klausul';
    public $incrementing = true;
    public $timestamps = true;

    protected $fillable = ['id_klausul', 'id_kriteria', 'no_klausul', 'deskripsi', 'status', 'additional', 'created_at', 'updated_at'];
}
