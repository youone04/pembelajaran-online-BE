<?php
Route::post('/tambahUser','Pengguna@tambahUser');
Route::post('/loginUser','Pengguna@loginUser');
Route::post('/listUser','Pengguna@listUser');
Route::post('/hapusUser','Pengguna@hapusUser');

Route::post('/tambahKonten','Konten@tambahKonten');
Route::post('/ubahKonten','Konten@ubahKonten');
Route::post('/hapusKonten','Konten@hapusKonten');
Route::post('/listKonten','Konten@listKonten');
Route::post('/listKontenPublic','Konten@listKontenPublic');
Route::post('/cariKontenPublic','Konten@cariKontenPublic');

Route::post('/listSoal','Ujian@listSoal');
Route::post('/jawab','Ujian@jawab');
Route::post('/hitungSkor','Ujian@hitungSkor');
Route::post('/selesaiUjian','Ujian@selesaiUjian');

Route::post('/regis','Peserta@Regis');
Route::post('/login','Peserta@login');