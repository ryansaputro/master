<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use App\Levelusers;
use Illuminate\Support\Facades\Auth;
use DB;
use App\User_role;
use App\Permission;
class UsersController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    private $controller = 'users';

    private function title(){
        return __('main.user_list');
    }

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $data['page_active'] ="users";
        $data['controller'] =$this->controller;
        $data['pages_title'] =$this->title();
        $data['dt_level'] = DB::table('roles')->get();
        $data['org_level'] = DB::table('org_role')->get();
        return view('admin.users')->with($data);
    }

    public function get_users_data(){
        $org = Auth::user()->id_org_role;

        $user = new User();
        $data_users =$user->get_users_data($org)->get();
        $data = array();
        $no = 0;
        foreach ($data_users as $users) {
            $no++;
            $row = array();
            $row[] = $no;
            $row[] = $users->nik;
            $row[] = $users->name;
            $row[] = $users->email;
            $row[] = $users->jenis_kelamin;
            $option="<div class='hidden-sm hidden-xs action-buttons center'>";

            $user = User::where('id_users', '=', Auth::user()->id_users)->first();
            if($user->can(['users-edit'])){
                $option .="<a href='javascript:void(0)' onclick=edited('".$users->id_users."') class='green'><i class='ace-icon fa fa-pencil bigger-130'></i></a>";
            }
            if($user->can(['users-delete'])){
                $option .="<a href='javascript:void(0)' onclick=removed('".$users->id_users."') class='red'><i class='ace-icon fa fa-trash-o bigger-130'></i></a>";
            }

            $option .="</div>";
            $row[] =$option;
            $data[] = $row;
        }

        $output = array(
                        "data" => $data,
                    );
        return json_encode($output);
    }

    private function _validate_data(Request $request){
        $data = array();
        $data['error_string'] = array();
        $data['inputerror'] = array();
        $data['status'] = TRUE;

        if($request->nik == '')
        {
            $data['inputerror'][] = 'nik';
            $data['error_string'][] = 'nik is required';
            $data['status'] = FALSE;
        }

        if($request->names == '')
        {
            $data['inputerror'][] = 'names';
            $data['error_string'][] = 'name is required';
            $data['status'] = FALSE;
        }
        if($request->email == '')
        {
            $data['inputerror'][] = 'email';
            $data['error_string'][] = 'email is required';
            $data['status'] = FALSE;
        }

        if (!filter_var($request->email, FILTER_VALIDATE_EMAIL)) {
            $data['inputerror'][] = 'email';
            $data['error_string'][] = 'Invalid email format';
            $data['status'] = FALSE;
        }

        if($request->id_role == '')
        {
            $data['inputerror'][] = 'id_role';
            $data['error_string'][] = 'level user is required';
            $data['status'] = FALSE;
        }

        if($request->id_org == '')
        {
            $data['inputerror'][] = 'id_org';
            $data['error_string'][] = 'org user is required';
            $data['status'] = FALSE;
        }

        if($data['status'] === FALSE)
        {
            echo json_encode($data);
            exit();
        }
    }

    public function save_users(Request $request){
        $data = array();
        $data['error_string'] = array();
        $data['inputerror'] = array();
        $data['status'] = TRUE;

        $check_nik = DB::table('ek_users')->where('nik', $request->nik)->count();

        if($check_nik >= 1)
        {
            $data['inputerror'][] = 'nik';
            $data['error_string'][] = 'nik Can not be the same';
            $data['status'] = FALSE;
        }

        if($data['status'] === FALSE)
        {
            echo json_encode($data);
            exit();
        }

        UsersController::_validate_data($request);
        $table="ek_users";
        $primary="id_users";
        $prefik="K";
        $Kode_unik=User::autonumber($table,$primary,$prefik);

        $pk = new User;
        $pk->id_users = $Kode_unik;
        $pk->nik = $request->nik;
        $pk->name = $request->name;
        $pk->email = $request->email;
        $pk->jenis_kelamin = $request->jenis_kelamin;
        $pk->password =bcrypt($request->password);

        if(!empty($request->file('image'))){
            $file       = $request->file('image');
            $fileName   = $file->getClientOriginalName();
            $request->file('image')->move("images/profile/", $fileName);
            $pk->image = $fileName;
        }

        $pk->created_by =Auth::user()->id_users;
        $pk->status ='Y';
        $pk->id_org_role = $request->id_org;
        // $pk->id_pos ='W00001';
        $pk->save();

        $role_user = new User_role;
        $role_user->user_id=$Kode_unik;
        $role_user->role_id=$request->id_role;
        $role_user->save();

        echo json_encode(array("status" => TRUE));
    }


    public function update_users(Request $request){
        UsersController::_validate_data($request);
        $pk = User::find($request->id_users);
        $pk->nik = $request->nik;
        $pk->name = $request->names;
        $pk->email = $request->email;
        $pk->jenis_kelamin = $request->jenis_kelamin;
        if(!empty($request->password)){
            $pk->password =bcrypt($request->password);
        }
        if(!empty($request->file('image'))){
            $file       = $request->file('image');
            $fileName   = $file->getClientOriginalName();
            $request->file('image')->move("images/profile/", $fileName);
            $pk->image = $fileName;
        }
        $pk->updated_by =Auth::user()->id_users;
        $pk->save();
        $role_id =$request->id_role;
        $user_id =$request->id_users;

        DB::table('role_user')
            ->where('user_id', $user_id)
            ->update(['role_id' => $role_id]);
        echo json_encode(array("status" => TRUE));
    }

    public function deleted_users(Request $request){
        $pk = User::find($request->id);
        $pk->status ="N";
        $pk->updated_by =Auth::user()->id_users;
        $pk->save();
        $result=array(
                "data_post"=>array(
                    "status"=>TRUE,
                    "class" => "success",
                    "message"=>"Success ! Deleted data"
                )
            );
        echo json_encode($result);
    }

    public function get_users_data_byid(Request $request){
        $id = $request->id;
        $user = new User();

        $data_users =$user->get_users_data_byid($id)->first();

        $data_role = $user->get_role_users($id)->first();
        $data_org = $user->get_role_org($data_users->id_org_role)->first();


        $data_return =array('data_users'=>$data_users,'data_role'=>$data_role,'data_org'=>$data_org);
        return response()->json($data_return);
    }
}
