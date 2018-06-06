<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Database extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::create('org_role', function (Blueprint $table) {
          $table->increments('id_divisi');
          $table->string('nama_org_divisi',100);
          $table->string('deskripsi',100);
          $table->enum('status',['Y','N']);
          $table->string('additional')->nullable();
          $table->timestamps();
      });

      Schema::create('kriteria', function (Blueprint $table) {
          $table->increments('id_kriteria');
          $table->string('nama_kriteria',255);
          $table->string('singkatan',255);
          $table->enum('status',['Y','N']);
          $table->string('additional')->nullable();
          $table->timestamps();
      });

      Schema::create('klausul', function (Blueprint $table) {
          $table->increments('id_klausul');
          $table->integer('id_kriteria')->unsigned();
          $table->string('no_klausul',255);
          $table->string('deskripsi',255);
          $table->enum('status',['Y','N']);
          $table->string('additional')->nullable();
          $table->timestamps();
      });

      Schema::create('dokumen', function (Blueprint $table) {
          $table->increments('id_dokumen');
          $table->integer('id_tipe_dokumen')->unsigned();
          $table->integer('id_divisi');
          $table->string('no_dokumen',255);
          $table->string('judul_dokumen',255);
          $table->text('deskripsi_dokumen');
          $table->integer('urutan');
          $table->string('tag',255);
          $table->enum('status',['Y','N']);
          $table->string('additional')->nullable();
          $table->timestamps();
      });

      Schema::create('dokumen_kriteria', function (Blueprint $table){
          $table->integer('id_dokumen')->unsigned();
          $table->integer('id_kriteria')->unsigned();
          $table->string('additional')->nullable();
          $table->timestamps();
      });

      Schema::create('tipe_dokumen', function (Blueprint $table){
          $table->increments('id_tipe_dokumen');
          $table->string('tipe_dokumen',100);
          $table->integer('urutan');
          $table->enum('status',['Y','N']);
          $table->string('additional')->nullable();
          $table->timestamps();
      });

      Schema::create('dokumen_detail', function (Blueprint $table){
          $table->increments('id_dokumen_detail');
          $table->integer('id_dokumen')->unsigned();
          $table->string('nama_file',100);
          $table->dateTime('tanggal_berlaku');
          $table->string('ttd_oleh',100);
          $table->string('upload_oleh',100);
          $table->integer('revisi');
          $table->enum('status',['Y','N']);
          $table->string('additional')->nullable();
          $table->timestamps();
      });

      Schema::create('jadwal_audit_kriteria', function (Blueprint $table){
          $table->increments('id_jadwal_audit_kriteria');
          $table->integer('id_jadwal_audit')->unsigned();
          $table->enum('status',['Y','N']);
          $table->string('additional')->nullable();
          $table->timestamps();
      });

      Schema::create('jadwal_audit', function (Blueprint $table){
          $table->increments('id_jadwal_audit');
          $table->dateTime('tangal_mulai');
          $table->dateTime('tanggal_selesai');
          $table->enum('status',['Y','N']);
          $table->string('additional')->nullable();
          $table->timestamps();
      });

      Schema::create('jadwal_audit_auditor', function (Blueprint $table){
          $table->increments('id_jadwal_audit_auditor');
          $table->integer('id_jadwal_audit')->unsigned();
          $table->integer('id_auditor')->unsigned();
          $table->smallInteger('posisi')->unsigned();
           // 1=Lead Auditor,  2=Auditor, 3=Technical expert ,4=Observer
          $table->string('additional')->nullable();
          $table->timestamps();
      });

      Schema::create('jadwal_audit_tugas', function (Blueprint $table){
          $table->increments('id_jadwal_audit_tugas');
          $table->date('tanggal');
          $table->time('waktu_mulai');
          $table->time('waktu_selesai');
          $table->integer('id_divisi')->unsigned();
          $table->integer('id_auditor')->unsigned();
          $table->integer('id_auditee')->unsigned();
          $table->string('additional')->nullable();
          $table->timestamps();
      });

      Schema::create('ceklis', function (Blueprint $table){
          $table->increments('id_ceklis');
          $table->integer('id_kriteria')->unsigned();
          $table->integer('id_klausul')->unsigned();
          $table->integer('id_divisi')->unsigned();
          $table->string('pertanyaan',255);
          $table->integer('urutan');
          $table->enum('status',['Y','N']);
          $table->string('additional')->nullable();
          $table->timestamps();
      });

      Schema::create('jadwal_audit_ceklis', function (Blueprint $table){
          $table->increments('id_jadwal_audit_ceklis');
          $table->integer('id_jadwal_audit')->unsigned();
          $table->integer('id_ceklis')->unsigned();
          $table->string('additional')->nullable();
          $table->timestamps();
      });

      Schema::create('laporan_ceklis_audit', function (Blueprint $table){
          $table->increments('id_laporan_ceklis_audit');
          $table->integer('id_jadwal_audit')->unsigned();
          $table->integer('id_ceklis')->unsigned();
          $table->integer('id_auditor')->unsigned();
          $table->integer('compliant');
          $table->integer('ofi');
          $table->integer('minor');
          $table->integer('major');
          $table->text('evidence');
          $table->string('additional')->nullable();
          $table->timestamps();
      });
      Schema::create('jenis_temuan', function (Blueprint $table) {
          $table->increments('id_temuan');
          $table->string('nama_temuan',255);
          $table->enum('status',['Y','N']);
          $table->string('additional')->nullable();
          $table->timestamps();
      });
      Schema::create('ncr', function (Blueprint $table){
          $table->increments('id_ncr');
          $table->integer('id_laporan_ceklis_audit')->unsigned();
          $table->integer('kategori_temuan')->unsigned();
          // 1=minor. 2=major
          $table->integer('pic')->unsigned();
          $table->integer('auditor'); //id_auditor
          $table->integer('id_temuan');
          $table->date('tanggal_temuan');
          $table->text('ketidaksesuaian');
          $table->text('analisa_masalah');
          $table->integer('faktor');
          // 1=Man, 2=Material, 3=Method, 4=Machine, 5=Money, 6=Environtment
          $table->text('rencana_koreksi');
          $table->integer('pic_rencana_koreksi');
          $table->date('tanggal_rencana_koreksi_selesai');
          $table->text('hasil_rencana_koreksi');
          $table->integer('efektifitas_koreksi');
          // 1=Efektif Menyelesaikan Masalah, 2=Memerlukan Tindak Lanjut
          $table->text('komentar_verifikasi');
          $table->integer('verifikasi_oleh');
          $table->string('additional')->nullable();
          $table->timestamps();
      });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
