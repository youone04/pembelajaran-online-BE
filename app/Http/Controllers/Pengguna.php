<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

// require_once('vendor/autoload.php');
use \Firebase\JWT\JWT;
use Illuminate\Http\Response;

use Illuminate\Support\Facades\Validator;
use Illuminate\Contracts\Encryption\DecryptException;

use App\M_Pengguna;

class Pengguna extends Controller
{
    //

    public function tambahUser(Request $request){
        $validator = Validator::make($request->all(), [
            'nama' => 'required',
            'email' => 'required|unique:tbl_user',
            'password' => 'required',
            'token' => 'required'
          ]);

         
          if ($validator->fails()) {
            return response()->json([
              'status' => 'gagal',
              'message' => $validator->messages()
            ]);
          }

          $token = $request->token;

          $tokenDb = M_Pengguna::where('token',$token)->count();
          if($tokenDb > 0){
            $key = env('APP_KEY');
          $decoded = JWT::decode($token, $key, array('HS256'));
				 $decoded_array = (array) $decoded;
            if($decoded_array['extime'] > time()){
              if(M_Pengguna::create([
                'nama' => $request->nama,
                'email' => $request->email,
                'password' => encrypt($request->password)
              ])){
                return response()->json([
                  'status' => 'berhasil',
                    'message' => 'Data berhasil disimpan'
                  ]);

              }else{
                return response()->json([
                  'status' => 'gagal',
                    'message' => 'Data gagal disimpan'
                  ]);
              }           
            }else{
              return response()->json([
                'status' => 'gagal',
                  'message' => 'Token kadaluarsa'
                ]);

            }
          }


   
    }

    public function listUser(Request $request){
      $validator = Validator::make($request->all(), [
          'token' => 'required'
        ]);

       
        if ($validator->fails()) {
          return response()->json([
            'status' => 'gagal',
            'message' => $validator->messages()
          ]);
        }

        $token = $request->token;

        $tokenDb = M_Pengguna::where('token',$token)->count();
        if($tokenDb > 0){
          $key = env('APP_KEY');
          $decoded = JWT::decode($token, $key, array('HS256'));
				 $decoded_array = (array) $decoded;
          if($decoded_array['extime'] > time()){
            $data = array();
          $pengguna = M_Pengguna::get();
          foreach($pengguna as $p){
            $data[] = array(
              'nama' => $p->nama,
              'email' => $p->email,
              'id_user' => $p->id_user
            );

          }
          return response()->json([
            'status' => 'berhasil',
            'message' => 'Data berhasil diambil',
            'data' => $data
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

    public function hapusUser(Request $request){
      $validator = Validator::make($request->all(), [
          'id_user' => 'required',
          'token' => 'required'
        ]);

       
        if ($validator->fails()) {
          return response()->json([
            'status' => 'gagal',
            'message' => $validator->messages()
          ]);
        }

        $token = $request->token;

        $tokenDb = M_Pengguna::where('token',$token)->count();
        if($tokenDb > 0){
          $key = env('APP_KEY');
          $decoded = JWT::decode($token, $key, array('HS256'));
				 $decoded_array = (array) $decoded;
          if($decoded_array['extime'] > time()){
            if(M_Pengguna::where('id_user',$request->id_user )->delete()){
              return response()->json([
                'status' => 'berhasil',
                  'message' => 'Data berhasil dihapus'
                ]);

            }else{
              return response()->json([
                'status' => 'gagal',
                  'message' => 'Data gagal dihapus'
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

    public function loginUser(Request $request){
        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'password' => 'required',
          ]);

         
          if ($validator->fails()) {
            return response()->json([
              'status' => 'gagal',
              'message' => $validator->messages()
            ]);
          }

          $cek = M_Pengguna::where('email',$request->email)->count();
          $users = M_Pengguna::where('email',$request->email)->get();

          if($cek > 0){
            foreach ($users as $user) {
              if($request->password == decrypt($user->password)){
                $key = env('APP_KEY');
                $data = array(
                  "id_user" => $user->id_user,
                  "extime" => time()+(60*120)
                );
                $jwt = JWT::encode($data, $key);
                M_pengguna::where('id_user',$user->id_user)->update([
                  'token' => $jwt,    
                ]);
                Session::put('token',$jwt);
                return response()->json([
                  'status' => 'berhasil',
                  'message' => 'kamu berhasil login',
                  'token' => $jwt
                ]);
      
              }else{
      
               return response()->json([
                'status' => 'gagal',
                'message' => 'kamu gagal login',
                'token' => "-"
              ]);
             }
           }
         }else{
          return response()->json([
            'status' => 'gagal',
            'message' => 'kamu gagal login',
            'token' => "-"
          ]);
        }
      

   
    }
}
