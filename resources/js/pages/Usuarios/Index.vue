<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import {
    Building,
    Edit,
    MoreHorizontal,
    Plus,
    Search,
    Shield,
    Trash2,
} from '@lucide/vue';
import { ref } from 'vue';
import Heading from '@/components/Heading.vue';
import { Avatar, AvatarFallback } from '@/components/ui/avatar';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { Input } from '@/components/ui/input';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import type { Usuario, UsuariosIndexProps } from '@/types/usuarios';
import DeleteUserDialog from './DeleteUserDialog.vue';
import UserDialog from './UserDialog.vue';

const props = defineProps<UsuariosIndexProps>();

const buscar = ref(props.filtros.buscar || '');
const roleId = ref(props.filtros.role_id ? String(props.filtros.role_id) : 'TODOS');
const regionId = ref(props.filtros.region_id ? String(props.filtros.region_id) : 'TODOS');

const modalFormOpen = ref(false);
const modalDeleteOpen = ref(false);
const usuarioSeleccionado = ref<Usuario | null>(null);
const isDeleting = ref(false);

const aplicarFiltros = () => {
    router.get(
        '/usuarios',
        {
            buscar: buscar.value || undefined,
            role_id: roleId.value !== 'TODOS' ? roleId.value : undefined,
            region_id: regionId.value !== 'TODOS' ? regionId.value : undefined,
        },
        {
            preserveState: true,
            replace: true,
        },
    );
};

const onRoleChange = (val: unknown) => {
    roleId.value = val !== null && val !== undefined ? String(val) : 'TODOS';

    aplicarFiltros();
};

