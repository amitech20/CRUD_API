<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Resources\UserResource;
use App\Http\Requests\UserRequest;
use Illuminate\Support\Facades\Auth; 
use Validator;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */


    public function login(Request $request)
    {
        
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:5',
        ]);
        if ($validator->fails())
        {
            return response(['errors'=>$validator->errors()->all()], 422);
        }

            $user = User::where('email', $request->email)->first();
        if ($user) 
            {
            if ($request->input('password') == $user->password) 
                {
                    $data = new UserResource($user);
                        $session = $user->name;
                            session_id($session);

                $response = ['session' => session_id($session)];
                    session_start();
            return response([$data,$response, 200]);
                } 
            else 
                {
                    $response = ["message" => "Password mismatch"];
                    return response($response, 422);
                }
            } 
        else 
            {
                $response = ["message" =>'User does not exist'];
                return response($response, 422);
            }
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:5|',
            'password repeat' => 'required|string|min:5',
            
        ]);
        
            if ($validator->fails())
                {
                    return response(['errors'=>$validator->errors()->all()], 422);
                }
        
                if ($request->input('password') == $request->input('password repeat')) {
                    $data = User::create([
                    "id" => $request->input('id'),
                    "name" => $request->input('name'),
                    "email" => $request->input('email'),
                    "password" => $request->input('password')]);

                        return new UserResource($data);
                }
                else {
                    
                        $response = ['error' => 'The password repeat confirmation is not the same with password'];
                        return response($response, 422);
                }
                    
            
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getUsers()
    {
        return UserResource::collection(User::all());
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getUser($id)
    {
        $data = User::findOrFail($id);
        return new UserResource($data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        
            $user = User::findOrFail($id);
            $user->name = $request->input('name');
            $user->email = $request->input('email');
            $user->password = $request->input('password');
           if ($user->save()) {
                return new UserResource($user);
           }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
        //  
        if (User::where('id', $id)->first()) 
        {
            $data = User::where('id', $id)->first();
            $data->delete();
            $response = new UserResource($data);
            return response($response,200);
        }
        else{
            $response = ["message" => "No record for ID $id"];
            return response($response, 422);
        }
    }
}
