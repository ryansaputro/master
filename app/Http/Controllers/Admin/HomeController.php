<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use PeterColes\Countries\CountriesFacade;
use Calendar;
use DB;
use DateTime;
use URL;
use App\FollowUp;
use App\Meeting;
use App\User;
use Illuminate\Support\Facades\Auth;
use Zizaco\Entrust\EntrustFacade as Entrust;

class HomeController extends Controller
{
    private $controller = 'home';
    public function __construct()
    {
        $this->middleware('auth');
    }

    private function title(){
        return __('main.dashboard');
    }

    public function index(){
      $controller =$this->controller;
      $pages_title="";
      $page_active='dashboard';
      return view('admin.home',compact('controller','page_active','pages_title'));

    }
}
