<?php

namespace App\Http\Controllers;

use App\Models\Persona;
use App\Models\Role;
use App\Models\Titularidad;
use App\Models\UnidadOrganizacional;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;
use Inertia\Response;

class UserController extends Controller
{
    public function index(Request $request): Response
    {
        Gate::authorize('viewAny', User::class);

        $currentUser = $request->user();
        $unidadPropia = $currentUser->titularidadActiva?->unidad;

        $query = User::query()
            ->with([
                'persona:id,curp,rfc',
                'roles:id,clave,nombre',
                'titularidadActiva.unidad.tipo:id,clave,nombre',
            ]);

        // Restricción territorial por jerarquía de grafo
        if (! $currentUser->hasRole('ADMIN_DGTIC') && $unidadPropia) {
            $query->whereHas('titularidades', function (Builder $t) use ($unidadPropia) {
                $t->where('activo', true)
                  ->whereHas('unidad', fn (Builder $u) => $u->where('path', 'like', "{$unidadPropia->path}%"));
            });
        }

        // Filtros
        $usuarios = $query
            ->when($request->filled('buscar'), function (Builder $q) use ($request) {
                $termino = trim($request->string('buscar'));
                $q->where(function (Builder $sub) use ($termino) {
                    $sub->where('name', 'like', "%{$termino}%")
                        ->orWhere('email', 'like', "%{$termino}%")
                        ->orWhereHas('persona', fn ($p) => $p->where('curp', 'like', "%{$termino}%"));
                });
            })
            ->when($request->filled('unidad_id'), function (Builder $q) use ($request) {
                $unidadId = $request->integer('unidad_id');
                $q->whereHas('titularidades', function (Builder $t) use ($unidadId) {
                    $t->where('activo', true)
                      ->whereHas('unidad', fn ($u) => $u->where('path', 'like', "%/{$unidadId}/%")->orWhere('id', $unidadId));
                });
            })
            ->when($request->filled('role_id'), function (Builder $q) use ($request) {
                $q->whereHas('roles', fn ($r) => $r->where('roles.id', $request->integer('role_id')));
            })
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Usuarios/Index', [
            'usuarios' => $usuarios,
            'filtros' => $request->only(['buscar', 'unidad_id', 'role_id']),
            'roles_disponibles' => fn () => Role::where('activo', true)->get(['id', 'clave', 'nombre']),
            'unidades_arbol' => fn () => $this->obtenerArbolUnidades(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('create', User::class);

        $validated = $request->validate([
            'name'                     => ['required', 'string', 'max:150'],
            'email'                    => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password'                 => ['required', 'string', Password::default()],
            'activo'                   => ['boolean'],
            'curp'                     => ['nullable', 'string', 'size:18'],
            'rfc'                      => ['nullable', 'string', 'min:10', 'max:13'],
            'roles'                    => ['required', 'array', 'min:1'],
            'roles.*'                  => ['integer', 'exists:roles,id'],
            'unidad_organizacional_id' => ['required', 'integer', 'exists:unidades_organizacionales,id'],
            'tipo_titularidad'         => ['required', 'string', Rule::in(['TITULAR', 'ENCARGADO_DESPACHO', 'SUPLENTE'])],
        ]);

        DB::transaction(function () use ($validated) {
            $personaId = null;
            if (! empty($validated['curp'])) {
                $persona = Persona::firstOrCreate(
                    ['curp' => strtoupper(trim($validated['curp']))],
                    [
                        'rfc'             => strtoupper(trim($validated['rfc'] ?? '')),
                        'nombre'          => $validated['name'],
                        'primer_apellido' => '',
                    ]
                );
                $personaId = $persona->id;
            }

            $user = User::create([
                'name'       => $validated['name'],
                'email'      => $validated['email'],
                'password'   => Hash::make($validated['password']),
                'persona_id' => $personaId,
                'activo'     => $validated['activo'] ?? true,
            ]);

            $user->roles()->sync($validated['roles']);

            Titularidad::create([
                'unidad_organizacional_id' => $validated['unidad_organizacional_id'],
                'user_id'                  => $user->id,
                'tipo'                     => $validated['tipo_titularidad'],
                'fecha_inicio'             => now()->toDateString(),
                'activo'                   => true,
            ]);
        });

        return to_route('usuarios.index')->with('success', 'Servidor público registrado correctamente.');
    }

    public function update(Request $request, User $usuario): RedirectResponse
    {
        Gate::authorize('update', $usuario);

        // Si no es admin DGTIC, solo puede actualizar contraseña
        if (! $request->user()->hasRole('ADMIN_DGTIC')) {
            $validated = $request->validate([
                'password' => ['required', 'string', Password::default()],
            ]);

            $usuario->update(['password' => Hash::make($validated['password'])]);

            return to_route('usuarios.index')->with('success', 'Contraseña actualizada correctamente.');
        }

        $validated = $request->validate([
            'name'                     => ['required', 'string', 'max:150'],
            'email'                    => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($usuario->id)],
            'password'                 => ['nullable', 'string', Password::default()],
            'activo'                   => ['boolean'],
            'roles'                    => ['required', 'array', 'min:1'],
            'roles.*'                  => ['integer', 'exists:roles,id'],
            'unidad_organizacional_id' => ['required', 'integer', 'exists:unidades_organizacionales,id'],
            'tipo_titularidad'         => ['required', 'string', Rule::in(['TITULAR', 'ENCARGADO_DESPACHO', 'SUPLENTE'])],
        ]);

        DB::transaction(function () use ($usuario, $validated) {
            $datosUser = [
                'name'   => $validated['name'],
                'email'  => $validated['email'],
                'activo' => $validated['activo'] ?? $usuario->activo,
            ];

            if (! empty($validated['password'])) {
                $datosUser['password'] = Hash::make($validated['password']);
            }

            $usuario->update($datosUser);
            $usuario->roles()->sync($validated['roles']);

            // Gestión de cambio de titularidad en el grafo
            $titularidadActiva = $usuario->titularidadActiva;
            $cambioUnidad = ! $titularidadActiva || $titularidadActiva->unidad_organizacional_id !== $validated['unidad_organizacional_id'];
            $cambioTipo = $titularidadActiva && $titularidadActiva->tipo !== $validated['tipo_titularidad'];

            if ($cambioUnidad || $cambioTipo) {
                if ($titularidadActiva) {
                    $titularidadActiva->update([
                        'activo'    => false,
                        'fecha_fin' => now()->toDateString(),
                    ]);
                }

                Titularidad::create([
                    'unidad_organizacional_id' => $validated['unidad_organizacional_id'],
                    'user_id'                  => $usuario->id,
                    'tipo'                     => $validated['tipo_titularidad'],
                    'fecha_inicio'             => now()->toDateString(),
                    'activo'                   => true,
                ]);
            }
        });

        return to_route('usuarios.index')->with('success', 'Expediente de usuario actualizado.');
    }

