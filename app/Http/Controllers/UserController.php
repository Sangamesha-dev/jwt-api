<?php
namespace App\Http\Controllers;

use App\Jobs\CreateBulkUser;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class UserController extends Controller
{
    public function authenticate(Request $request)
    {
        $credentials = $request->only('email', 'password');

        try {
            if (! $token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'invalid_credentials'], 400);
            }
        } catch (JWTException $e) {
            return response()->json(['error' => 'could_not_create_token'], 500);
        }

        return response()->json(compact('token'));
    }

    public function register(Request $request)
    {
            $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if($validator->fails()){
                return response()->json($validator->errors()->toJson(), 400);
        }

        $user = User::create([
            'name' => $request->get('name'),
            'email' => $request->get('email'),
            'password' => Hash::make($request->get('password')),
        ]);

        $token = JWTAuth::fromUser($user);

        return response()->json(compact('user','token'),201);
    }

    public function getAuthenticatedUser()
        {
            try {

                    if (! $user = JWTAuth::parseToken()->authenticate()) {
                            return response()->json(['user_not_found'], 404);
                    }

            } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {

                    return response()->json(['token_expired'], $e->getStatusCode());

            } catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {

                    return response()->json(['token_invalid'], $e->getStatusCode());

            } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {

                    return response()->json(['token_absent'], $e->getStatusCode());

            }

            return response()->json(compact('user'));
        }

    public function getAllUsers()
    {
        try {

                if (! $user = JWTAuth::parseToken()->authenticate()) {
                        return response()->json(['user_not_found'], 404);
                }

        } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {

                return response()->json(['token_expired'], $e->getStatusCode());

        } catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {

                return response()->json(['token_invalid'], $e->getStatusCode());

        } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {

                return response()->json(['token_absent'], $e->getStatusCode());

        }
        $users = User::all();
        return response()->json(compact('users'));
    }

    public function getUser($id)
    {
        try {

                if (! $user = JWTAuth::parseToken()->authenticate()) {
                        return response()->json(['user_not_found'], 404);
                }

        } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {

                return response()->json(['token_expired'], $e->getStatusCode());

        } catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {

                return response()->json(['token_invalid'], $e->getStatusCode());

        } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {

                return response()->json(['token_absent'], $e->getStatusCode());

        }
        $users = User::find($id);
        if($users){
            return response()->json(compact('users'));
        }else{
            return response()->json(['User not available']);
        }

    }

    public function updateAuthenticatedUser($id)
    {
            try {

                    if (! $user = JWTAuth::parseToken()->authenticate()) {
                            return response()->json(['user_not_found'], 404);
                    }

            } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {

                    return response()->json(['token_expired'], $e->getStatusCode());

            } catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {

                    return response()->json(['token_invalid'], $e->getStatusCode());

            } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {

                    return response()->json(['token_absent'], $e->getStatusCode());

            }

            return response()->json(compact('user'));
    }

    public function updateUser($id, Request $request)
    {
        try {

            if (! $user = JWTAuth::parseToken()->authenticate()) {
                    return response()->json(['user_not_found'], 404);
            }

        } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {

            return response()->json(['token_expired'], $e->getStatusCode());

        } catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {

            return response()->json(['token_invalid'], $e->getStatusCode());

        } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {

            return response()->json(['token_absent'], $e->getStatusCode());
        }
        $validator = Validator::make($request->all(), [
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|string|email|max:255|unique:users',
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }

        $user = User::find($id);
        if($request['name']){
            $user['name'] = $request->get('name');
        }
        if($request['email']){
            $user['email'] = $request->get('email');
        }
        if($request['password']){
            $user['password'] = Hash::make($request->get('password'));
        }
        $user->save();


        return response()->json(['User information has been updated']);
    }

    public function deleteUser($id)
    {
        try {

            if (! $user = JWTAuth::parseToken()->authenticate()) {
                    return response()->json(['user_not_found'], 404);
            }

        } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {

            return response()->json(['token_expired'], $e->getStatusCode());

        } catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {

            return response()->json(['token_invalid'], $e->getStatusCode());

        } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {

            return response()->json(['token_absent'], $e->getStatusCode());
        }


        $user = User::find($id);
        if($user){
            $user->delete();
            return response()->json(['User information has been deleted.']);
        }else{
            return response()->json(['User information is not present to delete.']);
        }

    }

    public function bulkUpload(Request $request)
    {
        try {

            if (! $user = JWTAuth::parseToken()->authenticate()) {
                    return response()->json(['user_not_found'], 404);
            }

        } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {

            return response()->json(['token_expired'], $e->getStatusCode());

        } catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {

            return response()->json(['token_invalid'], $e->getStatusCode());

        } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {

            return response()->json(['token_absent'], $e->getStatusCode());
        }

        $res = '';
        echo 'executing';
        if ($request->hasFile('csvFile'))
        {
            if ($request->file('csvFile')->isValid()) {
                // $extension = $request->file('csvFile')->extension();
                $extension = $request->file('csvFile')->getClientOriginalExtension();
                if($extension != 'csv')
                {
                    $res = 'Uploaded file is not CSV - file type = '.$extension.' & path ='.$request->file('csvFile');
                }else
                {
                    // $res = 'Bulk upload started';
                    // $path = $request->csvFile->store('public');
                    // $path = $request->file('csvFile')->move('/csv');
                    $file = $request->file('csvFile');
                    $path = $file->storeAs('csv', \Str::random(20) . '.' . $extension);
                    $res .= '- file path = '.$path;
                    CreateBulkUser::dispatch($path);
                }
            }else{
                $res = 'Invalid File';
            }
        }else
        {
            $res = 'There noe no files to upload';
        }

        return response()->json([$res]);
    }
}
