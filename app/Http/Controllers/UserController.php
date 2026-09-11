<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\Region;
use App\Models\Role;
use App\Models\Sala;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Inertia\Response;

class UserController extends Controller
{
    /**
     * Listado general con paginación, filtros institucionales y control territorial.
     */
    public function index(Request $request): Response
    {
        Gate::authorize('viewAny', User::class);

        $currentUser = $request->user();

        $usuarios = User::query()
            ->with(['roles', 'region', 'sala', 'area'])
            // Restricción territorial por rol institucional
            ->when($currentUser->hasRole('MAGISTRADO_PRESIDENTE'), function (Builder $query) use ($currentUser) {
                $query->where('sala_id', $currentUser->sala_id);
            })
            ->when($currentUser->hasRole('MAGISTRADO_PONENTE'), function (Builder $query) use ($currentUser) {
                $query->where('area_id', $currentUser->area_id);
            })
            ->when($currentUser->hasRole('MAGISTRADO_VISITADOR'), function (Builder $query) use ($currentUser) {
                $salasAsignadas = $currentUser->salasVisitadas()->pluck('salas.id');
                $query->whereIn('sala_id', $salasAsignadas);
            })
            // Filtro por término de búsqueda (nombre o correo)
            ->when($request->filled('buscar'), function (Builder $query) use ($request) {
                $termino = trim($request->string('buscar'));
                $query->where(function (Builder $subQuery) use ($termino) {
                    $subQuery->where('name', 'like', "%{$termino}%")
                        ->orWhere('email', 'like', "%{$termino}%");
                });
            })
            // Filtros selectivos de estructura
            ->when($request->filled('region_id'), fn (Builder $q) => $q->where('region_id', $request->integer('region_id')))
            ->when($request->filled('sala_id'), fn (Builder $q) => $q->where('sala_id', $request->integer('sala_id')))
            ->when($request->filled('area_id'), fn (Builder $q) => $q->where('area_id', $request->integer('area_id')))
            ->when($request->filled('role_id'), function (Builder $q) use ($request) {
                $q->whereHas('roles', fn (Builder $sub) => $sub->where('roles.id', $request->integer('role_id')));
            })
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Usuarios/Index', [
            'usuarios' => $usuarios,
            'filtros' => $request->only(['buscar', 'region_id', 'sala_id', 'area_id', 'role_id']),
            'regiones' => fn () => Region::where('activo', true)->select('id', 'clave', 'nombre')->orderBy('nombre')->get(),
            'roles' => fn () => Role::where('activo', true)->select('id', 'clave', 'nombre')->orderBy('id')->get(),
        ]);
    }

    /**
     * Registro y asignación territorial del servidor público.
     */
    public function store(StoreUserRequest $request): RedirectResponse
    {
        DB::transaction(function () use ($request) {
            $user = User::create([
                'name' => $request->validated('name'),
                'email' => $request->validated('email'),
                'password' => Hash::make($request->validated('password')),
                'region_id' => $request->validated('region_id'),
                'sala_id' => $request->validated('sala_id'),
                'area_id' => $request->validated('area_id'),
                'activo' => $request->boolean('activo', true),
            ]);

            $user->roles()->sync($request->validated('roles'));
        });

        return to_route('usuarios.index')->with('success', 'Servidor público registrado exitosamente.');
    }

    /**
     * Actualizar datos del servidor público o cambio de contraseña según el rol.
     */
    public function update(UpdateUserRequest $request, User $usuario): RedirectResponse
    {
        // Si es Magistrado: solo actualiza la contraseña de su subordinado
        if (! $request->user()->hasRole('ADMIN_DGTIC')) {
            $usuario->update([
                'password' => Hash::make($request->validated('password')),
            ]);

            return to_route('usuarios.index')->with('success', 'Contraseña actualizada correctamente.');
        }

        // Si es ADMIN_DGTIC: actualiza expediente completo, roles y adscripción
        DB::transaction(function () use ($request, $usuario) {
            $datos = [
                'name' => $request->validated('name'),
                'email' => $request->validated('email'),
                'region_id' => $request->validated('region_id'),
                'sala_id' => $request->validated('sala_id'),
                'area_id' => $request->validated('area_id'),
                'activo' => $request->boolean('activo', $usuario->activo),
            ];

            if ($request->filled('password')) {
                $datos['password'] = Hash::make($request->validated('password'));
            }

            $usuario->update($datos);
            $usuario->roles()->sync($request->validated('roles'));
        });

        return to_route('usuarios.index')->with('success', 'Expediente institucional actualizado correctamente.');
    }

    /**
     * Baja lógica institucional del personal en el sistema.
     */
    public function destroy(User $usuario): RedirectResponse
    {
        Gate::authorize('delete', $usuario);

        $usuario->update(['activo' => false]);

        return to_route('usuarios.index')->with('success', 'Servidor público desactivado.');
    }

    /*
    |--------------------------------------------------------------------------
    | Endpoints para selectores en cascada (Wayfinder)
    |--------------------------------------------------------------------------
    */

    /**
     * Catálogo dinámico de salas filtradas por circunscripción regional.
     */
    public function salasPorRegion(Region $region): JsonResponse
    {
        return response()->json(
            $region->salas()
                ->where('activo', true)
                ->select(['id', 'clave', 'nombre', 'tipo'])
                ->orderBy('nombre')
                ->get()
        );
    }

    /**
     * Catálogo dinámico de ponencias/áreas filtradas por sala.
     */
    public function areasPorSala(Sala $sala): JsonResponse
    {
        return response()->json(
            $sala->areas()
                ->where('activo', true)
                ->select(['id', 'clave', 'nombre', 'tipo', 'numero'])
                ->orderBy('tipo')
                ->orderBy('numero')
                ->get()
        );
    }
}
