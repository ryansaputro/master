<?php
use Zizaco\Entrust\EntrustFacade as Entrust;
use App\Meeting;
use Illuminate\Support\Facades\Auth;
use App\User;

	function diset($set,$id_quotation)
	{
		@session_start();
		if (session()->has('diset')){
			$diset = array('diset'=>$id_quotation);
			Session::put($diset, array());
		}
			$diset = array('diset'=>array($id_quotation=>true));
			Session::put($diset);
	}

	 function diget($id_quotation)
	{	

		if(session()->has('diset')){
					$diset = session()->get('diset');
		 			if(isset($diset[$id_quotation])) return $diset[$id_quotation];
		 			else return false;
		}else{
					return false;
		}
	} 


	function arrGender() {
	    return array('L' => __('main.male'), 'P' => __('main.female'));
	}

	function GetNotofyFollowup(){
		$date=date('Y-m-d');
		
		if(Entrust::can('all-data-follow-up')){
			$get_data= DB::table('ek_follow_up')
						->join('ek_setting_calling', 'ek_follow_up.id_status_call', '=', 'ek_setting_calling.id_status_call')
						->where('follow_date_next','like','%'.$date.'%')
						->where('ek_follow_up.status_hide', 'Y')
    					->where('ek_setting_calling.status_followed','F');
	    }else{
	    	$get_data= DB::table('ek_follow_up')
					->join('ek_setting_calling', 'ek_follow_up.id_status_call', '=', 'ek_setting_calling.id_status_call')
					->where('follow_date_next','like','%'.$date.'%')
					->where('ek_follow_up.status_hide', 'Y')
					->where('ek_setting_calling.status_followed','F')
					->where('ek_follow_up.created_by',Auth::user()->id_users);
	    }



		
                    			
        return $ceks=$get_data->count();
	}

	function GetNotofyFollowupLate(){
		$date=date('Y-m-d');
        if(Entrust::can('all-data-follow-up')){
			$get_data=DB::table('ek_follow_up')
						->join('ek_setting_calling', 'ek_follow_up.id_status_call', '=', 'ek_setting_calling.id_status_call')
						->where('follow_date_next','<',''.$date.'')
						->where('ek_follow_up.status_hide', 'Y')
    					->where('ek_setting_calling.status_followed','F');
	    }else{
	    	$get_data= DB::table('ek_follow_up')
						->join('ek_setting_calling', 'ek_follow_up.id_status_call', '=', 'ek_setting_calling.id_status_call')
						->where('follow_date_next','<',''.$date.'')
						->where('ek_follow_up.status_hide', 'Y')
    					->where('ek_setting_calling.status_followed','F')
    					->where('ek_follow_up.created_by',Auth::user()->id_users);
	    }


        return $ceks=$get_data->count();	
        
	}


	function GetNotofyWin()
	{	
		$date=date('Y-m-d');
        if(Entrust::can('all-data-win-project')){
        	return Meeting::where('type_meeting','win')
                 ->where('status_meeting','Y')
                 ->where('start_meeting','like','%'.$date.'%')->count();
        }else{

        	return Meeting::where('type_meeting','win')
                 ->where('status_meeting','Y')
                 ->where('start_meeting','like','%'.$date.'%')
                 ->where('ek_meeting.created_by',Auth::user()->id_users)->count();
        }
	}

	function GetNotofyWinLate()
	{
		$date=date('Y-m-d');
		if(Entrust::can('all-data-win-project')){
        	return Meeting::where('type_meeting','win')
                 ->where('status_meeting','Y')
                 ->where('start_meeting','<',''.$date.'')->count();
        }else{

        	return Meeting::where('type_meeting','win')
                 ->where('status_meeting','Y')
                 ->where('start_meeting','<',''.$date.'')
                 ->where('ek_meeting.created_by',Auth::user()->id_users)->count();
        }	
	}

	function GetNotofyNewWin(){
		if(Entrust::can('all-data-win-project')){
			return $get_data= DB::table('ek_follow_up')
					->join('ek_setting_calling', 'ek_follow_up.id_status_call', '=', 'ek_setting_calling.id_status_call')
					->where('ek_follow_up.status_meeting_on_win','N')
					->where('ek_setting_calling.status_name','Win')->count();
		}else{
			return $get_data= DB::table('ek_follow_up')
					->join('ek_setting_calling', 'ek_follow_up.id_status_call', '=', 'ek_setting_calling.id_status_call')
					->where('ek_setting_calling.status_name','Win')
					->where('ek_follow_up.status_meeting_on_win','N')
					->where('ek_follow_up.created_by',Auth::user()->id_users)->count();
		}

		return 20;
	}


	function formatSizeUnits($bytes)
{
    if ($bytes >= 1073741824)
    {
        $bytes = number_format($bytes / 1073741824, 2) . ' GB';
    }
    elseif ($bytes >= 1048576)
    {
        $bytes = number_format($bytes / 1048576, 2) . ' MB';
    }
    elseif ($bytes >= 1024)
    {
        $bytes = number_format($bytes / 1024, 2) . ' KB';
    }
    elseif ($bytes > 1)
    {
        $bytes = $bytes . ' bytes';
    }
    elseif ($bytes == 1)
    {
        $bytes = $bytes . ' byte';
    }
    else
    {
        $bytes = '0 bytes';
    }

    return $bytes;
}