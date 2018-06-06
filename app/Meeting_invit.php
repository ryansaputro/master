<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;


class Meeting_invit extends Model
{
	protected $table = 'ek_meeting_invitation';
    protected $primaryKey = 'id_invite';


    protected $fillable = ['id_meeting','id_users','created_by','updated_by','created_at','updated_at','status'];
}