<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class Checklist extends Model
{
    //
    protected $table = 'ceklis';
    protected $primaryKey = 'id_ceklis';
    public $incrementing = true;
    public $timestamps = true;

    protected $fillable = ['id_klausul', 'id_kriteria', 'id_klausul', 'pertanyaan','urutan', 'status', 'additional', 'created_at', 'updated_at'];
}