const onRegionChange = (val: unknown) => {
    regionId.value = val !== null && val !== undefined ? String(val) : 'TODOS';

    aplicarFiltros();
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

const abrirEditar = (usuario: Usuario) => {
    usuarioSeleccionado.value = usuario;
    modalFormOpen.value = true;
};

const abrirEliminar = (usuario: Usuario) => {
    usuarioSeleccionado.value = usuario;
    modalDeleteOpen.value = true;
};

const confirmarBaja = () => {
    if (!usuarioSeleccionado.value) {
        return;
    }

    isDeleting.value = true;

    router.delete(`/usuarios/${usuarioSeleccionado.value.id}`, {
        preserveScroll: true,
        onSuccess: () => {
            modalDeleteOpen.value = false;
            isDeleting.value = false;
        },
        onError: () => {
            isDeleting.value = false;
        },
    });
};

const getInitials = (nombre: string) => {
    return nombre
        .split(' ')
        .filter(Boolean)
        .slice(0, 2)
        .map((n) => n[0])
        .join('')
        .toUpperCase();
};

const formatAdscripcion = (u: Usuario) => {
    if (u.sala && u.area) {
        return `${u.sala.nombre} • ${u.area.nombre}`;
    }

    if (u.sala) {
        return u.sala.nombre;
    }

    if (u.region) {
        return u.region.nombre;
    }

    return 'Ámbito Central / DGTIC';
};
</script>

<template>
    <Head title="Padrón de Usuarios - TFJA" />

    <div class="px-4 py-6 md:px-8 space-y-6 max-w-7xl mx-auto">
        <!-- Encabezado y Acción Principal -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <Heading
                variant="small"
                title="Padrón de Usuarios Institucionales"
                description="Control de identidades, perfiles de acceso y adscripciones territoriales del TFJA."
            />

            <Button @click="abrirCrear" size="sm" class="text-xs shadow-xs">
                <Plus class="mr-1.5 h-3.5 w-3.5" />
                Nuevo Usuario
            </Button>
        </div>

        <!-- Tarjeta Principal con Tabla y Filtros -->
        <Card>
            <CardHeader class="pb-3">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3">
                    <div>
                        <CardTitle class="text-sm font-semibold">Cuentas Registradas</CardTitle>
                        <CardDescription class="text-xs">
                            Mostrando {{ usuarios.from || 0 }} - {{ usuarios.to || 0 }} de {{ usuarios.total }} servidores públicos.
                        </CardDescription>
                    </div>

                    <!-- Barra de Búsqueda y Filtros con Componentes Shadcn -->
                    <div class="flex flex-wrap items-center gap-2">
                        <div class="relative w-full sm:w-56">
                            <Search class="absolute left-2.5 top-2.5 h-3.5 w-3.5 text-muted-foreground" />
                            <Input
                                v-model="buscar"
                                @keydown.enter="aplicarFiltros"
                                placeholder="Buscar por nombre o correo..."
                                class="pl-8 text-xs h-8"
                            />
                        </div>

                        <!-- Selector de Rol -->
                        <div class="w-40">
                            <Select :model-value="roleId" @update:model-value="onRoleChange">
                                <SelectTrigger class="h-8 text-xs">
                                    <SelectValue placeholder="Todos los roles" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="TODOS" class="text-xs">Todos los roles</SelectItem>
                                    <SelectItem
                                        v-for="r in roles"
                                        :key="r.id"
                                        :value="String(r.id)"
                                        class="text-xs"
                                    >
                                        {{ r.nombre }}
                                    </SelectItem>
                                </SelectContent>
                            </Select>
                        </div>

                        <!-- Selector de Región -->
                        <div class="w-44">
                            <Select :model-value="regionId" @update:model-value="onRegionChange">
                                <SelectTrigger class="h-8 text-xs">
                                    <SelectValue placeholder="Todas las regiones" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="TODOS" class="text-xs">Todas las regiones</SelectItem>
                                    <SelectItem
                                        v-for="reg in regiones"
                                        :key="reg.id"
                                        :value="String(reg.id)"
                                        class="text-xs"
                                    >
                                        {{ reg.nombre }}
                                    </SelectItem>
                                </SelectContent>
                            </Select>
                        </div>

                        <Button
                            v-if="buscar || roleId !== 'TODOS' || regionId !== 'TODOS'"
                            @click="limpiarFiltros"
                            variant="ghost"
                            size="sm"
                            class="text-xs h-8"
                        >
                            Limpiar
                        </Button>
                    </div>
                </div>
            </CardHeader>

            <CardContent>
                <div class="rounded-lg border border-border/80 bg-background overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead class="border-b border-border bg-muted/40 text-muted-foreground">
                                <tr>
                                    <th class="h-9 px-4 font-medium">Servidor Público</th>
                                    <th class="h-9 px-4 font-medium">Rol Normativo</th>
                                    <th class="h-9 px-4 font-medium">Adscripción</th>
                                    <th class="h-9 px-4 font-medium text-center">Estado</th>
                                    <th class="h-9 px-4 font-medium text-right">Acciones</th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-border">
                                <tr
                                    v-for="u in usuarios.data"
                                    :key="u.id"
                                    class="transition-colors hover:bg-muted/30"
                                >
                                    <!-- Nombre, Correo y Avatar -->
                                    <td class="p-3.5">
                                        <div class="flex items-center gap-3">
                                            <Avatar class="h-8 w-8">
                                                <AvatarFallback class="text-[11px] font-semibold bg-primary/10 text-primary">
                                                    {{ getInitials(u.name) }}
                                                </AvatarFallback>
                                            </Avatar>

                                            <div>
                                                <div class="font-semibold text-foreground">{{ u.name }}</div>
                                                <div class="text-[11px] font-mono text-muted-foreground">{{ u.email }}</div>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Roles institucionales -->
                                    <td class="p-3.5">
                                        <div class="flex flex-wrap gap-1">
                                            <Badge
                                                v-for="rol in u.roles"
                                                :key="rol.id"
                                                variant="outline"
                                                class="text-[11px] font-normal gap-1"
                                            >
                                                <Shield class="h-3 w-3 text-tfja-blue-hover" />
                                                {{ rol.nombre }}
                                            </Badge>
                                        </div>
                                    </td>

                                    <!-- Jurisdicción Territorial -->
                                    <td class="p-3.5 text-muted-foreground max-w-xs truncate">
                                        <div class="flex items-center gap-1.5">
                                            <Building class="h-3.5 w-3.5 text-muted-foreground/70 shrink-0" />
                                            <span>{{ formatAdscripcion(u) }}</span>
                                        </div>
                                    </td>

                                    <!-- Estatus Activo / Inactivo -->
                                    <td class="p-3.5 text-center">
                                        <Badge
                                            :variant="u.activo ? 'default' : 'secondary'"
                                            class="text-[10px]"
                                        >
                                            {{ u.activo ? 'Activo' : 'Inactivo' }}
                                        </Badge>
                                    </td>

                                    <!-- Acciones por registro con DropdownMenu -->
                                    <td class="p-3.5 text-right">
                                        <DropdownMenu>
                                            <DropdownMenuTrigger as-child>
                                                <Button variant="ghost" size="sm" class="h-7 w-7 p-0">
                                                    <MoreHorizontal class="h-4 w-4" />
                                                </Button>
                                            </DropdownMenuTrigger>

                                            <DropdownMenuContent align="end">
                                                <DropdownMenuItem @click="abrirEditar(u)" class="text-xs cursor-pointer">
                                                    <Edit class="mr-2 h-3.5 w-3.5" />
                                                    Editar Datos
                                                </DropdownMenuItem>

                                                <DropdownMenuItem
                                                    @click="abrirEliminar(u)"
                                                    class="text-xs text-destructive focus:text-destructive cursor-pointer"
                                                >
                                                    <Trash2 class="mr-2 h-3.5 w-3.5" />
                                                    Dar de Baja
                                                </DropdownMenuItem>
                                            </DropdownMenuContent>
                                        </DropdownMenu>
                                    </td>
                                </tr>

                                <tr v-if="usuarios.data.length === 0">
                                    <td colspan="5" class="p-8 text-center text-xs text-muted-foreground">
                                        No se encontraron servidores públicos con los criterios seleccionados.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Paginación Inertia -->
                <div v-if="usuarios.links.length > 3" class="flex items-center justify-between pt-4">
                    <p class="text-[11px] text-muted-foreground">
                        Página {{ usuarios.current_page }} de {{ usuarios.last_page }}
                    </p>

                    <div class="flex items-center space-x-1">
                        <Link
                            v-for="(link, i) in usuarios.links"
                            :key="i"
                            :href="link.url || '#'"
                            :class="[
                                'h-8 min-w-8 px-2 inline-flex items-center justify-center rounded-md text-xs font-medium transition-colors',
                                link.active
                                    ? 'bg-primary text-primary-foreground font-semibold'
                                    : link.url
                                    ? 'border border-border bg-background hover:bg-muted text-foreground'
                                    : 'text-muted-foreground pointer-events-none opacity-40',
                            ]"
                            preserve-scroll
                            preserve-state
                        >
                            <span v-html="link.label" />
                        </Link>
                    </div>
                </div>
            </CardContent>
        </Card>

        <!-- Modales Conectados -->
        <UserDialog
            v-model:open="modalFormOpen"
            :usuario="usuarioSeleccionado"
            :roles="roles"
            :regiones="regiones"
        />

        <DeleteUserDialog
            v-model:open="modalDeleteOpen"
            :usuario="usuarioSeleccionado"
            :processing="isDeleting"
            @confirm="confirmarBaja"
        />
    </div>
</template>