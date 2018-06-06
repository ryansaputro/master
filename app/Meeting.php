<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
class Meeting extends Model
{
    protected $table = 'ek_meeting';
    protected $primaryKey = 'id_meeting';

    protected $fillable = ['id_follow_up','id_project','id_kendaraan','id_supir','start_meeting','end_meeting','budget_meeting','status_meeting','type_meeting','description','created_by','updated_by','created_at','updated_at','status'];




    public function get_meeting_by_followup($id){
    	$data = DB::table('ek_meeting')
			->select('ek_mobil.id_mobil','ek_mobil.nama_mobil','ek_supir.id_supir','ek_supir.nama_supir','ek_meeting.*','ek_project.nama_project','ek_follow_up.*','ek_client.nama_client')
			->join('ek_follow_up', 'ek_meeting.id_follow_up', '=', 'ek_follow_up.id_follow_up')
			->join('ek_supir','ek_meeting.id_supir','ek_supir.id_supir')
			->join('ek_mobil','ek_meeting.id_kendaraan','ek_mobil.id_mobil')
			->join('ek_project','ek_meeting.id_project','ek_project.id_project')
			->join('ek_client','ek_project.id_client','ek_client.id_client')
			->where('ek_meeting.status', 'Y')
	        ->where('ek_meeting.id_follow_up',$id);
	    return $data;
    }


    public function get_meeting_by_id($id){
    	$data = DB::table('ek_meeting')
			->select('ek_meeting.id_meeting','ek_mobil.id_mobil','ek_mobil.nama_mobil','ek_supir.id_supir','ek_supir.nama_supir','ek_meeting.*','ek_project.nama_project','ek_follow_up.*','ek_client.nama_client')
			->join('ek_follow_up', 'ek_meeting.id_follow_up', '=', 'ek_follow_up.id_follow_up')
			->join('ek_supir','ek_meeting.id_supir','ek_supir.id_supir')
			->join('ek_mobil','ek_meeting.id_kendaraan','ek_mobil.id_mobil')
			->join('ek_project','ek_meeting.id_project','ek_project.id_project')
			->join('ek_client','ek_project.id_client','ek_client.id_client')
	        ->where('ek_meeting.id_meeting',$id);
	    return $data;
    }

    public function get_meeting(){
    	$data = DB::table('ek_meeting')
			->select('ek_meeting.id_meeting','ek_mobil.id_mobil','ek_mobil.nama_mobil','ek_supir.id_supir','ek_supir.nama_supir','ek_meeting.*','ek_project.nama_project','ek_follow_up.*','ek_client.nama_client')
			->join('ek_follow_up', 'ek_meeting.id_follow_up', '=', 'ek_follow_up.id_follow_up')
			->join('ek_supir','ek_meeting.id_supir','ek_supir.id_supir')
			->join('ek_mobil','ek_meeting.id_kendaraan','ek_mobil.id_mobil')
			->join('ek_project','ek_meeting.id_project','ek_project.id_project')
			->join('ek_client','ek_project.id_client','ek_client.id_client');
	    return $data;
    }


    public function get_meeting_invite(){
    	$data = DB::table('ek_meeting_invitation')
			->select( 'ek_users.id_users','ek_users.name','ek_meeting.start_meeting', 'ek_meeting.end_meeting','ek_meeting.id_project','ek_project.nama_project','ek_meeting_invitation.id_invite','ek_meeting_invitation.status')
			->join('ek_meeting','ek_meeting.id_meeting','ek_meeting_invitation.id_meeting')
			->join('ek_project','ek_meeting.id_project','ek_project.id_project')
			->join('ek_users','ek_meeting_invitation.id_users','ek_users.id_users');

	    return $data;
    }
}

