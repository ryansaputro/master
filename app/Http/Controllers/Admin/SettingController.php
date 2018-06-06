<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Auth;
use DB;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
class SettingController extends Controller
{
    private $controller = 'my_profile';
    //
	public function __construct()
    {
        $this->middleware('auth');
    }

    public function my_profile(){
        $data['page_active'] ="profile";
    	$data['pages_title'] =__('main.profile');
		$data['id_users'] = Auth::user()->id_users;
		$data['nik'] = Auth::user()->nik;
		$data['name'] = Auth::user()->name;
		$data['password'] = Auth::user()->password;
		$data['email'] = Auth::user()->email;
		$data['jenis_kelamin'] = Auth::user()->jenis_kelamin;
		$data['id_level_user'] = Auth::user()->id_level_user;
		$data['id_pos'] = Auth::user()->id_pos;
		$data['image'] = Auth::user()->image;
		$data['remember_token'] = Auth::user()->remember_token;
        $data['controller'] =$this->controller;
        return view('admin.my_profile')->with($data);
    }

    public function update_profile(Request $request){

    	$this->validate($request, [
	        'email' => 'required|email',
	    ]);
		$names =$request->names;
		$email =$request->email;
		$jenis_kelamin =$request->jenis_kelamin;
		$image =$request->image;
		$id_users =Auth::user()->id_users;
        $data = User::find($id_users);
        $data->name = $request->names;
        $data->email = $request->email;
        $data->jenis_kelamin = $request->jenis_kelamin;
        if(!empty($request->file('image'))){
            $file       = $request->file('image');
            $fileName   = $file->getClientOriginalName();
            $request->file('image')->move("images/profile/", $fileName);
            $data->image = $fileName;
        }
        $data->updated_by=$id_users;
        $data->save();
        
        return redirect(LaravelLocalization::getCurrentLocale().'/dashboard/my_profile')->with('message', 'success update data.');

        // return redirect('dashboard//my_profile')->with('message', 'success update data.');
    }

    public function update_password_profile(Request $request){
    	$data = User::find($request->id_users);
        $data->password = bcrypt($request->password);
        $data->updated_by=$request->id_users;
        $data->save();
        $result=array(
                    "data_result"=>array(
                        "class" => "success",
                        "message"=>"Success ! Change Password Success."
                    )
                );
        echo json_encode($result);
    }
}
