<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
class FollowUp extends Model
{
    //
    protected $table = 'ek_follow_up';
    protected $primaryKey = 'id_follow_up';


    protected $fillable = ['id_client','id_project','id_status_call','source','follow_date','follow_date_next','followed_by','number_of_contacts','status_hide','remarks','status_meeting_on_win','created_by','updated_by','created_at','updated_at','status'];


    public function get_data(){
		$data = DB::table('ek_follow_up')
	        ->select('ek_follow_up.id_follow_up', 'ek_follow_up.source', 'ek_follow_up.follow_date','ek_follow_up.follow_date_next' ,'ek_follow_up.followed_by','ek_follow_up.status_hide','ek_follow_up.remarks', 'ek_follow_up.number_of_contacts','ek_setting_calling.id_status_call','ek_setting_calling.status_name','ek_setting_calling.days_value','ek_client.id','ek_client.id_client','ek_client.nama_client','ek_project.id_project','ek_project.project_id', 'ek_project.nama_project', 'ek_users.name')
	        ->join('ek_setting_calling', 'ek_follow_up.id_status_call', '=', 'ek_setting_calling.id_status_call')
	        ->join('ek_client', 'ek_follow_up.id_client', '=', 'ek_client.id_client')
	        ->join('ek_project', 'ek_follow_up.id_project', '=', 'ek_project.id_project')
	        ->join('ek_users', 'ek_follow_up.followed_by', '=', 'ek_users.id_users')
	        ->where('ek_follow_up.status', 'Y')
	        ->where('ek_follow_up.status_hide', 'Y')
	        ->where('ek_setting_calling.status_followed','F');
        return $data;
    }

    public function get_data_meeting(){
		$data = DB::table('ek_follow_up')
	        ->select('ek_follow_up.id_follow_up', 'ek_follow_up.source', 'ek_follow_up.follow_date','ek_follow_up.follow_date_next' ,'ek_follow_up.followed_by','ek_follow_up.status_hide','ek_follow_up.remarks', 'ek_follow_up.number_of_contacts','ek_setting_calling.id_status_call','ek_setting_calling.status_name','ek_setting_calling.days_value','ek_client.id','ek_client.id_client','ek_client.nama_client','ek_project.id_project','ek_project.project_id', 'ek_project.nama_project', 'ek_users.name','ek_meeting.start_meeting','ek_meeting.end_meeting','ek_meeting.status_meeting')
	        ->join('ek_setting_calling', 'ek_follow_up.id_status_call', '=', 'ek_setting_calling.id_status_call')
	        ->join('ek_client', 'ek_follow_up.id_client', '=', 'ek_client.id_client')
	        ->join('ek_project', 'ek_follow_up.id_project', '=', 'ek_project.id_project')
	        ->join('ek_users', 'ek_follow_up.followed_by', '=', 'ek_users.id_users')
	        ->join('ek_meeting', 'ek_follow_up.id_follow_up', '=', 'ek_meeting.id_follow_up')
	        ->where('ek_follow_up.status', 'Y')
	        ->where('ek_follow_up.status_hide', 'Y')
	        ->where('ek_meeting.status_meeting', 'Y')
	        ->where('ek_setting_calling.status_followed','F');
        return $data;
    }


    public function get_data_win(){
		$data = DB::table('ek_follow_up')
	        ->select('ek_follow_up.id_follow_up', 'ek_follow_up.source', 'ek_follow_up.follow_date','ek_follow_up.follow_date_next' ,'ek_follow_up.followed_by','ek_follow_up.status_hide','ek_follow_up.remarks', 'ek_follow_up.number_of_contacts','ek_setting_calling.id_status_call','ek_setting_calling.status_name','ek_setting_calling.days_value','ek_client.id','ek_client.id_client','ek_client.nama_client','ek_project.id_project','ek_project.project_id', 'ek_project.nama_project', 'ek_users.name')
	        ->join('ek_setting_calling', 'ek_follow_up.id_status_call', '=', 'ek_setting_calling.id_status_call')
	        ->join('ek_client', 'ek_follow_up.id_client', '=', 'ek_client.id_client')
	        ->join('ek_project', 'ek_follow_up.id_project', '=', 'ek_project.id_project')
	        ->join('ek_users', 'ek_follow_up.followed_by', '=', 'ek_users.id_users')
	        ->where('ek_follow_up.status', 'Y')
	        ->where('ek_setting_calling.status_name', 'Win');
        return $data;
    }


    function get_data_by_project($id_project){
    	$data = DB::table('ek_follow_up')
	        ->select('ek_follow_up.id_follow_up', 'ek_follow_up.source', 'ek_follow_up.follow_date','ek_follow_up.follow_date_next' ,'ek_follow_up.followed_by','ek_follow_up.status_hide','ek_follow_up.remarks', 'ek_follow_up.number_of_contacts','ek_setting_calling.id_status_call','ek_setting_calling.status_name','ek_setting_calling.days_value','ek_client.id','ek_client.id_client','ek_client.nama_client','ek_project.id_project','ek_project.project_id', 'ek_project.nama_project', 'ek_users.name')
	        ->join('ek_setting_calling', 'ek_follow_up.id_status_call', '=', 'ek_setting_calling.id_status_call')
	        ->join('ek_client', 'ek_follow_up.id_client', '=', 'ek_client.id_client')
	        ->join('ek_project', 'ek_follow_up.id_project', '=', 'ek_project.id_project')
	        ->join('ek_users', 'ek_follow_up.followed_by', '=', 'ek_users.id_users')
	        ->where('ek_follow_up.status', 'Y')
	        ->where('ek_project.id_project',$id_project)
	        ->orderBy('ek_follow_up.follow_date','DESC');
        return $data;
    }

    function get_data_byid($id){
    	$data = DB::table('ek_follow_up')
	        ->select('ek_follow_up.id_follow_up', 'ek_follow_up.source', 'ek_follow_up.follow_date','ek_follow_up.follow_date_next' ,'ek_follow_up.followed_by','ek_follow_up.status_hide','ek_follow_up.remarks', 'ek_follow_up.number_of_contacts','ek_setting_calling.id_status_call','ek_setting_calling.status_name','ek_setting_calling.days_value','ek_client.id','ek_client.id_client','ek_client.nama_client','ek_project.id_project','ek_project.project_id', 'ek_project.nama_project','ek_project.deskripsi_project', 'ek_users.name')
	        ->join('ek_setting_calling', 'ek_follow_up.id_status_call', '=', 'ek_setting_calling.id_status_call')
	        ->join('ek_client', 'ek_follow_up.id_client', '=', 'ek_client.id_client')
	        ->join('ek_project', 'ek_follow_up.id_project', '=', 'ek_project.id_project')
	        ->join('ek_users', 'ek_follow_up.followed_by', '=', 'ek_users.id_users')
	        ->where('ek_follow_up.status', 'Y')
	        ->where('ek_follow_up.id_follow_up',$id)
	        ->orderBy('ek_follow_up.follow_date','DESC');
        return $data;
    }

}