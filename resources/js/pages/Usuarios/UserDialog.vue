<!-- eslint-disable import/order -->
<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import { computed, watch } from 'vue';
import UnidadTreeSelect from '@/components/custom/UnidadTreeSelect.vue';
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
    Rol,
    TipoTitularidad,
    UnidadOrganizacional,
    Usuario,
} from '@/types/usuarios';

type Props = {
    open: boolean;
    usuario: Usuario | null;
    roles: Rol[];
    unidadesArbol: UnidadOrganizacional[];
};

const props = defineProps<Props>();

const emit = defineEmits<{
    (e: 'update:open', value: boolean): void;
}>();

const esEdicion = computed(() => {
    return !!props.usuario;
});

const form = useForm({
    name: '',
    email: '',
    password: '',
    curp: '',
    rfc: '',
    roles: [] as number[],
    unidad_organizacional_id: null as number | null,
    tipo_titularidad: 'TITULAR' as TipoTitularidad,
    activo: true,
});

const selectedRoleId = computed({
    get: () => {
        if (form.roles.length > 0) {
            return String(form.roles[0]);
        }

        return '';
    },
    set: (val: string) => {
        if (val) {
            form.roles = [Number(val)];
        } else {
            form.roles = [];
        }
    },
});

watch(
    () => props.usuario,
    (nuevoUsuario) => {
        if (nuevoUsuario) {
            form.name = nuevoUsuario.name ?? '';
            form.email = nuevoUsuario.email ?? '';
            form.password = '';
            form.curp = nuevoUsuario.persona?.curp ?? '';
            form.rfc = nuevoUsuario.persona?.rfc ?? '';
            form.roles = nuevoUsuario.roles?.map((r) => r.id) ?? [];
            form.unidad_organizacional_id = nuevoUsuario.titularidad_activa?.unidad?.id ?? null;
            form.tipo_titularidad = nuevoUsuario.titularidad_activa?.tipo ?? 'TITULAR';
            form.activo = nuevoUsuario.activo ?? true;
        } else {
            form.reset();
            form.clearErrors();
            form.roles = [];
            form.unidad_organizacional_id = null;
            form.tipo_titularidad = 'TITULAR';
            form.activo = true;
        }
    },
    { immediate: true },
);

