<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class M_Skor extends Model
{
      //
      protected $table = 'tbl_skor';
      protected $primaryKey = 'tbl_skor';
      protected $fillable = ['id_skor','id_peserta','skor','status'];
}
