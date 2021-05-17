<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});
// $filePath = storage_path('app\/csv\/EY90nyEbJMh1F5rEqtvs.csv');
// $fileData = array_map('str_getcsv',file($filePath));
//     foreach ($fileData as $key => $data)
//     {
//         dd($data);
//         Log::info("Log from dispatcher each row = ".$data);
//         if($key == 0){
//             $headers = $data;
//         }
//         $user = User::create([
//             'name' => $data[0],
//             'email' => $data[1],
//             'password' => Hash::make($data[2]),
//         ]);
// }