    public function destroy(User $usuario): RedirectResponse
    {
        Gate::authorize('delete', $usuario);

        DB::transaction(function () use ($usuario) {
            $usuario->update(['activo' => false]);
            $usuario->titularidades()->where('activo', true)->update([
                'activo'    => false,
                'fecha_fin' => now()->toDateString(),
            ]);
        });

        return to_route('usuarios.index')->with('success', 'Usuario desactivado correctamente.');
    }

    /**
     * Construye el árbol jerárquico para el selector del frontend.
     */
    protected function obtenerArbolUnidades(): array
    {
        $unidades = UnidadOrganizacional::with('tipo:id,clave,nombre')
            ->where('activo', true)
            ->orderBy('nombre')
            ->get();

        return $this->armarRamas($unidades, null);
    }

    protected function armarRamas($unidades, ?int $padreId): array
    {
        $rama = [];
        foreach ($unidades->where('padre_id', $padreId) as $unidad) {
            $hijos = $this->armarRamas($unidades, $unidad->id);
            $item = [
                'id'     => $unidad->id,
                'clave'  => $unidad->clave,
                'nombre' => $unidad->nombre,
                'tipo'   => $unidad->tipo?->clave,
            ];
            if (! empty($hijos)) {
                $item['hijos'] = $hijos;
            }
            $rama[] = $item;
        }
        return $rama;
    }
}