const submit = () => {
    const payload = {
        name: form.name,
        email: form.email,
        password: form.password ? form.password : null,
        activo: form.activo,
        roles: form.roles,
        unidad_organizacional_id: form.unidad_organizacional_id
            ? Number(form.unidad_organizacional_id)
            : null,
        tipo_titularidad: form.tipo_titularidad,
        ...(esEdicion.value
            ? {}
            : {
                  curp: form.curp.toUpperCase().trim(),
                  rfc: form.rfc.toUpperCase().trim(),
              }),
    };

    if (props.usuario) {
        form.transform(() => payload).put(toUrl(usuariosRoutes.update(props.usuario.id)), {
            preserveScroll: true,
            onSuccess: () => {
                emit('update:open', false);
                form.reset();
            },
        });
    } else {
        form.transform(() => payload).post(toUrl(usuariosRoutes.store()), {
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
        <DialogContent class="sm:max-w-xl max-h-[90vh] overflow-y-auto" :show-close-button="!form.processing">
            <form class="space-y-4" @submit.prevent="submit">
                <DialogHeader>
                    <DialogTitle>
                        {{ esEdicion ? 'Actualizar Servidor Público' : 'Registrar Nuevo Servidor Público' }}
                    </DialogTitle>
                    <DialogDescription>
                        {{
                            esEdicion
                                ? 'Modifique las credenciales, perfiles o la adscripción en el árbol organizacional.'
                                : 'Capture la identidad civil, cuenta institucional y asigne la plaza correspondiente.'
                        }}
                    </DialogDescription>
                </DialogHeader>

                <div class="py-2 space-y-3 text-xs">
                    <!-- Nombre Completo -->
                    <div class="space-y-1.5">
                        <Label for="usr-name" class="font-bold text-slate-700 uppercase">
                            Nombre Completo <span class="text-red-600">*</span>
                        </Label>
                        <Input
                            id="usr-name"
                            v-model="form.name"
                            type="text"
                            placeholder="Ej. Lic. Fernando Garza Ruiz"
                            :disabled="form.processing"
                            :aria-invalid="!!form.errors.name"
                            required
                        />
                        <span v-if="form.errors.name" class="text-[11px] text-red-600 block">
                            {{ form.errors.name }}
                        </span>
                    </div>

                    <!-- Identidad Civil (Solo creación; en edición es inmutable) -->
                    <div v-if="!esEdicion" class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div class="space-y-1.5">
                            <Label for="usr-curp" class="font-bold text-slate-700 uppercase">
                                CURP <span class="text-red-600">*</span>
                            </Label>
                            <Input
                                id="usr-curp"
                                v-model="form.curp"
                                type="text"
                                maxlength="18"
                                class="uppercase font-mono"
                                placeholder="18 caracteres"
                                :disabled="form.processing"
                                :aria-invalid="!!form.errors.curp"
                                required
                            />
                            <span v-if="form.errors.curp" class="text-[11px] text-red-600 block">
                                {{ form.errors.curp }}
                            </span>
                        </div>

                        <div class="space-y-1.5">
                            <Label for="usr-rfc" class="font-bold text-slate-700 uppercase">
                                RFC <span class="text-red-600">*</span>
                            </Label>
                            <Input
                                id="usr-rfc"
                                v-model="form.rfc"
                                type="text"
                                maxlength="13"
                                class="uppercase font-mono"
                                placeholder="12 o 13 caracteres"
                                :disabled="form.processing"
                                :aria-invalid="!!form.errors.rfc"
                                required
                            />
                            <span v-if="form.errors.rfc" class="text-[11px] text-red-600 block">
                                {{ form.errors.rfc }}
                            </span>
                        </div>
                    </div>

                    <div v-else class="p-2.5 bg-slate-50 border border-slate-200 flex gap-6 text-[11px]">
                        <div>
                            <span class="font-bold uppercase text-slate-500">CURP:</span>
                            <span class="font-mono font-semibold ml-1.5 text-slate-800">{{ form.curp || 'Sin registro' }}</span>
                        </div>
                        <div>
                            <span class="font-bold uppercase text-slate-500">RFC:</span>
                            <span class="font-mono font-semibold ml-1.5 text-slate-800">{{ form.rfc || '—' }}</span>
                        </div>
                    </div>

                    <!-- Credenciales -->
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
                                required
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
                                :placeholder="esEdicion ? 'Mantener contraseña actual' : '••••••••••••'"
                                :disabled="form.processing"
                                :aria-invalid="!!form.errors.password"
                                :required="!esEdicion"
                            />
                            <span v-if="form.errors.password" class="text-[11px] text-red-600 block">
                                {{ form.errors.password }}
                            </span>
                        </div>
                    </div>

                    <!-- Rol Institucional -->
                    <div class="space-y-1.5">
                        <Label for="usr-role" class="font-bold text-slate-700 uppercase">
                            Rol Institucional <span class="text-red-600">*</span>
                        </Label>
                        <Select
                            v-model="selectedRoleId"
                            :disabled="form.processing"
                        >
                            <SelectTrigger id="usr-role" class="w-full bg-white" :aria-invalid="!!form.errors.roles">
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

                    <!-- Adscripción Organizacional (Grafo) -->
                    <div class="p-3 bg-slate-50 border border-slate-200 space-y-3">
                        <span class="font-bold text-slate-800 uppercase tracking-wide block text-[11px]">
                            Adscripción Organizacional
                        </span>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <div class="sm:col-span-2 space-y-1.5">
                                <Label class="font-bold text-slate-700 uppercase">
                                    Unidad del Árbol <span class="text-red-600">*</span>
                                </Label>
                                <UnidadTreeSelect
                                    v-model="form.unidad_organizacional_id"
                                    :unidades="props.unidadesArbol"
                                    :disabled="form.processing"
                                />
                                <span v-if="form.errors.unidad_organizacional_id" class="text-[11px] text-red-600 block">
                                    {{ form.errors.unidad_organizacional_id }}
                                </span>
                            </div>

                            <div class="space-y-1.5">
                                <Label class="font-bold text-slate-700 uppercase">
                                    Titularidad <span class="text-red-600">*</span>
                                </Label>
                                <Select
                                    v-model="form.tipo_titularidad"
                                    :disabled="form.processing"
                                >
                                    <SelectTrigger class="w-full bg-white">
                                        <SelectValue />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="TITULAR">Titular</SelectItem>
                                        <SelectItem value="ENCARGADO_DESPACHO">Encargado de Despacho</SelectItem>
                                        <SelectItem value="SUPLENTE">Suplente</SelectItem>
                                    </SelectContent>
                                </Select>
                            </div>
                        </div>
                    </div>

                    <!-- Estatus Activo -->
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
