<!-- eslint-disable import/order -->
<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
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
    Area,
    Region,
    Rol,
    Sala,
    Usuario,
    UsuarioFormPayload,
} from '@/types/usuarios';

type Props = {
    open: boolean;
    usuario: Usuario | null;
    roles: Rol[];
    regiones: Region[];
};

type FormState = Omit<UsuarioFormPayload, 'password'> & {
    password: string;
};

const props = defineProps<Props>();

const emit = defineEmits<{
    (e: 'update:open', value: boolean): void;
}>();

const esEdicion = computed(() => !!props.usuario);

const salasOptions = ref<Sala[]>([]);
const areasOptions = ref<Area[]>([]);
const cargandoSalas = ref(false);
const cargandoAreas = ref(false);

const form = useForm<FormState>({
    name: '',
    email: '',
    password: '',
    roles: [],
    region_id: null,
    sala_id: null,
    area_id: null,
    activo: true,
});

const selectedRoleId = computed({
    get: () => (form.roles.length > 0 ? String(form.roles[0]) : ''),
    set: (val: string) => {
        form.roles = val ? [Number(val)] : [];
    },
});

const cargarSalas = async (regionId: number) => {
    try {
        cargandoSalas.value = true;

        const response = await fetch(`/catalogos/regiones/${regionId}/salas`);

        if (response.ok) {
            salasOptions.value = await response.json();
        }
    } finally {
        cargandoSalas.value = false;
    }
};

const cargarAreas = async (salaId: number) => {
    try {
        cargandoAreas.value = true;

        const response = await fetch(`/catalogos/salas/${salaId}/areas`);

        if (response.ok) {
            areasOptions.value = await response.json();
        }
    } finally {
        cargandoAreas.value = false;
    }
};

const onRegionChange = async (val: unknown) => {
    const regId = Number(val);

    form.region_id = regId || null;
    form.sala_id = null;
    form.area_id = null;
    salasOptions.value = [];
    areasOptions.value = [];

    if (regId) {
        await cargarSalas(regId);
    }
};

const onSalaChange = async (val: unknown) => {
    const sId = Number(val);

    form.sala_id = sId || null;
    form.area_id = null;
    areasOptions.value = [];

    if (sId) {
        await cargarAreas(sId);
    }
};

watch(
    () => props.usuario,
    async (nuevoUsuario) => {
        if (nuevoUsuario) {
            form.name = nuevoUsuario.name ?? '';
            form.email = nuevoUsuario.email ?? '';
            form.password = '';
            form.roles = nuevoUsuario.roles?.map((r) => r.id) ?? [];
            form.region_id = nuevoUsuario.region_id ?? null;
            form.sala_id = nuevoUsuario.sala_id ?? null;
            form.area_id = nuevoUsuario.area_id ?? null;
            form.activo = nuevoUsuario.activo ?? true;

            if (nuevoUsuario.region_id) {
                await cargarSalas(nuevoUsuario.region_id);
            }

            if (nuevoUsuario.sala_id) {
                await cargarAreas(nuevoUsuario.sala_id);
            }
        } else {
            form.reset();
            form.clearErrors();
            salasOptions.value = [];
            areasOptions.value = [];
        }
    },
    { immediate: true },
);

const submit = () => {
    form.transform((data) => ({
        ...data,
        password: data.password ? data.password : null,
        region_id: data.region_id ? Number(data.region_id) : null,
        sala_id: data.sala_id ? Number(data.sala_id) : null,
        area_id: data.area_id ? Number(data.area_id) : null,
    }));

    if (props.usuario) {
        form.put(toUrl(usuariosRoutes.update(props.usuario.id)), {
            preserveScroll: true,
            onSuccess: () => {
                emit('update:open', false);
                form.reset();
            },
        });
    } else {
        form.post(toUrl(usuariosRoutes.store()), {
            preserveScroll: true,
            onSuccess: () => {
                emit('update:open', false);
                form.reset();
            },
        });
    }
};
</script>

