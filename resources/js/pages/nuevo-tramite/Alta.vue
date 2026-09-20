<!-- eslint-disable import/order -->
<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import {
    AlertCircle,
    ArrowLeft,
    Send,
} from '@lucide/vue';
import { computed } from 'vue';
import PdfFileInput from '@/components/custom/PdfFileInput.vue';
import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardFooter,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { Separator } from '@/components/ui/separator';
import { toUrl } from '@/lib/utils';
import nuevoTramiteRoutes from '@/routes/nuevo-tramite';
import type { AltaFormPayload, AltaProps } from '../../types/tramites';

const props = defineProps<AltaProps>();

const form = useForm<AltaFormPayload>({
    plaza_destino_id: props.plaza_destino_id ? Number(props.plaza_destino_id) : '',
    fecha_efectos_propuesta: '',
    curp: '',
    rfc: '',
    nombre: '',
    primer_apellido: '',
    segundo_apellido: '',
    correo_contacto: '',
    telefono_contacto: '',
    oficio_propuesta: null,
    curriculum_vitae: null,
});

const plazaSeleccionada = computed(() => {
    if (!form.plaza_destino_id) {
        return null;
    }

    return props.plazas_disponibles?.find(
        (p) => p.id === Number(form.plaza_destino_id),
    ) ?? null;
});

