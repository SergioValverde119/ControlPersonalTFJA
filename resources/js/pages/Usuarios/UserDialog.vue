<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import { Building2, KeyRound, ShieldCheck, User as UserIcon } from '@lucide/vue';
import { computed, ref, watch } from 'vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import {
    Dialog,
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
import type { Area, Region, Rol, Sala, Usuario } from '@/types/usuarios';

const props = defineProps<{
    open: boolean;
    usuario: Usuario | null;
    roles: Rol[];
    regiones: Region[];
}>();

const emit = defineEmits<{
    (e: 'update:open', val: boolean): void;
}>();

interface FormState {
    name: string;
    email: string;
    password: string;
    roles: number[];
    region_id: number | null;
    sala_id: number | null;
    area_id: number | null;
    activo: boolean;
}

const salas = ref<Sala[]>([]);
const areas = ref<Area[]>([]);
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

const cargarSalas = async (regionId: number, salaPreviaId?: number | null) => {
    cargandoSalas.value = true;

    try {
        const res = await fetch(`/catalogos/regiones/${regionId}/salas`);

        salas.value = await res.json();

        if (salaPreviaId) {
            form.sala_id = salaPreviaId;
        }
    } catch {
        salas.value = [];
    } finally {
        cargandoSalas.value = false;
    }
};

const cargarAreas = async (salaId: number, areaPreviaId?: number | null) => {
    cargandoAreas.value = true;

    try {
        const res = await fetch(`/catalogos/salas/${salaId}/areas`);

        areas.value = await res.json();

        if (areaPreviaId) {
            form.area_id = areaPreviaId;
        }
    } catch {
        areas.value = [];
    } finally {
        cargandoAreas.value = false;
    }
};

watch(
    () => props.open,
    async (isOpen) => {
        if (!isOpen) {
            return;
        }

        form.clearErrors();

        if (props.usuario) {
            form.name = props.usuario.name;
            form.email = props.usuario.email;
            form.password = '';
            form.roles = props.usuario.roles.map((r) => r.id);
            form.region_id = props.usuario.region_id;
            form.activo = props.usuario.activo;

            if (props.usuario.region_id) {
                await cargarSalas(props.usuario.region_id, props.usuario.sala_id);
            }

            if (props.usuario.sala_id) {
                await cargarAreas(props.usuario.sala_id, props.usuario.area_id);
            }
        } else {
            form.reset();
            form.roles = props.roles.length ? [props.roles[0].id] : [];
            form.activo = true;
            salas.value = [];
            areas.value = [];
        }
    },
);

const rolSeleccionado = computed(() => {
    const rolId = form.roles[0];

    return props.roles.find((r) => r.id === rolId)?.clave || null;
});

const esPresidente = computed(() => rolSeleccionado.value === 'MAGISTRADO_PRESIDENTE');
const esPonente = computed(() => rolSeleccionado.value === 'MAGISTRADO_PONENTE');
const esCentral = computed(
    () => rolSeleccionado.value === 'ADMIN_DGTIC' || rolSeleccionado.value === 'RECURSOS_HUMANOS',
);

const areasFiltradas = computed(() => {
    if (esPonente.value) {
        return areas.value.filter((a) => a.tipo === 'PONENCIA');
    }

    return areas.value;
});

const onRegionChange = (val: unknown) => {
    const strVal = val !== null && val !== undefined ? String(val) : '';

    form.region_id = strVal && strVal !== 'NINGUNA' ? Number(strVal) : null;
    form.sala_id = null;
    form.area_id = null;
    salas.value = [];
    areas.value = [];

    if (form.region_id) {
        cargarSalas(form.region_id);
    }
};

const onSalaChange = (val: unknown) => {
    const strVal = val !== null && val !== undefined ? String(val) : '';

    form.sala_id = strVal && strVal !== 'NINGUNA' ? Number(strVal) : null;
    form.area_id = null;
    areas.value = [];

    if (form.sala_id && !esPresidente.value) {
        cargarAreas(form.sala_id);
    }
};

