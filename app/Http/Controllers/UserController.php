<?php

namespace App\Http\Controllers;

use App\User;
use Exception;
use Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
    * @OA\Post(
    *     path="/regist",
    *     tags={"user"},
    *     summary="Register Form",
    *     operationId="regist",
    *     @OA\Parameter(
    *          name="nama",
    *          required=true,
    *          in="query",
    *          @OA\Schema(
    *              type="string"
    *          )
    *     ),
    *     @OA\Parameter(
    *          name="email",
    *          required=true,
    *          in="query",
    *          @OA\Schema(
    *              type="string"
    *          )
    *     ),
    *     @OA\Parameter(
    *          name="password",
    *          required=true,
    *          in="query",
    *          @OA\Schema(
    *              type="string"
    *          )
    *     ),
    *     @OA\Response(
    *          response=200,
    *          description="Successful",
    *       ),
    *      @OA\Response(
    *          response=400, 
    *          description="Bad Request"
    *      ),
    * )
    */
    public function regist(Request $request)
    {
        $rules = [
            'nama'              => 'regex:/^[\pL\s\-]+$/u',
            'email'                 => 'email',
            'password'              => 'alpha_num',
        ];

        $messages = [
            'nama.regex'           => 'Nama harus huruf',
            'email.email'           => 'Format email salah',
            'password.alpha_num'       => 'Password harus berupa huruf dan angka'
        ];
 
        $validator = Validator::make($request->all(), $rules, $messages);
 
        if($validator->fails()){
            // return redirect()->back()->withErrors($validator)->withInput($request->all);
            return response()->json($validator,400);
        }
        
        $user = new User;
        $user->nama = $request->nama;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->save();

        return  response()->json([
            "message" => "Success"
        ],200);
    }

    /**
    * @OA\Post(
    *     path="/login",
    *     tags={"user"},
    *     summary="Login Form",
    *     operationId="login",
    *     @OA\Parameter(
    *          name="email",
    *          required=true,
    *          in="query",
    *          @OA\Schema(
    *              type="string"
    *          )
    *     ),
    *     @OA\Parameter(
    *          name="password",
    *          required=true,
    *          in="query",
    *          @OA\Schema(
    *              type="string"
    *          )
    *     ),
    *     @OA\Response(
    *          response=200,
    *          description="Successful",
    *       ),
    *      @OA\Response(
    *          response=401, 
    *          description="Unauthorized"
    *      ),
    * )
    */
    public function login(Request $request)
    {
        $rules = [
            'email'                 => 'email|exist:users',
            'password'              => 'alpha_num',
        ];

        $messages = [
            'email.email'           => 'Format email salah',
            'email.exist'           => 'Email tidak tersedia',
            'password.alpha_num'       => 'Password harus berupa huruf dan angka'
        ];
        $credential = $request->only('email', 'password');
        $validator = Validator::make($credential, $rules, $messages);
        // try to log user in
        if (! $token = auth()->attempt($credential)) {
            return response()->json(["message" => $validator], 401);
        }

        // generate token
        return response()->json(["message" => "Login Success !"],200);
    }

    /**
    * @OA\Get(
    *     path="/list",
    *     tags={"user"},
    *     summary="List User Form",
    *     operationId="list",
    *     @OA\Response(
    *          response=200,
    *          description="Successful",
    *       ),
    * )
    */
    public function list()
    {
        $user = User::all();
        return response()->json($user,200);
    }
}
