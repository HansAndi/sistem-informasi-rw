<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('penduduk', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sosial_id')->default(0);
            $table->foreignId('kk_id')->default(0);
            $table->char('nik', 16);
            $table->string('nama', 128);
            $table->string('tempat_lahir', 128);
            $table->date('tanggal_lahir');
            $table->enum('jenis_kelamin', ['L','P']);
            $table->string('golongan_darah', 2);
            $table->string('agama', 16);
            $table->string('status_perkawinan', 32);
            $table->string('pekerjaan', 128);
            $table->string('alamat', 256);
            $table->string('keterangan', 128);
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
        Schema::dropIfExists('penduduks');
    }
};