const submit = () => {
    form.transform((data) => {
        return {
            ...data,
            plaza_destino_id: Number(data.plaza_destino_id),
            curp: data.curp.trim().toUpperCase(),
            rfc: data.rfc.trim().toUpperCase(),
            nombre: data.nombre.trim(),
            primer_apellido: data.primer_apellido.trim(),
            segundo_apellido: data.segundo_apellido ? data.segundo_apellido.trim() : '',
            correo_contacto: data.correo_contacto ? data.correo_contacto.trim().toLowerCase() : '',
            telefono_contacto: data.telefono_contacto ? data.telefono_contacto.trim() : '',
        };
    }).post(toUrl(nuevoTramiteRoutes.alta.store()), {
        forceFormData: true,
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="Registro de Propuesta de Alta - TFJA" />

    <form @submit.prevent="submit">
        <Card>
            <CardHeader class="border-b">
                <div class="flex items-center justify-between">
                    <div>
                        <CardTitle>Registro de Propuesta de Alta</CardTitle>
                        <CardDescription>
                            Apertura de trámite institucional para postulación de personal a plaza vacante.
                        </CardDescription>
                    </div>

                    <Button
                        as-child
                        variant="outline"
                        size="sm"
                        class="gap-1.5"
                    >
                        <Link :href="toUrl(nuevoTramiteRoutes.plazas())">
                            <ArrowLeft class="size-3.5" />
                            <span>Ver Plazas</span>
                        </Link>
                    </Button>
                </div>
            </CardHeader>

            <CardContent class="pt-5 space-y-6">
                <!-- Aviso Normativo -->
                <Alert class="bg-slate-50 border-slate-200 text-slate-800">
                    <AlertCircle class="size-4 text-slate-600" />
                    <AlertTitle class="text-xs font-bold uppercase tracking-wide">
                        Requisitos de Postulación
                    </AlertTitle>
                    <AlertDescription class="text-xs text-slate-600 mt-1">
                        Asegúrese de contar con el oficio de propuesta formal debidamente rubricado y el currículum vítae actualizado en formato PDF.
                    </AlertDescription>
                </Alert>

                <!-- Bloque 1: Plaza y Temporalidad -->
                <div class="space-y-3">
                    <span class="text-xs font-bold text-slate-900 uppercase tracking-wider block">
                        1. Plaza Presupuestal y Fecha de Efectos
                    </span>
                    <Separator />

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="space-y-1.5 text-xs">
                            <Label for="plaza-destino" class="font-bold text-slate-700 uppercase">
                                Plaza Vacante Destino <span class="text-red-600">*</span>
                            </Label>
                            <Select
                                v-model="form.plaza_destino_id"
                                :disabled="form.processing"
                            >
                                <SelectTrigger id="plaza-destino" class="w-full bg-white" :aria-invalid="!!form.errors.plaza_destino_id">
                                    <SelectValue placeholder="Seleccione la plaza a ocupar..." />
                                </SelectTrigger>
                                <SelectContent class="max-h-60">
                                    <SelectItem
                                        v-for="plaza in props.plazas_disponibles ?? []"
                                        :key="plaza.id"
                                        :value="String(plaza.id)"
                                    >
                                        {{ plaza.numero_plaza }} - {{ plaza.puesto }} ({{ plaza.nivel }})
                                    </SelectItem>
                                </SelectContent>
                            </Select>
                            <span v-if="form.errors.plaza_destino_id" class="text-[11px] text-red-600 block">
                                {{ form.errors.plaza_destino_id }}
                            </span>
                        </div>

                        <div class="space-y-1.5 text-xs">
                            <Label for="fecha-efectos" class="font-bold text-slate-700 uppercase">
                                Fecha de Efectos Propuesta <span class="text-red-600">*</span>
                            </Label>
                            <Input
                                id="fecha-efectos"
                                v-model="form.fecha_efectos_propuesta"
                                type="date"
                                class="bg-white font-mono"
                                :disabled="form.processing"
                                :aria-invalid="!!form.errors.fecha_efectos_propuesta"
                                required
                            />
                            <span v-if="form.errors.fecha_efectos_propuesta" class="text-[11px] text-red-600 block">
                                {{ form.errors.fecha_efectos_propuesta }}
                            </span>
                        </div>
                    </div>

                    <div
                        v-if="plazaSeleccionada"
                        class="p-3 bg-slate-50 border border-slate-200 rounded grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs"
                    >
                        <div>
                            <span class="text-[10px] text-slate-400 font-bold uppercase block">Puesto</span>
                            <span class="font-semibold text-slate-800">{{ plazaSeleccionada.puesto }}</span>
                        </div>
                        <div>
                            <span class="text-[10px] text-slate-400 font-bold uppercase block">Adscripción</span>
                            <span class="text-slate-700">{{ plazaSeleccionada.unidad?.nombre ?? plazaSeleccionada.adscripcion ?? 'Por asignar' }}</span>
                        </div>
                        <div>
                            <span class="text-[10px] text-slate-400 font-bold uppercase block">Nivel / Rango</span>
                            <Badge variant="outline" class="font-mono text-[10px] bg-white">
                                {{ plazaSeleccionada.nivel }}
                            </Badge>
                        </div>
                    </div>
                </div>

                <!-- Bloque 2: Identidad Civil -->
                <div class="space-y-3">
                    <span class="text-xs font-bold text-slate-900 uppercase tracking-wider block">
                        2. Identidad Civil del Servidor Propuesto
                    </span>
                    <Separator />

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="space-y-1.5 text-xs">
                            <Label for="candidato-curp" class="font-bold text-slate-700 uppercase">
                                CURP <span class="text-red-600">*</span>
                            </Label>
                            <Input
                                id="candidato-curp"
                                v-model="form.curp"
                                type="text"
                                maxlength="18"
                                class="uppercase font-mono bg-white"
                                placeholder="18 posiciones"
                                :disabled="form.processing"
                                :aria-invalid="!!form.errors.curp"
                                required
                            />
                            <span v-if="form.errors.curp" class="text-[11px] text-red-600 block">
                                {{ form.errors.curp }}
                            </span>
                        </div>

                        <div class="space-y-1.5 text-xs">
                            <Label for="candidato-rfc" class="font-bold text-slate-700 uppercase">
                                RFC <span class="text-red-600">*</span>
                            </Label>
                            <Input
                                id="candidato-rfc"
                                v-model="form.rfc"
                                type="text"
                                maxlength="13"
                                class="uppercase font-mono bg-white"
                                placeholder="12 o 13 posiciones con homoclave"
                                :disabled="form.processing"
                                :aria-invalid="!!form.errors.rfc"
                                required
                            />
                            <span v-if="form.errors.rfc" class="text-[11px] text-red-600 block">
                                {{ form.errors.rfc }}
                            </span>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div class="space-y-1.5 text-xs">
                            <Label for="candidato-nombre" class="font-bold text-slate-700 uppercase">
                                Nombre(s) <span class="text-red-600">*</span>
                            </Label>
                            <Input
                                id="candidato-nombre"
                                v-model="form.nombre"
                                type="text"
                                placeholder="Nombre de pila"
                                class="bg-white"
                                :disabled="form.processing"
                                :aria-invalid="!!form.errors.nombre"
                                required
                            />
                            <span v-if="form.errors.nombre" class="text-[11px] text-red-600 block">
                                {{ form.errors.nombre }}
                            </span>
                        </div>

                        <div class="space-y-1.5 text-xs">
                            <Label for="candidato-primer-apellido" class="font-bold text-slate-700 uppercase">
                                Primer Apellido <span class="text-red-600">*</span>
                            </Label>
                            <Input
                                id="candidato-primer-apellido"
                                v-model="form.primer_apellido"
                                type="text"
                                placeholder="Apellido paterno"
                                class="bg-white"
                                :disabled="form.processing"
                                :aria-invalid="!!form.errors.primer_apellido"
                                required
                            />
                            <span v-if="form.errors.primer_apellido" class="text-[11px] text-red-600 block">
                                {{ form.errors.primer_apellido }}
                            </span>
                        </div>

                        <div class="space-y-1.5 text-xs">
                            <Label for="candidato-segundo-apellido" class="font-bold text-slate-700 uppercase">
                                Segundo Apellido <span class="text-slate-400 font-normal lowercase">(opcional)</span>
                            </Label>
                            <Input
                                id="candidato-segundo-apellido"
                                v-model="form.segundo_apellido"
                                type="text"
                                placeholder="Apellido materno"
                                class="bg-white"
                                :disabled="form.processing"
                            />
                        </div>
                    </div>
                </div>

                <!-- Bloque 3: Contacto -->
                <div class="space-y-3">
                    <span class="text-xs font-bold text-slate-900 uppercase tracking-wider block">
                        3. Datos de Contacto
                    </span>
                    <Separator />

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="space-y-1.5 text-xs">
                            <Label for="contacto-correo" class="font-bold text-slate-700 uppercase">
                                Correo Electrónico de Contacto
                            </Label>
                            <Input
                                id="contacto-correo"
                                v-model="form.correo_contacto"
                                type="email"
                                placeholder="candidato@correo.com"
                                class="bg-white font-mono"
                                :disabled="form.processing"
                                :aria-invalid="!!form.errors.correo_contacto"
                            />
                            <span v-if="form.errors.correo_contacto" class="text-[11px] text-red-600 block">
                                {{ form.errors.correo_contacto }}
                            </span>
                        </div>

                        <div class="space-y-1.5 text-xs">
                            <Label for="contacto-telefono" class="font-bold text-slate-700 uppercase">
                                Teléfono de Contacto
                            </Label>
                            <Input
                                id="contacto-telefono"
                                v-model="form.telefono_contacto"
                                type="tel"
                                placeholder="10 dígitos"
                                class="bg-white font-mono"
                                :disabled="form.processing"
                                :aria-invalid="!!form.errors.telefono_contacto"
                            />
                            <span v-if="form.errors.telefono_contacto" class="text-[11px] text-red-600 block">
                                {{ form.errors.telefono_contacto }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Bloque 4: PDFs -->
                <div class="space-y-3">
                    <span class="text-xs font-bold text-slate-900 uppercase tracking-wider block">
                        4. Expediente Digital Transaccional (PDF Obligatorio)
                    </span>
                    <Separator />

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <PdfFileInput
                            id="archivo-oficio"
                            v-model="form.oficio_propuesta"
                            label="Oficio de Propuesta"
                            descripcion="Documento firmado en PDF"
                            :error="form.errors.oficio_propuesta"
                            :disabled="form.processing"
                            required
                        />

                        <PdfFileInput
                            id="archivo-cv"
                            v-model="form.curriculum_vitae"
                            label="Currículum Vítae"
                            descripcion="Síntesis curricular probatoria en PDF"
                            :error="form.errors.curriculum_vitae"
                            :disabled="form.processing"
                            required
                        />
                    </div>
                </div>
            </CardContent>

            <CardFooter class="border-t bg-slate-50/50 flex items-center justify-between gap-3 pt-4">
                <Button
                    as-child
                    type="button"
                    variant="outline"
                    size="sm"
                    :disabled="form.processing"
                >
                    <Link :href="toUrl(nuevoTramiteRoutes.plazas())">
                        Cancelar
                    </Link>
                </Button>

                <Button
                    type="submit"
                    variant="default"
                    size="sm"
                    class="gap-1.5"
                    :disabled="form.processing"
                >
                    <Send class="size-3.5" />
                    <span>{{ form.processing ? 'Registrando Trámite...' : 'Iniciar Circuito de Alta' }}</span>
                </Button>
            </CardFooter>
        </Card>
    </form>
</template>
