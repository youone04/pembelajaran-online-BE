<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

// require_once('vendor/autoload.php');
use \Firebase\JWT\JWT;
use Illuminate\Http\Response;

use Illuminate\Support\Facades\Validator;
use Illuminate\Contracts\Encryption\DecryptException;

use App\M_Peserta;
use App\M_Soal;
use App\M_Jawaban;
use App\M_Skor;

class Ujian extends Controller
{
    //
    public function listSoal(Request $request){

          $token = $request->token;
      
          $tokenDb = M_Peserta::where('token',$token)->count();
          if($tokenDb > 0){
            $key = env('APP_KEY');
            $decoded = JWT::decode($token, $key, array('HS256'));
           $decoded_array = (array) $decoded;
            if($decoded_array['extime'] > time()){

              $skor = M_Skor::where('id_peserta',$decoded_array['id_peserta'])->where('status','1')->first();
              $cal_skor = M_Skor::where('id_peserta',$decoded_array['id_peserta'])->where('status','1')->count();
              $id_s = "";
              if($cal_skor > 0){
                $id_s = M_Skor::where('id_peserta',$decoded_array['id_peserta'])->where('status','1')->first();

              }else{
                M_Skor::create([
                  'id_peserta' => $decoded_array['id_peserta']
                ]);
                $id_s = M_Skor::where('id_peserta',$decoded_array['id_peserta'])->where('status','1')->first();
              }
              
              $jawaban = M_Jawaban::where('id_peserta', $decoded_array['id_peserta'])->first();
              $jum_jawaban = M_Jawaban::where('id_peserta', $decoded_array['id_peserta'])->where('id_skor',$skor->id_skor)->count();

              
              $jumlah_soal = M_Soal::count();
              $max_rand = $jumlah_soal - 10;
              $mulai = rand(0,$max_rand);
              $soal = M_Soal::skip($mulai)->take(10)->get();
              // if($jum_jawaban % 10 == 0){
              //   $jawab = $jawaban->first();
              //   $soal = M_Soal::skip(($jawab->id_soal)-1)->take(10)->get();
              // }else{
              //   $soal = M_Soal::skip($mulai)->take(10)->get();
              // }


             

              

              $data = array();
              foreach($soal as $p){
                $data[] = array(
                  'id_soal' => $p->id_soal,
                  'pertanyaan' => $p->pertanyaan,
                  'opsi1' => $p->opsi1,
                  'opsi2' => $p->opsi2,
                  'opsi3' => $p->opsi3,
                  'opsi4' => $p->opsi4,
                  'jumlah_jawaban' => $jum_jawaban
                );
    
              }

            

            return response()->json([
              'status' => 'berhasil',
              'message' => 'Data berhasil diambil',
              'id_skor' => $id_s->id_skor,
              'data' =>  $data
            ]);  
            }else{
              return response()->json([
                'status' => 'gagal',
                  'message' => 'Token kadaluarsa'
                ]);
            }
          }else{
            return response()->json([
              'status' => 'gagal',
                'message' => 'Token Tidak Valid'
              ]);
          }
        
            
      
      }

      public function jawab(Request $request){

        $token = $request->token;
      
          $tokenDb = M_Peserta::where('token',$token)->count();
          if($tokenDb > 0){
            $key = env('APP_KEY');
            $decoded = JWT::decode($token, $key, array('HS256'));
           $decoded_array = (array) $decoded;
            if($decoded_array['extime'] > time()){
              
              $soal = M_Soal::where('id_soal',$request->id_soal)->get();

              foreach($soal as $s){
                if($request->jawaban == $s->jawaban){
                  if(M_Jawaban::create([
                    'id_peserta' => $decoded_array['id_peserta'],
                    'id_soal' => $s->id_soal,
                    'jawaban' => $request->jawaban,
                    'id_skor' => $request->id_skor,
                    'status_jawaban' => '1'
                  ])){
                    return response()->json([
                      'status' => 'berhasil',
                      'message' => 'Data berhasil diambil',
                    ]); 
                  }else{
                    return response()->json([
                      'status' => 'gagal',
                        'message' => 'Data gagal disimpan'
                      ]);
                  }
                }else{
                  if(M_Jawaban::create([
                    'id_peserta' => $decoded_array['id_peserta'],
                    'id_soal' => $s->id_soal,
                    'jawaban' => $request->jawaban,
                    'id_skor' => $request->id_skor,
                    'status_jawaban' => '0'
                  ])){
                    return response()->json([
                      'status' => 'berhasil',
                      'message' => 'Data berhasil diambil',
                    ]); 
                  }else{
                    return response()->json([
                      'status' => 'gagal',
                        'message' => 'Data gagal disimpan'
                      ]);
                  }
                }
              }

             
            }else{
              return response()->json([
                'status' => 'gagal',
                  'message' => 'Token kadaluarsa'
                ]);
            }
          }else{
            return response()->json([
              'status' => 'gagal',
                'message' => 'Token Tidak Valid'
              ]);
          }
        
            
      
      }

      public function hitungSkor(Request $request){

        $token = $request->token;
      
          $tokenDb = M_Peserta::where('token',$token)->count();
          if($tokenDb > 0){
            $key = env('APP_KEY');
            $decoded = JWT::decode($token, $key, array('HS256'));
            $decoded_array = (array) $decoded;
            if($decoded_array['extime'] > time()){
              
              $id_s = M_Skor::where('id_peserta',$decoded_array['id_peserta'])->where('status','1')->first();
              $jawaban = M_Jawaban::where('status_jawaban','1')->where('id_skor', $id_s->id_skor)->count();
              return response()->json([
                'status' => 'berhasil',
                'skor' => $jawaban,
                 
                ]);

             
            }else{
              return response()->json([
                'status' => 'gagal',
                  'message' => 'Token kadaluarsa'
                ]);
            }
          }else{
            return response()->json([
              'status' => 'gagal',
                'message' => 'Token Tidak Valid'
              ]);
          }
        
            
      
      }

      public function selesaiUjian(Request $request){

        $token = $request->token;
      
          $tokenDb = M_Peserta::where('token',$token)->count();
          if($tokenDb > 0){
            $key = env('APP_KEY');
            $decoded = JWT::decode($token, $key, array('HS256'));
            $decoded_array = (array) $decoded;
            if($decoded_array['extime'] > time()){
              
              $id_s = M_Skor::where('id_peserta',$decoded_array['id_peserta'])->where('status','1')->first();
              if(M_Skor::where('id_skor',$id_s->id_skor)->update([
                'status' => '0',
              ])){
                return response()->json([
                  'status' => 'berhasil',
                  'message' => 'data berhasil diubah',
                   
                  ]);
              }
              

             
            }else{
              return response()->json([
                'status' => 'gagal',
                  'message' => 'Token kadaluarsa'
                ]);
            }
          }else{
            return response()->json([
              'status' => 'gagal',
                'message' => 'Token Tidak Valid'
              ]);
          }
        
            
      
      }
}
