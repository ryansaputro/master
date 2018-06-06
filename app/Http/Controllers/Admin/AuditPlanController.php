<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

use DB;
use App\User;
use App\Levelusers;
use App\Permission;
use App\User_role;

class AuditPlanController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    private $controller = 'audit_plan';

    private function title(){
        return __('main.audit_plan');
    }

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $data['page_active'] = "audit_plan";
        $data['controller']  = $this->controller;
        $data['pages_title'] = $this->title();
        $data['audit_plan'] = DB::table('jadwal_audit')->get();
        return view('admin.'.$this->controller.'.list')->with($data);
    }

    public function create()
    {
      $data['page_active'] = "audit_plan";
      $data['controller']  = $this->controller;
      $data['pages_title'] = $this->title();
      $data['checklist'] = DB::table('ceklis')->get();

      return view('admin.'.$this->controller.'.create')->with($data);
    }

    public function update($id)
    {
      $data['page_active'] = "audit_plan";
      $data['controller']  = $this->controller;
      $data['pages_title'] = $this->title();

      $data['jadwal_audit'] = \App\JadwalAudit::find($id);
      $data['jadwal_kriteria'] = \App\JadwalKriteria::where('id_jadwal_audit', $id)->get();
      $data['lead_auditor'] = \App\JadwalAuditor::where('id_jadwal_audit', $id)->where('posisi', 1)->value('id_auditor');
      $data['jadwal_auditor'] = \App\JadwalAuditor::where('id_jadwal_audit', $id)->where('posisi', '!=', 1)->get();

      // dd($data);

      return view('admin.'.$this->controller.'.update')->with($data);
    }

    public function add(Request $request)
    {
      $jadwal_audit = new \App\JadwalAudit;
      $jadwal_audit->tanggal_mulai = $request->startDate;
      $jadwal_audit->tanggal_selesai = $request->endDate;
      $jadwal_audit->tech_expert = $request->techExpert;
      $jadwal_audit->observer = $request->observer;
      $jadwal_audit->status = 'Y';
      $jadwal_audit->save();

      foreach ($request->kriteria as $key => $value) {
        $jadwal_kriteria = new \App\JadwalKriteria;
        $jadwal_kriteria->id_jadwal_audit_kriteria = $request->kriteria[$key];
        $jadwal_kriteria->id_jadwal_audit = $jadwal_audit->id_jadwal_audit;
        $jadwal_kriteria->status = 'Y';
        $jadwal_kriteria->save();
      }

      $jadwal_auditor = new \App\JadwalAuditor;
      $jadwal_auditor->id_jadwal_audit = $jadwal_audit->id_jadwal_audit;
      $jadwal_auditor->id_auditor = $request->lead_auditor;
      $jadwal_auditor->posisi = 1;
      $jadwal_auditor->save();

      foreach ($request->auditor as $key => $value) {
        $jadwal_auditor = new \App\JadwalAuditor;
        $jadwal_auditor->id_jadwal_audit = $jadwal_audit->id_jadwal_audit;
        $jadwal_auditor->id_auditor = $request->auditor[$key];
        $jadwal_auditor->save();
      }

      $data['page_active'] = "audit_plan";
      $data['controller']  = $this->controller;
      $data['pages_title'] = $this->title();
      $data['audit_plan'] = DB::table('jadwal_audit')->get();
      return view('admin.'.$this->controller.'.list')->with($data);
    }
}
