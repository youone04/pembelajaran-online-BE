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

class Peserta extends Controller
{
    //
    public function Regis(Request $request){
        $validator = Validator::make($request->all(), [
            'nama' => 'required',
            'email' => 'required|unique:tbl_user',
            'password' => 'required |confirmed',
            'password_confirmation' => 'required'
          ]);

         
          if ($validator->fails()) {
            return response()->json([
              'status' => 'gagal',
              'message' => $validator->messages()
            ]);
          }


          if(M_Peserta::create([
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


   
    }

    public function login(Request $request){
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

          $cek = M_Peserta::where('email',$request->email)->count();
          $users = M_Peserta::where('email',$request->email)->get();

          if($cek > 0){
            foreach ($users as $user) {
              if($request->password == decrypt($user->password)){
                $key = env('APP_KEY');
                $data = array(
                  "id_peserta" => $user->id_peserta,
                  "extime" => time()+((60*120)*60)
                );
                $jwt = JWT::encode($data, $key);
                M_Peserta::where('id_peserta',$user->id_peserta)->update([
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
