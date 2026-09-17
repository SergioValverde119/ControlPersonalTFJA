<!-- eslint-disable import/order -->
<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import {
    Building2,
    ChevronDown,
    Edit3,
    MoreHorizontal,
    Plus,
    RotateCcw,
    Search,
    UserCheck,
    UserX,
} from '@lucide/vue';
import { computed, ref, watch } from 'vue';
import UnidadTreeSelect from '@/components/custom/UnidadTreeSelect.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardAction,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import {
    Collapsible,
    CollapsibleContent,
    CollapsibleTrigger,
} from '@/components/ui/collapsible';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { toUrl } from '@/lib/utils';
import usuariosRoutes from '@/routes/usuarios';
import type {
    UnidadOrganizacional,
    Usuario,
    UsuariosIndexProps,
} from '@/types/usuarios';
import DeleteUserDialog from './DeleteUserDialog.vue';
import UserDialog from './UserDialog.vue';

const props = defineProps<UsuariosIndexProps>();

const buscar = ref(props.filtros.buscar ?? '');
const roleId = ref(props.filtros.role_id ? String(props.filtros.role_id) : 'TODOS');
const unidadId = ref<number | null>(
    props.filtros.unidad_id ? Number(props.filtros.unidad_id) : null,
);

const mostrarFiltroArbol = ref(Boolean(props.filtros.unidad_id));

// Mantiene sincronizado el estado si la respuesta de Inertia cambia los filtros
watch(
    () => props.filtros,
    (nuevosFiltros) => {
        buscar.value = nuevosFiltros.buscar ?? '';
        roleId.value = nuevosFiltros.role_id ? String(nuevosFiltros.role_id) : 'TODOS';
        unidadId.value = nuevosFiltros.unidad_id ? Number(nuevosFiltros.unidad_id) : null;
        if (nuevosFiltros.unidad_id) {
            mostrarFiltroArbol.value = true;
        }
    },
);

const modalFormOpen = ref(false);
const modalDeactivateOpen = ref(false);
const usuarioSeleccionado = ref<Usuario | null>(null);
const isDeactivating = ref(false);

const mapaPadres = computed(() => {
    const mapa = new Map<number, string>();

    const recorrer = (nodos: UnidadOrganizacional[], nombrePadre: string | null = null) => {
        for (const nodo of nodos) {
            if (nombrePadre) {
                mapa.set(nodo.id, nombrePadre);
            }

            if (nodo.hijos?.length) {
                recorrer(nodo.hijos, nodo.nombre);
            }
        }
    };

    recorrer(props.unidades_arbol ?? []);

    return mapa;
});

const obtenerPadreInmediato = (id?: number | null) => {
    if (!id) {
        return null;
    }

    return mapaPadres.value.get(id) ?? null;
};

const formatearEtiqueta = (label: string) => {
    if (label.includes('&laquo;') || label.toLowerCase().includes('previous') || label.toLowerCase().includes('anterior')) {
        return '«';
    }

    if (label.includes('&raquo;') || label.toLowerCase().includes('next') || label.toLowerCase().includes('siguiente')) {
        return '»';
    }

    return label;
};

const aplicarFiltros = () => {
    const params: Record<string, string | number> = {};

    if (buscar.value.trim()) {
        params.buscar = buscar.value.trim();
    }

    if (roleId.value && roleId.value !== 'TODOS') {
        params.role_id = roleId.value;
    }

    if (unidadId.value) {
        params.unidad_id = unidadId.value;
    }

    router.get(
        toUrl(usuariosRoutes.index()),
        params,
        {
            preserveState: true,
            replace: true,
        },
    );
};

const limpiarFiltros = () => {
    buscar.value = '';
    roleId.value = 'TODOS';
    unidadId.value = null;

    router.get(
        toUrl(usuariosRoutes.index()),
        {},
        {
            preserveState: true,
            replace: true,
        },
    );
};

const abrirCrear = () => {
    usuarioSeleccionado.value = null;
    modalFormOpen.value = true;
};

const abrirEditar = (u: Usuario) => {
    usuarioSeleccionado.value = u;
    modalFormOpen.value = true;
};

const abrirDesactivar = (u: Usuario) => {
    usuarioSeleccionado.value = u;
    modalDeactivateOpen.value = true;
};

const reactivarServidor = (u: Usuario) => {
    router.put(
        toUrl(usuariosRoutes.update(u.id)),
        {
            name: u.name,
            email: u.email,
            password: null,
            roles: u.roles ? u.roles.map((r) => r.id) : [],
            unidad_organizacional_id: u.titularidad_activa?.unidad.id ?? null,
            tipo_titularidad: u.titularidad_activa?.tipo ?? 'TITULAR',
            activo: true,
        },
        {
            preserveScroll: true,
            preserveState: true,
        },
    );
};

const confirmarDesactivacion = () => {
    if (!usuarioSeleccionado.value) {
        return;
    }

    isDeactivating.value = true;

    router.delete(toUrl(usuariosRoutes.destroy(usuarioSeleccionado.value.id)), {
        preserveScroll: true,
        onSuccess: () => {
            modalDeactivateOpen.value = false;
            isDeactivating.value = false;
            usuarioSeleccionado.value = null;
        },
        onError: () => {
            isDeactivating.value = false;
        },
    });
};
</script>

