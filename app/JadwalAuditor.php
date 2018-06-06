<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
use DB;


class JadwalAuditor extends Model
{
    //
    protected $table = 'jadwal_audit_auditor';
    protected $primaryKey = 'id_jadwal_audit_auditor';
}
