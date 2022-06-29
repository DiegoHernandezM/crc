<?php

namespace App\Http\Controllers;

use Symfony\Component\HttpFoundation\Response;
use App\Http\Requests\UserDeleteRequest;
use App\Http\Requests\UserStoreRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Http\Resources\UserCollection;
use App\Http\Resources\UserResource;
use App\Models\Area;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;

class UsersController extends Controller
{
    public function index()
    {
        $this->authorize('Usuarios.Ver Lista');
        return Inertia::render('Users/Index', [
            'filters' => Request::all('search', 'role', 'trashed'),
            'users' => new UserCollection(
                Auth::user()->account->users()
                    ->orderByName()
                    ->where('id', '!=', 1)
                    ->filter(Request::only('search', 'role', 'trashed'))
                    ->paginate()
                    ->appends(Request::all())
            ),
        ]);
    }

    public function create()
    {
        $this->authorize('Usuarios.Crear');
        $permissions = Permission::where('name', 'not like', 'Catalogos.%')->get();
        foreach ($permissions as $key => $per) {
            $permissions[$key]->enabled = false;
        }

        return Inertia::render('Users/Create', [
            'areas' => Area::all(),
            'permissions' => $permissions,
        ]);
    }

    public function store(UserStoreRequest $request)
    {
        $this->authorize('Usuarios.Crear');
        $requestValidated = $request->validated();
        $permissions = $requestValidated["permissions"];

        unset($requestValidated["permissions"]);

        $user = Auth::user()->account->users()->create(
            $requestValidated
        );

        foreach ($permissions as $key => $perm) {
            if ($perm["enabled"] === true) {
                $user->givePermissionTo($perm["name"]);
            }
        }
        return Redirect::route('users')->with('success', 'Usuario creado.');
    }

    public function edit(User $user)
    {
        if ($user->id !== 1) {
            if (Auth::id() !== $user->id) {
                $this->authorize('Usuarios.Actualizar');
            }
            $permissions = [];
            $id = Auth::id();
            if ($id === 1) {
                $permissions = Permission::where('name', 'not like', 'Catalogos.%')->get();
                foreach ($permissions as $key => $per) {
                    if ($user->can($per->name)) {
                        $permissions[$key]->enabled = true;
                    } else {
                        $permissions[$key]->enabled = false;
                    }
                }
            }
            return Inertia::render('Users/Edit', [
                'user' => new UserResource($user),
                'areas' => Area::all(),
                'permissions' => $permissions
            ]);
        } else {
            $this->authorize('everything');
            $permissions = Permission::where('name', 'not like', 'Catalogos.%')->get();
            foreach ($permissions as $key => $per) {
                if ($user->can($per->name)) {
                    $permissions[$key]->enabled = true;
                } else {
                    $permissions[$key]->enabled = false;
                }
            }
            return Inertia::render('Users/Edit', [
                'user' => new UserResource($user),
                'areas' => Area::all(),
                'permissions' => $permissions
            ]);
        }
    }

    public function update(User $user, UserUpdateRequest $request)
    {
        if (Auth::id() !== $user->id) {
            $this->authorize('Usuarios.Actualizar');
        }

        $requestValidated = $request->validated();
        $permissions = $requestValidated["permissions"];

        unset($requestValidated["permissions"]);
        if (!$user->can('everything')) {
            foreach ($permissions as $key => $per) {
                if ($user->can($per["name"]) && $per["enabled"] === false) {
                    $user->revokePermissionTo($per["name"]);
                } elseif (!$user->can($per["name"]) && $per["enabled"] === true) {
                    $user->givePermissionTo($per["name"]);
                }
            }
        }

        $user->update(
            $requestValidated
        );

        return Redirect::back()->with('success', 'Usuario actualizado.');
    }

    public function destroy(User $user, UserDeleteRequest $request)
    {
        $this->authorize('Usuarios.Borrar');
        $user->delete();

        return Redirect::back()->with('success', 'Usuario borrado.');
    }

    public function restore(User $user)
    {
        $this->authorize('Usuarios.Actualizar');
        $user->restore();

        return Redirect::back()->with('success', 'Usuario recuperado.');
    }

    public function validatePassword($pasword)
    {
        if (Hash::check($pasword, Auth::user()->password)) {
            $message = 'ok';
        } else {
            $message= 'contraseña incorrecta';
        }
        return response()->json(['message' => $message]);
    }
}