const onAreaChange = (val: unknown) => {
    const strVal = val !== null && val !== undefined ? String(val) : '';

    form.area_id = strVal && strVal !== 'NINGUNA' ? Number(strVal) : null;
};

const onRolChange = (val: unknown) => {
    if (val !== null && val !== undefined && val !== '') {
        form.roles = [Number(val)];
    }
};

const onActivoChange = (checked: boolean | 'indeterminate') => {
    form.activo = Boolean(checked);
};

watch(esPresidente, (preside) => {
    if (preside) {
        form.area_id = null;
    }
});

const submit = () => {
    form.transform((data) => ({
        ...data,
        password: data.password ? data.password : null,
    }));

    if (props.usuario) {
        form.put(`/usuarios/${props.usuario.id}`, {
            onSuccess: () => emit('update:open', false),
        });
    } else {
        form.post('/usuarios', {
            onSuccess: () => emit('update:open', false),
        });
    }
};
</script>

<template>
    <Dialog :open="open" @update:open="emit('update:open', $event)">
        <DialogContent class="max-w-2xl max-h-[90vh] overflow-y-auto">
            <DialogHeader>
                <div class="flex items-center gap-2">
                    <div class="p-2 rounded-lg bg-primary/10 text-primary">
                        <UserIcon class="h-5 w-5" />
                    </div>

                    <div>
                        <DialogTitle class="text-base font-bold">
                            {{ usuario ? 'Modificar Usuario Institucional' : 'Alta de Personal TFJA' }}
                        </DialogTitle>
                        <DialogDescription class="text-xs">
                            Complete las credenciales de acceso, rol y jurisdicción territorial.
                        </DialogDescription>
                    </div>
                </div>
            </DialogHeader>

            <form @submit.prevent="submit" class="space-y-4 py-2">
                <!-- Nombre y Correo -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <Label class="text-xs font-semibold">Nombre Completo *</Label>
                        <Input
                            v-model="form.name"
                            required
                            placeholder="Ej. Lic. Roberto Gómez Fernández"
                            class="text-xs"
                        />
                        <span v-if="form.errors.name" class="text-[11px] text-destructive">{{ form.errors.name }}</span>
                    </div>

                    <div class="space-y-1.5">
                        <Label class="text-xs font-semibold">Correo Institucional *</Label>
                        <Input
                            v-model="form.email"
                            type="email"
                            required
                            placeholder="usuario@tfja.gob.mx"
                            class="text-xs font-mono"
                        />
                        <span v-if="form.errors.email" class="text-[11px] text-destructive">{{ form.errors.email }}</span>
                    </div>
                </div>

                <!-- Contraseña y Rol -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <Label class="text-xs font-semibold flex items-center gap-1">
                            <KeyRound class="h-3.5 w-3.5 text-muted-foreground" />
                            {{ usuario ? 'Nueva Contraseña (Opcional)' : 'Contraseña Temporal *' }}
                        </Label>
                        <Input
                            v-model="form.password"
                            type="password"
                            :required="!usuario"
                            placeholder="••••••••••••"
                            class="text-xs font-mono"
                        />
                        <span v-if="form.errors.password" class="text-[11px] text-destructive">{{ form.errors.password }}</span>
                    </div>

                    <div class="space-y-1.5">
                        <Label class="text-xs font-semibold flex items-center gap-1">
                            <ShieldCheck class="h-3.5 w-3.5 text-muted-foreground" />
                            Rol Institucional *
                        </Label>
                        <Select
                            :model-value="form.roles[0] ? String(form.roles[0]) : undefined"
                            @update:model-value="onRolChange"
                        >
                            <SelectTrigger class="text-xs">
                                <SelectValue placeholder="Seleccione Rol" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem
                                    v-for="rol in roles"
                                    :key="rol.id"
                                    :value="String(rol.id)"
                                    class="text-xs"
                                >
                                    {{ rol.nombre }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                    </div>
                </div>

                <!-- Cascada Territorial -->
                <div class="rounded-lg border border-border/80 bg-muted/20 p-4 space-y-3">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-1.5 text-xs font-bold uppercase tracking-wider text-foreground">
                            <Building2 class="h-3.5 w-3.5 text-tfja-blue-hover" />
                            Adscripción Territorial
                        </div>
                        <span v-if="esCentral" class="text-[11px] text-muted-foreground">
                            Ámbito Central / No obligatorio
                        </span>
                        <span v-else class="text-[11px] text-amber-600 dark:text-amber-400 font-medium">
                            Obligatorio según rol
                        </span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                        <!-- 1. Región -->
                        <div class="space-y-1">
                            <Label class="text-[11px]">Región</Label>
                            <Select
                                :model-value="form.region_id ? String(form.region_id) : 'NINGUNA'"
                                @update:model-value="onRegionChange"
                            >
                                <SelectTrigger class="h-8 text-xs">
                                    <SelectValue placeholder="-- Central / Ninguna --" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="NINGUNA" class="text-xs">-- Central / Ninguna --</SelectItem>
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

                        <!-- 2. Sala -->
                        <div class="space-y-1">
                            <Label class="text-[11px]">
                                Sala Regional
                                <span v-if="cargandoSalas" class="text-[10px] text-muted-foreground">(Cargando...)</span>
                            </Label>
                            <Select
                                :model-value="form.sala_id ? String(form.sala_id) : 'NINGUNA'"
                                :disabled="!form.region_id || cargandoSalas"
                                @update:model-value="onSalaChange"
                            >
                                <SelectTrigger class="h-8 text-xs">
                                    <SelectValue placeholder="-- Seleccione Sala --" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="NINGUNA" class="text-xs">-- Seleccione Sala --</SelectItem>
                                    <SelectItem
                                        v-for="sala in salas"
                                        :key="sala.id"
                                        :value="String(sala.id)"
                                        class="text-xs"
                                    >
                                        {{ sala.nombre }}
                                    </SelectItem>
                                </SelectContent>
                            </Select>
                        </div>

                        <!-- 3. Ponencia / Área -->
                        <div class="space-y-1">
                            <Label class="text-[11px]">
                                Ponencia / Área
                                <span v-if="cargandoAreas" class="text-[10px] text-muted-foreground">(Cargando...)</span>
                                <span v-if="esPresidente" class="text-[10px] text-muted-foreground">(No aplica)</span>
                            </Label>
                            <Select
                                :model-value="form.area_id ? String(form.area_id) : 'NINGUNA'"
                                :disabled="!form.sala_id || esPresidente || cargandoAreas"
                                @update:model-value="onAreaChange"
                            >
                                <SelectTrigger class="h-8 text-xs">
                                    <SelectValue :placeholder="esPresidente ? 'Área Completa' : '-- Seleccione Área --'" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="NINGUNA" class="text-xs">
                                        {{ esPresidente ? 'Área Completa' : '-- Seleccione Área --' }}
                                    </SelectItem>
                                    <SelectItem
                                        v-for="area in areasFiltradas"
                                        :key="area.id"
                                        :value="String(area.id)"
                                        class="text-xs"
                                    >
                                        {{ area.nombre }} {{ area.numero ? `(Ponencia ${area.numero})` : '' }}
                                    </SelectItem>
                                </SelectContent>
                            </Select>
                        </div>
                    </div>
                </div>

                <!-- Checkbox Activo -->
                <div class="flex items-center space-x-2 pt-1">
                    <Checkbox
                        id="form-activo"
                        :checked="form.activo"
                        @update:checked="onActivoChange"
                    />
                    <Label for="form-activo" class="text-xs cursor-pointer">
                        Servidor público habilitado para operar en el sistema
                    </Label>
                </div>

                <DialogFooter class="pt-3 border-t border-border mt-4">
                    <Button
                        type="button"
                        variant="outline"
                        size="sm"
                        class="text-xs"
                        @click="emit('update:open', false)"
                    >
                        Cancelar
                    </Button>

                    <Button
                        type="submit"
                        size="sm"
                        class="text-xs"
                        :disabled="form.processing"
                    >
                        {{ form.processing ? 'Guardando...' : (usuario ? 'Actualizar Usuario' : 'Registrar Servidor') }}
                    </Button>
                </DialogFooter>
            </form>
        </DialogContent>
    </Dialog>
</template>