<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use App\DocumentType;
use App\Levelusers;
use Illuminate\Support\Facades\Auth;
use DB;
use Illuminate\Support\Facades\Hash;
use App\Criteria;
use Illuminate\Support\Facades\Storage;
use File;

class DocumentController extends Controller
{
  /**
   * Create a new controller instance.
   *
   * @return void
   */
  private $controller = 'document';

  private function title(){
      return "Master Document";
  }

  public function __construct()
  {
      $this->middleware('auth');
  }

  public function index()
  {
      $data['page_active'] ="document";
      $data['controller'] =$this->controller;
      $data['pages_title'] =$this->title();
      $data['doc_list'] = DB::table('ek_doc')->where('status', 'Y')->get();
      $data['kriteria'] = Criteria::where('status', 'Y')->get();
      $data['tipe'] = DB::table('tipe_dokumen')->get();
      $data['divisi'] = DB::table('org_role')->get();
      $data['search'] = "";

      File::deleteDirectory('assets/tmp/', true);

      return view('admin.document.list')->with($data);
  }

  public function search(Request $request)
  {
    $data['page_active'] ="document";
    $data['controller'] =$this->controller;
    $data['pages_title'] =$this->title();
    $data['doc_list'] = DB::table('ek_doc')->where('status', 'Y')->get();
    $data['kriteria'] = Criteria::where('status', 'Y')->get();
    $data['tipe'] = DB::table('tipe_dokumen')->get();
    $data['divisi'] = DB::table('org_role')->get();

    $word = $request->word;
    $data['word'] = $word;
    $data['search'] = "AND (no_dokumen like '%".$word."%' OR judul_dokumen like '%".$word."%' OR tag like '%".$word."%')";

    return view('admin.document.list')->with($data);
  }

  public function create()
  {

      $data['page_active'] ="document";
      $data['controller'] =$this->controller;
      $data['pages_title'] =$this->title();
      $data['kriteria'] = Criteria::where('status', 'Y')->get();
      $data['divisi'] = DB::table('org_role')->get();
      $data['tipe'] = DB::table('tipe_dokumen')->get();

      return view('admin.document.create')->with($data);
  }

  public function upload(Request $request){
    // dd($request);
    $file = $request->file('docs');
    $id_divisi = Auth::user()->id_org_role;
    $destinationPath = 'assets/docs/f'.$id_divisi.'_'.md5($id_divisi).'/';

    if(!File::exists($destinationPath)) {
      File::makeDirectory($destinationPath, 0775, true);
    }
    // dd($file->getClientOriginalExtension());

    $docs = new \App\Doc;
    $docs->id_tipe_dokumen = $request->id_tipe_dokumen;
    $docs->id_divisi = $request->id_divisi;
    $docs->no_dokumen = $request->no_doc;
    $docs->judul_dokumen = $request->nama;
    $docs->deskripsi_dokumen = $request->desc;
    $docs->urutan = 0;
    $docs->tag = $request->tags;
    $docs->status = "Y";
    $docs->save();

    $nama = md5($docs->id_dokumen."_0");

    DB::table('dokumen_kriteria')->insert([
      'id_dokumen' => $docs->id_dokumen,
      'id_kriteria' => $request->id_kriteria,
    ]);

    DB::table('dokumen_detail')->insert([
      'id_dokumen' => $docs->id_dokumen,
      'nama_file' => $docs->id_dokumen."_".$nama,
      'revisi' => 0,
      'status' => 'Y',
      'tanggal_berlaku' => date('Y-m-d'),
      'upload_oleh' => Auth::user()->id_users,
      'additional' => $file->getClientOriginalExtension(),
      // 'additional' => $file->getSize(),
    ]);

    $judul = str_replace(" ", "_", strtolower($docs->judul));
    $no = str_replace(" ", "_", strtolower($docs->no));
    // $rev = ($docs->rev == 0 ? "" : "_".$docs->rev);

    $file->move($destinationPath, $docs->id_dokumen."_".$nama.".".$file->getClientOriginalExtension());

    return redirect(route('document.index'));
  }

  public function delete(Request $request){
    // dd($request);

    $docs = \App\Doc::find($request->id);
    $docs->status = "N";
    $docs->save();

    return redirect(route('document.index'));
  }

  /**
   * Display the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function view(Request $request){
    // dd($id);

    $data['id_dokumen'] = $request->id;
    $data['nama_file'] = DB::table('dokumen_detail')->where('id_dokumen', $request->id)->where('revisi', $request->rev)->value('nama_file');
    $data['ext_file'] = DB::table('dokumen_detail')->where('id_dokumen', $request->id)->where('revisi', $request->rev)->value('additional');
    $data['divisi'] = $request->divisi;

    return view('admin.document.view')->with($data);
  }

  /**
   * Display the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function download(Request $request){
    $data['page_active'] ="document";
    $data['controller'] =$this->controller;
    $data['pages_title'] =$this->title();
    $data['doc_list'] = DB::table('ek_doc')->where('status', 'Y')->get();
    $data['kriteria'] = Criteria::where('status', 'Y')->get();
    $data['tipe'] = DB::table('tipe_dokumen')->get();
    $data['divisi'] = DB::table('org_role')->get();
    $data['search'] = "";

    $tmp = explode("_", $request->id);
    $judul = str_replace(" ", "_", strtolower($request->judul));
    $no = str_replace(" ", "_", strtolower($request->no));
    $rev = ($request->rev == 0 ? "" : "_".$request->rev);

    if (md5($tmp[0]."_".$request->rev) ==  $tmp[1]) {
      $ext = DB::table('dokumen_detail')->where('id_dokumen', $tmp[0])->where('revisi', $request->rev)->value('additional');
      $fix = asset('assets/docs/f'.$request->divisi."_".md5($request->divisi)."/".$request->id.".".$ext);
      // $down = asset('assets/docs/f'.$request->divisi."_".md5($request->divisi)."/".$judul."_".$no.$rev.".".$ext);
      $fix = 'assets/docs/f'.$request->divisi."_".md5($request->divisi)."/".$request->id.".".$ext;
      $down = 'assets/tmp/f'.$request->divisi."_".md5($request->divisi)."/".$judul."_".$no.$rev.".".$ext;

      $destinationPath = 'assets/tmp/f'.$request->divisi."_".md5($request->divisi)."/";
      if(!File::exists($destinationPath)) {
        File::makeDirectory($destinationPath, 0775, true);
      }
      File::copy($fix, $down);
      return redirect($down);
    }
    // return view('admin.document.list')->with($data);
  }
}
