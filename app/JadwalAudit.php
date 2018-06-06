<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
use DB;


class JadwalAudit extends Model
{
    //
    protected $table = 'jadwal_audit';
    protected $primaryKey = 'id_jadwal_audit';
}
