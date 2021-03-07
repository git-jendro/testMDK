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
            return response()->json($validator->errors(),400);
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
    *          response=400, 
    *          description="Bad Request",
    *          @OA\JsonContent(
    *             @OA\Property(property="message", type="string", example="Sorry, wrong email address or password. Please try again")
    *                )
    *           )
    *      ),
    *      @OA\Response(
    *          response=401, 
    *          description="Unauthorized"
    *      ),
    * )
    */
    public function login(Request $request)
    {
        $rules = [
            'email'                 => 'email',
            'password'              => 'alpha_num',
        ];

        $messages = [
            'email.email'           => 'Format email salah',
            'password.alpha_num'       => 'Password harus berupa huruf dan angka'
        ];
 
        $validator = Validator::make($request->all(), $rules, $messages);
 
        if($validator->fails()){
            return response()->json($validator->errors(),400);
        }

        $credential = $request->only('email', 'password');
        if (! $token = auth()->attempt($credential)) {
            return response()->json(["message" => "Email atau Password salah !"], 401);
        }

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

    public function tags(Request $request)
    {
        $user = User::select('id','nama')->where('nama', 'like', '%'.$request->tags.'%')->get();
        return response()->json($user,200);
    }
}
