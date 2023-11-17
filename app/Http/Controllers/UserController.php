<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserLoginRequest;
use App\Http\Requests\UserRegisterRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redis;
use Nette\Utils\Strings;

class UserController extends Controller
{
    public function register(UserRegisterRequest $req): JsonResponse
    {
        $data = $req->validated();
        // $data = [
        //     "username" => $req->username,
        //     "password" => $req->password,
        //     "name" => $req->name,
        // ];

        if (User::where('username', $data['username'])->count() == 1) {
            throw new HttpResponseException(response([
                "errors" => [
                    'username' => [
                        'username already registered'
                    ]
                ]
            ], 400));
        }

        $user = new User($data);
        $user->password = Hash::make($data['password']);


        $user->save();
        return (new UserResource($user))->response()->setStatusCode(201);
    }
    public function getAllUsers(): mixed
    {   
        $key = "users";
        $cache = Redis::get($key);

        if ($cache) {
            // Redis::del($key);
            return json_decode($cache);
        }
        $data = User::all(['id', "username", "name", "created_at", "updated_at"]);

        Redis::set($key, $data, "EX", 5);
        return [
            'message' => 'success',
            'data' => $data
        ];
    }

    public function get(Request $req): UserResource
    {
        $user = Auth::user();
        return new UserResource($user);
    }

    public function login(UserLoginRequest $req): JsonResponse
    {
        $data = $req->validated();

        $user = User::where('username', $data["username"])->first();
        if (!$user || !Hash::check($data["password"], $user["password"])) {
            throw new HttpResponseException(response([
                "errors" => [
                    'message' => [
                        'username or password wrong'
                    ],
                    "data" => $user
                ]
            ]), 400);
        }
        $user->token = Str::uuid()->toString();
        $user->save();

        return (new UserResource($user))->response();
    }

    public function updateUser(UserUpdateRequest $req): JsonResponse
    {
        $data = $req->validated();
        // return (new UserResource($data))->response();
        $user = Auth::user();

        if (isset($data["password"])) {
            $user->password = Hash::make($data["password"]);
        }
        if (isset($data["username"])) {
            $user->username = $data["username"];
        }
        if (isset($data["name"])) {
            $user->name = $data["name"];
        }

        $user->save();

        return (new UserResource($user))->response();
    }

    public function logout(Request $req): JsonResponse
    {
        $user = Auth::user();
        $user->token = null;
        $user->save();

        return response()->json([
            "data" => true
        ]);
    }
}