<template>
    <Dialog
        :open="props.open"
        @update:open="(val) => emit('update:open', val)"
    >
        <DialogContent class="sm:max-w-xl" :show-close-button="!form.processing">
            <form @submit.prevent="submit" class="space-y-4">
                <DialogHeader>
                    <DialogTitle>
                        {{ esEdicion ? 'Actualizar Servidor Público' : 'Registrar Nuevo Servidor Público' }}
                    </DialogTitle>
                    <DialogDescription>
                        Asigne los datos generales, perfil institucional y adscripción jurisdiccional en cascada.
                    </DialogDescription>
                </DialogHeader>

                <div class="py-2 space-y-3 text-xs">
                    <div class="space-y-1.5">
                        <Label for="usr-name" class="font-bold text-slate-700 uppercase">
                            Nombre Completo <span class="text-red-600">*</span>
                        </Label>
                        <Input
                            id="usr-name"
                            v-model="form.name"
                            type="text"
                            placeholder="Ej. Lic. Roberto Méndez Cruz"
                            :disabled="form.processing"
                            :aria-invalid="!!form.errors.name"
                        />
                        <span v-if="form.errors.name" class="text-[11px] text-red-600 block">
                            {{ form.errors.name }}
                        </span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div class="space-y-1.5">
                            <Label for="usr-email" class="font-bold text-slate-700 uppercase">
                                Correo Oficial <span class="text-red-600">*</span>
                            </Label>
                            <Input
                                id="usr-email"
                                v-model="form.email"
                                type="email"
                                placeholder="usuario@tfja.gob.mx"
                                :disabled="form.processing"
                                :aria-invalid="!!form.errors.email"
                            />
                            <span v-if="form.errors.email" class="text-[11px] text-red-600 block">
                                {{ form.errors.email }}
                            </span>
                        </div>

                        <div class="space-y-1.5">
                            <Label for="usr-password" class="font-bold text-slate-700 uppercase">
                                Contraseña
                                <span v-if="!esEdicion" class="text-red-600">*</span>
                                <span v-else class="text-[10px] text-slate-400 font-normal lowercase">(opcional)</span>
                            </Label>
                            <Input
                                id="usr-password"
                                v-model="form.password"
                                type="password"
                                placeholder="••••••••••••"
                                :disabled="form.processing"
                                :aria-invalid="!!form.errors.password"
                            />
                            <span v-if="form.errors.password" class="text-[11px] text-red-600 block">
                                {{ form.errors.password }}
                            </span>
                        </div>
                    </div>

                    <div class="space-y-1.5">
                        <Label for="usr-role" class="font-bold text-slate-700 uppercase">
                            Rol Institucional <span class="text-red-600">*</span>
                        </Label>
                        <Select
                            v-model="selectedRoleId"
                            :disabled="form.processing"
                        >
                            <SelectTrigger id="usr-role" class="w-full" :aria-invalid="!!form.errors.roles">
                                <SelectValue placeholder="Seleccione un perfil institucional..." />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem
                                    v-for="rol in props.roles"
                                    :key="rol.id"
                                    :value="String(rol.id)"
                                >
                                    {{ rol.nombre }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                        <span v-if="form.errors.roles" class="text-[11px] text-red-600 block">
                            {{ form.errors.roles }}
                        </span>
                    </div>

                    <div class="p-3 bg-slate-50 border border-slate-200 space-y-3">
                        <span class="font-bold text-slate-800 uppercase tracking-wide block text-[11px]">
                            Adscripción Jurisdiccional
                        </span>

                        <div class="space-y-1.5">
                            <Label class="font-bold text-slate-700 uppercase">
                                1. Región
                            </Label>
                            <Select
                                :model-value="form.region_id ? String(form.region_id) : undefined"
                                :disabled="form.processing"
                                @update:model-value="onRegionChange"
                            >
                                <SelectTrigger class="w-full bg-white">
                                    <SelectValue placeholder="Seleccione región..." />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem
                                        v-for="reg in props.regiones"
                                        :key="reg.id"
                                        :value="String(reg.id)"
                                    >
                                        {{ reg.nombre }}
                                    </SelectItem>
                                </SelectContent>
                            </Select>
                        </div>

                        <div class="space-y-1.5">
                            <Label class="font-bold text-slate-700 uppercase">
                                2. Sala Regional
                            </Label>
                            <Select
                                :model-value="form.sala_id ? String(form.sala_id) : undefined"
                                :disabled="form.processing || !form.region_id || cargandoSalas"
                                @update:model-value="onSalaChange"
                            >
                                <SelectTrigger class="w-full bg-white">
                                    <SelectValue :placeholder="form.region_id ? (cargandoSalas ? 'Cargando salas...' : 'Seleccione sala regional...') : 'Primero elija una región'" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem
                                        v-for="sala in salasOptions"
                                        :key="sala.id"
                                        :value="String(sala.id)"
                                    >
                                        {{ sala.nombre }}
                                    </SelectItem>
                                </SelectContent>
                            </Select>
                        </div>

                        <div class="space-y-1.5">
                            <Label class="font-bold text-slate-700 uppercase">
                                3. Área / Ponencia
                            </Label>
                            <Select
                                :model-value="form.area_id ? String(form.area_id) : undefined"
                                :disabled="form.processing || !form.sala_id || cargandoAreas"
                                @update:model-value="(val) => form.area_id = val ? Number(val) : null"
                            >
                                <SelectTrigger class="w-full bg-white">
                                    <SelectValue :placeholder="form.sala_id ? (cargandoAreas ? 'Cargando áreas...' : 'Seleccione área o ponencia...') : 'Primero elija una sala'" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem
                                        v-for="area in areasOptions"
                                        :key="area.id"
                                        :value="String(area.id)"
                                    >
                                        {{ area.nombre }}
                                    </SelectItem>
                                </SelectContent>
                            </Select>
                        </div>
                    </div>

                    <div class="flex items-center gap-2.5 pt-1">
                        <Checkbox
                            id="usr-activo"
                            v-model="form.activo"
                            :disabled="form.processing"
                        />
                        <Label for="usr-activo" class="cursor-pointer select-none text-xs font-semibold text-slate-700">
                            Servidor Público con acceso habilitado al sistema
                        </Label>
                    </div>
                </div>

                <DialogFooter class="gap-2">
                    <DialogClose as-child>
                        <Button
                            type="button"
                            variant="outline"
                            size="sm"
                            :disabled="form.processing"
                        >
                            Cancelar
                        </Button>
                    </DialogClose>
                    <Button
                        type="submit"
                        variant="default"
                        size="sm"
                        :disabled="form.processing"
                    >
                        {{ form.processing ? 'Guardando...' : (esEdicion ? 'Guardar Cambios' : 'Registrar Servidor') }}
                    </Button>
                </DialogFooter>
            </form>
        </DialogContent>
    </Dialog>
</template>