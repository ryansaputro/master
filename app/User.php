<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Zizaco\Entrust\Traits\EntrustUserTrait;
use DB;


class User extends Authenticatable
{
    use Notifiable,EntrustUserTrait;

    protected $table = 'ek_users';


    protected $primaryKey = 'id_users';
    public $incrementing = false;

    protected $fillable = [
        'id_users','nik','username', 'password','email','jenis_kelamin','id_level_user','status','created_by','update_by','image'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function get_users_data($id_org){
        if ($id_org != '') {
          // code...
          $users = DB::table('ek_users')
          ->select('ek_users.*')
          ->where('ek_users.id_org_role', $id_org)
          ->where('ek_users.status', 'Y')
          ;
        } else {
          $users = DB::table('ek_users')
          ->select('ek_users.*')
          ->where('ek_users.status', 'Y')
          ;
        }
        return $users;
    }

    public function get_users_data_pos($id_pos){
        $users = DB::table('ek_users')
        ->select('ek_users.*','ek_pos.nama_pos')
        ->leftJoin('ek_pos', 'ek_users.id_pos', '=', 'ek_pos.id_pos')
        ->where('ek_users.id_pos',$id_pos)
        ->where('ek_users.status', 'Y');
        return $users;
    }

    public function get_users_data_byid($id){
        $users = DB::table('ek_users')
        ->select('ek_users.*')
        ->where('ek_users.status', 'Y')
        ->where('ek_users.id_users',$id);
        return $users;
    }


    public function get_role_users($id){
        $role_user= DB::table('role_user')
        ->select('*')
        ->Join('roles', 'role_user.role_id', '=', 'roles.id')
        ->where('user_id',$id);
        return $role_user;
    }

    public function get_role_org($id){
        $role_org= DB::table('org_role')
        ->select('*')
        ->where('id_divisi', $id);
        return $role_org;
    }

    public function get_data_users_level(){
        $product =DB::table('ek_level_users')->where('status','Y');
        return $product;
    }

    public static function autonumber($table,$primary,$prefix){

        $q=DB::table($table)->select(DB::raw('MAX(RIGHT('.$primary.',5)) as kd_max'));

        if($q->count()>0)
        {
            foreach($q->get() as $k)
            {
                $tmp = ((int)$k->kd_max)+1;
                $kd = $prefix.sprintf("%05s", $tmp);
            }
        }
        else
        {
            $kd = $prefix."00001";
        }
        return $kd;
    }


}