<template>
    <Head title="Padrón de Servidores Públicos - TFJA" />

    <div class="space-y-6">
        <Card>
            <CardHeader class="border-b">
                <CardTitle>Padrón de Servidores Públicos</CardTitle>
                <CardDescription>
                    Administración de cuentas institucionales, asignación de perfiles y adscripción al árbol organizacional.
                </CardDescription>
                <CardAction>
                    <Button
                        size="sm"
                        variant="default"
                        class="gap-1.5"
                        @click="abrirCrear"
                    >
                        <Plus class="size-4" />
                        <span>Nuevo Servidor</span>
                    </Button>
                </CardAction>
            </CardHeader>

            <CardContent class="pt-4 space-y-4">
                <!-- Filtros Reactivos -->
                <div class="p-3 bg-slate-50 border border-slate-200 space-y-3">
                    <div class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
                        <div class="space-y-1.5 sm:col-span-6 text-xs">
                            <Label for="filtro-buscar" class="font-bold text-slate-700 uppercase">
                                Búsqueda General
                            </Label>
                            <Input
                                id="filtro-buscar"
                                v-model="buscar"
                                type="search"
                                placeholder="Buscar por nombre, correo o CURP..."
                                @keydown.enter="aplicarFiltros"
                            />
                        </div>

                        <div class="space-y-1.5 sm:col-span-3 text-xs">
                            <Label class="font-bold text-slate-700 uppercase">Rol</Label>
                            <Select v-model="roleId">
                                <SelectTrigger size="sm" class="w-full bg-white">
                                    <SelectValue />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="TODOS">Todos los roles</SelectItem>
                                    <SelectItem
                                        v-for="r in props.roles_disponibles"
                                        :key="r.id"
                                        :value="String(r.id)"
                                    >
                                        {{ r.nombre }}
                                    </SelectItem>
                                </SelectContent>
                            </Select>
                        </div>

                        <div class="flex items-center gap-2 sm:col-span-3">
                            <Button
                                size="sm"
                                variant="default"
                                class="flex-1 gap-1"
                                @click="aplicarFiltros"
                            >
                                <Search class="size-3.5" />
                                <span>Buscar</span>
                            </Button>
                            <Button
                                size="icon-sm"
                                variant="outline"
                                title="Limpiar Filtros"
                                @click="limpiarFiltros"
                            >
                                <RotateCcw class="size-3.5 text-slate-600" />
                            </Button>
                        </div>
                    </div>

                    <!-- Filtro Avanzado por Árbol -->
                    <Collapsible v-model:open="mostrarFiltroArbol" class="border-t border-slate-200 pt-2">
                        <div class="flex items-center justify-between">
                            <CollapsibleTrigger as-child>
                                <Button
                                    type="button"
                                    variant="ghost"
                                    size="sm"
                                    class="text-xs h-7 px-2 font-bold text-slate-700 uppercase flex items-center gap-1.5 hover:bg-slate-200/60"
                                >
                                    <Building2 class="size-3.5 text-slate-500" />
                                    <span>Filtrar por Adscripción Organizacional</span>
                                    <Badge v-if="unidadId" variant="default" class="ml-1 text-[10px] px-1.5 py-0 font-normal">
                                        Adscripción activa
                                    </Badge>
                                    <ChevronDown
                                        class="size-3.5 text-slate-400 transition-transform duration-200"
                                        :class="{ 'rotate-180': mostrarFiltroArbol }"
                                    />
                                </Button>
                            </CollapsibleTrigger>
                        </div>

                        <CollapsibleContent class="pt-2">
                            <UnidadTreeSelect
                                v-model="unidadId"
                                :unidades="props.unidades_arbol"
                            />
                        </CollapsibleContent>
                    </Collapsible>
                </div>

                <!-- Tabla del Padrón -->
                <div class="border border-slate-300 overflow-x-auto">
                    <table class="w-full text-xs text-left border-collapse">
                        <thead>
                            <tr class="border-b border-slate-300 bg-slate-100 text-slate-700 font-bold uppercase">
                                <th class="p-3">Servidor Público</th>
                                <th class="p-3">Identidad Civil</th>
                                <th class="p-3">Rol</th>
                                <th class="p-3">Adscripción y Titularidad</th>
                                <th class="p-3 text-center">Estatus</th>
                                <th class="p-3 text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="u in props.usuarios.data"
                                :key="u.id"
                                class="border-b border-slate-200 hover:bg-slate-50 transition-colors"
                            >
                                <td class="p-3">
                                    <div class="font-semibold text-slate-800">{{ u.name }}</div>
                                    <div class="text-[11px] text-slate-500 font-mono">{{ u.email }}</div>
                                </td>
                                <td class="p-3 font-mono text-slate-600">
                                    <div>CURP: {{ u.persona?.curp ?? 'Sin registro' }}</div>
                                    <div class="text-[10px] text-slate-400">RFC: {{ u.persona?.rfc ?? '—' }}</div>
                                </td>
                                <td class="p-3">
                                    <Badge
                                        v-for="r in u.roles"
                                        :key="r.id"
                                        variant="default"
                                        class="mr-1"
                                    >
                                        {{ r.nombre }}
                                    </Badge>
                                </td>
                                <td class="p-3 text-slate-600">
                                    <div v-if="u.titularidad_activa" class="space-y-0.5">
                                        <div class="font-medium text-slate-800">
                                            {{ u.titularidad_activa.unidad.nombre }}
                                        </div>
                                        <div
                                            v-if="obtenerPadreInmediato(u.titularidad_activa.unidad.id)"
                                            class="text-[11px] text-slate-500"
                                        >
                                            Depende de: {{ obtenerPadreInmediato(u.titularidad_activa.unidad.id) }}
                                        </div>
                                        <div class="pt-0.5">
                                            <Badge variant="outline" class="text-[10px] px-1.5 py-0 font-normal text-slate-700 border-slate-300">
                                                {{ u.titularidad_activa.tipo }}
                                            </Badge>
                                        </div>
                                    </div>
                                    <span v-else class="text-slate-400 italic">Sin adscripción activa</span>
                                </td>
                                <td class="p-3 text-center">
                                    <Badge :variant="u.activo ? 'approved' : 'rejected'">
                                        {{ u.activo ? 'Activo' : 'Inactivo' }}
                                    </Badge>
                                </td>
                                <td class="p-3 text-right">
                                    <DropdownMenu>
                                        <DropdownMenuTrigger as-child>
                                            <Button
                                                variant="ghost"
                                                size="icon-sm"
                                                class="size-7 p-0"
                                                aria-label="Opciones del servidor"
                                            >
                                                <MoreHorizontal class="size-4 text-slate-600" />
                                            </Button>
                                        </DropdownMenuTrigger>

                                        <DropdownMenuContent align="end" class="w-48 text-xs">
                                            <DropdownMenuLabel>Acciones</DropdownMenuLabel>
                                            <DropdownMenuSeparator />

                                            <DropdownMenuItem @select="abrirEditar(u)">
                                                <Edit3 class="mr-2 size-3.5 text-slate-600" />
                                                <span>Editar Datos</span>
                                            </DropdownMenuItem>

                                            <DropdownMenuSeparator />

                                            <DropdownMenuItem
                                                v-if="u.activo"
                                                variant="destructive"
                                                @select="abrirDesactivar(u)"
                                            >
                                                <UserX class="mr-2 size-3.5" />
                                                <span>Desactivar Servidor</span>
                                            </DropdownMenuItem>

                                            <DropdownMenuItem
                                                v-else
                                                class="text-emerald-700 hover:text-emerald-800 focus:text-emerald-800 focus:bg-emerald-50"
                                                @select="reactivarServidor(u)"
                                            >
                                                <UserCheck class="mr-2 size-3.5 text-emerald-600" />
                                                <span>Reactivar Servidor</span>
                                            </DropdownMenuItem>
                                        </DropdownMenuContent>
                                    </DropdownMenu>
                                </td>
                            </tr>

                            <tr v-if="props.usuarios.data.length === 0">
                                <td colspan="6" class="p-6 text-center text-slate-500">
                                    No se encontraron servidores públicos con los criterios seleccionados.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Paginación -->
                <div class="flex flex-col sm:flex-row items-center justify-between gap-3 text-[11px] text-slate-500 pt-2">
                    <span>
                        Mostrando {{ props.usuarios.from ?? 0 }} a {{ props.usuarios.to ?? 0 }} de {{ props.usuarios.total }} registros
                    </span>

                    <div v-if="props.usuarios.last_page > 1" class="flex flex-wrap items-center gap-1">
                        <template v-for="(link, index) in props.usuarios.links" :key="index">
                            <Button
                                v-if="!link.url"
                                size="sm"
                                variant="outline"
                                class="h-7 min-w-7 px-2 text-xs opacity-40 cursor-not-allowed"
                                disabled
                            >
                                <span>{{ formatearEtiqueta(link.label) }}</span>
                            </Button>

                            <Button
                                v-else
                                as-child
                                size="sm"
                                :variant="link.active ? 'default' : 'outline'"
                                class="h-7 min-w-7 px-2 text-xs"
                            >
                                <Link
                                    :href="link.url"
                                    preserve-scroll
                                    preserve-state
                                >
                                    {{ formatearEtiqueta(link.label) }}
                                </Link>
                            </Button>
                        </template>
                    </div>
                </div>
            </CardContent>
        </Card>

        <!-- Modales -->
        <UserDialog
            v-model:open="modalFormOpen"
            :usuario="usuarioSeleccionado"
            :roles="props.roles_disponibles"
            :unidades-arbol="props.unidades_arbol"
        />

        <DeleteUserDialog
            v-model:open="modalDeactivateOpen"
            :usuario="usuarioSeleccionado"
            :processing="isDeactivating"
            @confirm="confirmarDesactivacion"
        />
    </div>
</template>
