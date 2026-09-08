<!-- eslint-disable import/order -->
<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import {
    Edit3,
    MoreHorizontal,
    Plus,
    RotateCcw,
    Search,
    Trash2,
} from '@lucide/vue';
import { ref } from 'vue';
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
import type { Usuario, UsuariosIndexProps } from '@/types/usuarios';
import DeleteUserDialog from './DeleteUserDialog.vue';
import UserDialog from './UserDialog.vue';

const props = defineProps<UsuariosIndexProps>();

const buscar = ref(props.filtros.buscar ?? '');
const roleId = ref(props.filtros.role_id ? String(props.filtros.role_id) : 'TODOS');
const regionId = ref(props.filtros.region_id ? String(props.filtros.region_id) : 'TODOS');

const modalFormOpen = ref(false);
const modalDeleteOpen = ref(false);
const usuarioSeleccionado = ref<Usuario | null>(null);
const isDeleting = ref(false);

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
    router.get(
        toUrl(
            usuariosRoutes.index({
                query: {
                    buscar: buscar.value || undefined,
                    role_id: roleId.value !== 'TODOS' ? roleId.value : undefined,
                    region_id: regionId.value !== 'TODOS' ? regionId.value : undefined,
                },
            }),
        ),
        {},
        {
            preserveState: true,
            replace: true,
        },
    );
};

const limpiarFiltros = () => {
    buscar.value = '';
    roleId.value = 'TODOS';
    regionId.value = 'TODOS';

    aplicarFiltros();
};

const abrirCrear = () => {
    usuarioSeleccionado.value = null;
    modalFormOpen.value = true;
};

const abrirEditar = (u: Usuario) => {
    usuarioSeleccionado.value = u;
    modalFormOpen.value = true;
};

const abrirEliminar = (u: Usuario) => {
    usuarioSeleccionado.value = u;
    modalDeleteOpen.value = true;
};

const confirmarBaja = () => {
    if (!usuarioSeleccionado.value) {
        return;
    }

    isDeleting.value = true;

    router.delete(toUrl(usuariosRoutes.destroy(usuarioSeleccionado.value.id)), {
        preserveScroll: true,
        onSuccess: () => {
            modalDeleteOpen.value = false;
            isDeleting.value = false;
            usuarioSeleccionado.value = null;
        },
        onError: () => {
            isDeleting.value = false;
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
                    Administración de cuentas institucionales, asignación de perfiles y circunscripción jurisdiccional.
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
                <!-- Barra de Filtros -->
                <div class="p-3 bg-slate-50 border border-slate-200 grid grid-cols-1 sm:grid-cols-4 gap-3 items-end">
                    <div class="space-y-1.5 sm:col-span-2 text-xs">
                        <Label for="filtro-buscar" class="font-bold text-slate-700 uppercase">
                            Búsqueda General
                        </Label>
                        <Input
                            id="filtro-buscar"
                            v-model="buscar"
                            type="search"
                            placeholder="Buscar por nombre o correo oficial..."
                            @keydown.enter="aplicarFiltros"
                        />
                    </div>

                    <div class="space-y-1.5 text-xs">
                        <Label class="font-bold text-slate-700 uppercase">Rol</Label>
                        <Select v-model="roleId">
                            <SelectTrigger size="sm" class="w-full bg-white">
                                <SelectValue />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem value="TODOS">Todos los roles</SelectItem>
                                <SelectItem
                                    v-for="r in props.roles"
                                    :key="r.id"
                                    :value="String(r.id)"
                                >
                                    {{ r.nombre }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                    </div>

                    <div class="flex items-center gap-2">
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

                <!-- Tabla de Datos -->
                <div class="border border-slate-300 overflow-x-auto">
                    <table class="w-full text-xs text-left border-collapse">
                        <thead>
                            <tr class="border-b border-slate-300 bg-slate-100 text-slate-700 font-bold uppercase">
                                <th class="p-3">Servidor Público</th>
                                <th class="p-3">Correo Institucional</th>
                                <th class="p-3">Rol</th>
                                <th class="p-3">Adscripción</th>
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
                                <td class="p-3 font-semibold text-slate-800">
                                    {{ u.name }}
                                </td>
                                <td class="p-3 text-slate-600 font-mono">
                                    {{ u.email }}
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
                                    <div>{{ u.region?.nombre ?? 'Sin Región' }}</div>
                                    <div v-if="u.sala" class="text-[11px] text-slate-500">
                                        {{ u.sala.nombre }}
                                    </div>
                                    <div v-if="u.area" class="text-[10px] text-slate-400 font-mono">
                                        &bull; {{ u.area.nombre }}
                                    </div>
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
                                                aria-label="Opciones del usuario"
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
                                                variant="destructive"
                                                @select="abrirEliminar(u)"
                                            >
                                                <Trash2 class="mr-2 size-3.5" />
                                                <span>Eliminar Usuario</span>
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

                <!-- Resumen y Controles de Paginación -->
                <div class="flex flex-col sm:flex-row items-center justify-between gap-3 text-[11px] text-slate-500 pt-2">
                    <span>
                        Mostrando {{ props.usuarios.from ?? 0 }} a {{ props.usuarios.to ?? 0 }} de {{ props.usuarios.total }} registros
                    </span>

                    <div v-if="props.usuarios.last_page > 1" class="flex flex-wrap items-center gap-1">
                        <template v-for="(link, index) in props.usuarios.links" :key="index">
                            <!-- Enlace inactivo o deshabilitado -->
                            <Button
                                v-if="!link.url"
                                size="sm"
                                variant="outline"
                                class="h-7 min-w-7 px-2 text-xs opacity-40 cursor-not-allowed"
                                disabled
                            >
                                <span>{{ formatearEtiqueta(link.label) }}</span>
                            </Button>

                            <!-- Enlace activo navegable -->
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

        <!-- Modales Compuestos -->
        <UserDialog
            v-model:open="modalFormOpen"
            :usuario="usuarioSeleccionado"
            :roles="props.roles"
            :regiones="props.regiones"
        />

        <DeleteUserDialog
            v-model:open="modalDeleteOpen"
            :usuario="usuarioSeleccionado"
            :processing="isDeleting"
            @confirm="confirmarBaja"
        />
    </div>
</template>