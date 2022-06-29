<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiResponses;
use App\Http\Controllers\Controller;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Log;

class UserController extends Controller
{
    public function show(UserRepository $rUser)
    {
        try {
            $user = $rUser->showUser();
            return ApiResponses::okObject($user);
        } catch (\Exception $e) {
            Log::error('Error en '.__METHOD__.' línea '.$e->getLine().':'.$e->getMessage());
            return ApiResponses::internalServerError($e->getMessage());
        }
    }

    public function all(UserRepository $rUser)
    {
        try {
            $users = $rUser->getAll();
            return ApiResponses::okObject($users);
        } catch (\Exception $e) {
            return ApiResponses::internalServerError($e->getMessage());
        }
    }

    public function find($id, UserRepository $rUser)
    {
        try {
            $user = $rUser->findUser($id);
            if ($user) {
                return ApiResponses::okObject($user);
            }
            return ApiResponses::notFound();
        } catch (\Exception $e) {
            return ApiResponses::internalServerError($e->getMessage());
        }
    }

    public function create(UserRequest $request, UserRepository $rUser)
    {
        try {
            $password = rand(100, 999).str_pad(Str::random(8), 3, STR_PAD_LEFT);
            request()->merge([ 'password' => $password]);
            $user = $rUser->createUser($request->all());
            return ApiResponses::okObject($user);
        } catch (\Exception $e) {
            return ApiResponses::internalServerError($e->getMessage());
        }
    }

    public function update($id, Request $request, UserRepository $rUser)
    {
        try {
            $user = $rUser->updateUser($id, $request);
            return ApiResponses::okObject($user);
        } catch (\Exception $e) {
            return ApiResponses::internalServerError($e->getMessage());
        }
    }

    public function destroy($id, UserRepository $rUser)
    {
        try {
            $rUser->destroyUser($id);
            return ApiResponses::ok('Recurso eliminado');
        } catch (\Exception $e) {
            return ApiResponses::internalServerError($e->getMessage());
        }
    }

    public function restore($id, UserRepository $rUser)
    {
        try {
            $rUser->restoreUser($id);
            return ApiResponses::ok('Recurso recuperado');
        } catch (\Exception $e) {
            return ApiResponses::internalServerError($e->getMessage());
        }
    }

    public function getByPermission($permission, UserRepository $rUser)
    {
        try {
            $users = $rUser->getUsersByPermission($permission);
            return ApiResponses::okObject($users);
        } catch (\Exception $e) {
            return ApiResponses::internalServerError($e->getMessage());
        }
    }

    public function changePassword (Request $request, UserRepository $rUser)
    {
        try {
            $user = $rUser->changePasswordUser($request);
            if($user->getStatusCode() === 200) {
                return ApiResponses::okObject($user);
            } else {
                $errors = $user->getOriginalContent();
                return ApiResponses::badRequestValidations('', $errors['message']);
            }
        } catch (\Exception $e) {
            return ApiResponses::internalServerError($e->getMessage());
        }
    }
}
