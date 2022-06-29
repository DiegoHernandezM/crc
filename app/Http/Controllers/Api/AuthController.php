<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiResponses;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    protected $mUser;

    public function __construct()
    {
        $this->mUser = new User();
    }

    public function login(Request $request)
    {
        try {
            $user = $this->mUser->where('email', $request->username)->first();
            if ($user) {
                if (Hash::check($request->password, $user->password)) {
                    $token = $user->createToken('Laravel Password Grant Client')->accessToken;
                    $data = [
                        'access_token' => $token,
                        'accessToken' => $token,
                        'user' => [
                            'displayName' => $user->name,
                            'permissions' => $user->permissions,
                            'roles' => $user->roles
                        ]
                    ];
                    return ApiResponses::okObject($data);
                } else {
                    return ApiResponses::badRequest("Contraseña incorrecta");
                }
            } else {
                return ApiResponses::notFound('El usuario no existe');
            }
        } catch (\Exception $e) {
            Log::error('Error en '.__METHOD__.' línea '.$e->getLine().':'.$e->getMessage());
            return ApiResponses::internalServerError($e->getMessage());
        }
    }

    public function verifyemail(Request $request)
    {
        $token = $request->token;
        if ($token) {
            $user = $this->mUser->where('device_key', $token)
                ->whereNull('email_verified_at')
                ->first();
        }
        if (!empty($user)) {
            $user->email_verified_at = new \DateTime();
            $user->save();
        } else {
            $response = 'Bad Request.';
            return response($response, 400);
        }
        $response = 'You have succesfully verified your email!';
        return response($response, 200);
    }

    public function logout(Request $request)
    {
        $token = $request->user()->token();
        $token->revoke();
        $response = 'You have been succesfully logged out!';
        return response($response, 200);
    }
}
