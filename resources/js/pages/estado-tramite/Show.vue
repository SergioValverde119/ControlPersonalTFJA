<!-- eslint-disable import/order -->
<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import {
    AlertCircle,
    ArrowLeft,
    Building2,
    Calendar,
    CheckCircle2,
    Clock,
    Download,
    ExternalLink,
    FileCheck,
    FileText,
    KeyRound,
    Mail,
    Phone,
    ShieldAlert,
    User,
    XCircle,
} from '@lucide/vue';
import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Separator } from '@/components/ui/separator';
import { toUrl } from '@/lib/utils';
import documentosTramiteRoutes from '@/routes/documentos-tramite';
import estadoTramiteRoutes from '@/routes/estado-tramite';
import type {
    DocumentoTramite,
    EstadoTramiteShowProps,
    EstatusFirma,
    EstatusTramite,
    FirmaPaso,
} from '../../types/tramites';

const props = defineProps<EstadoTramiteShowProps>();

const badgeVariantPorEstatus = (estado: EstatusTramite) => {
    switch (estado) {
        case 'AUTORIZADO':
        case 'CONCLUIDO':
            return 'approved';
        case 'RECHAZADO':
        case 'DEVUELTO':
            return 'rejected';
        case 'PENDIENTE':
        case 'EN_REVISION':
            return 'review';
        default:
            return 'outline';
    }
};

const badgeVariantPorFirma = (estatus: EstatusFirma) => {
    switch (estatus) {
        case 'AUTORIZADO':
        case 'VOBO':
            return 'approved';
        case 'DEVUELTO':
            return 'rejected';
        case 'PENDIENTE':
            return 'review';
        default:
            return 'outline';
    }
};

const formatearFecha = (fechaStr?: string | null): string => {
    if (!fechaStr) {
        return '—';
    }

    try {
        const d = new Date(fechaStr.includes('T') ? fechaStr : `${fechaStr}T00:00:00`);

        return new Intl.DateTimeFormat('es-MX', {
            year: 'numeric',
            month: 'short',
            day: '2-digit',
        }).format(d);
    } catch {
        return fechaStr;
    }
};

const formatearTamano = (bytes?: number): string => {
    if (!bytes || bytes === 0) {
        return '—';
    }

    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));

    return `${parseFloat((bytes / Math.pow(k, i)).toFixed(1))} ${sizes[i]}`;
};

const etiquetaDocumento = (tipo: DocumentoTramite['tipo_documento']): string => {
    switch (tipo) {
        case 'OFICIO_PROPUESTA':
            return 'Oficio de Propuesta Formal';
        case 'CURRICULUM_VITAE':
            return 'Currículum Vítae Probatorio';
        default:
            return 'Documento Complementario';
    }
};
</script>

<template>
    <Head :title="`Expediente ${props.tramite.folio} - TFJA`" />

    <div class="space-y-6">
        <!-- Encabezado de la Ficha -->
        <Card>
            <CardHeader class="border-b bg-slate-50/50">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div class="space-y-1">
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-bold text-slate-500 uppercase tracking-wide">
                                Expediente Digital
                            </span>
                            <Badge variant="outline" class="font-mono text-xs px-2 bg-white">
                                {{ props.tramite.folio }}
                            </Badge>
                            <Badge :variant="badgeVariantPorEstatus(props.tramite.estatus)">
                                {{ props.tramite.estatus }}
                            </Badge>
                        </div>
                        <CardTitle class="text-lg">
                            Movimiento de {{ props.tramite.tipo }} de Personal
                        </CardTitle>
                        <CardDescription>
                            Registrado el {{ formatearFecha(props.tramite.created_at) }} por {{ props.tramite.solicitante?.name ?? 'Personal Solicitante' }}.
                        </CardDescription>
                    </div>

                    <Button
                        as-child
                        variant="outline"
                        size="sm"
                        class="gap-1.5 w-fit"
                    >
                        <Link :href="toUrl(estadoTramiteRoutes.consulta())">
                            <ArrowLeft class="size-3.5" />
                            <span>Bandeja General</span>
                        </Link>
                    </Button>
                </div>
            </CardHeader>

            <CardContent class="pt-5 space-y-6">
                <!-- Resumen en 3 Columnas: Servidor, Plaza y Fechas -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-xs">
                    <!-- Servidor Propuesto -->
                    <div class="p-3 bg-slate-50 border border-slate-200 rounded space-y-2">
                        <span class="font-bold text-slate-700 uppercase tracking-wider block border-b border-slate-200 pb-1 flex items-center gap-1.5">
                            <User class="size-3.5 text-slate-500" />
                            Servidor Propuesto
                        </span>
                        <div class="space-y-1">
                            <p class="font-semibold text-slate-900 text-sm">
                                {{ props.tramite.nombre_completo ?? `${props.tramite.nombre} ${props.tramite.primer_apellido} ${props.tramite.segundo_apellido ?? ''}`.trim() }}
                            </p>
                            <p class="font-mono text-slate-600">CURP: {{ props.tramite.curp }}</p>
                            <p class="font-mono text-slate-500">RFC: {{ props.tramite.rfc }}</p>
                            <div v-if="props.tramite.correo_contacto" class="flex items-center gap-1.5 text-slate-600 pt-1">
                                <Mail class="size-3 text-slate-400" />
                                <span class="font-mono truncate">{{ props.tramite.correo_contacto }}</span>
                            </div>
                            <div v-if="props.tramite.telefono_contacto" class="flex items-center gap-1.5 text-slate-600">
                                <Phone class="size-3 text-slate-400" />
                                <span class="font-mono">{{ props.tramite.telefono_contacto }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Plaza Destino -->
                    <div class="p-3 bg-slate-50 border border-slate-200 rounded space-y-2">
                        <span class="font-bold text-slate-700 uppercase tracking-wider block border-b border-slate-200 pb-1 flex items-center gap-1.5">
                            <Building2 class="size-3.5 text-slate-500" />
                            Plaza Presupuestal
                        </span>
                        <div v-if="props.tramite.plaza_destino" class="space-y-1">
                            <p class="font-semibold text-slate-900">
                                {{ props.tramite.plaza_destino.puesto }}
                            </p>
                            <p class="text-slate-600">
                                {{ props.tramite.plaza_destino.unidad?.nombre ?? props.tramite.plaza_destino.adscripcion ?? 'Por asignar' }}
                            </p>
                            <div class="flex items-center gap-2 pt-1">
                                <Badge variant="outline" class="font-mono bg-white text-[10px]">
                                    Plaza: {{ props.tramite.plaza_destino.numero_plaza }}
                                </Badge>
                                <Badge variant="outline" class="font-mono bg-white text-[10px]">
                                    Nivel: {{ props.tramite.plaza_destino.nivel }}
                                </Badge>
                            </div>
                        </div>
                        <div v-else class="text-slate-400 italic py-2">
                            Sin plaza vinculada
                        </div>
                    </div>

                    <!-- Temporalidad y Efectos -->
                    <div class="p-3 bg-slate-50 border border-slate-200 rounded space-y-2">
                        <span class="font-bold text-slate-700 uppercase tracking-wider block border-b border-slate-200 pb-1 flex items-center gap-1.5">
                            <Calendar class="size-3.5 text-slate-500" />
                            Temporalidad
                        </span>
                        <div class="space-y-2 pt-0.5">
                            <div>
                                <span class="text-[10px] text-slate-400 uppercase block font-semibold">Fecha de Efectos Propuesta</span>
                                <span class="font-mono font-bold text-slate-800 text-sm">
                                    {{ formatearFecha(props.tramite.fecha_efectos_propuesta) }}
                                </span>
                            </div>
                            <div>
                                <span class="text-[10px] text-slate-400 uppercase block font-semibold">Última Actualización</span>
                                <span class="font-mono text-slate-600">
                                    {{ formatearFecha(props.tramite.updated_at) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sección: Escalera de Firmas y Trazabilidad -->
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-slate-900 uppercase tracking-wider block">
                            Escalera Institucional de Firmas
                        </span>
                        <span class="text-[11px] text-slate-500 font-mono">
                            Avance: {{ props.tramite.firmas_completadas ?? 0 }} de {{ props.tramite.total_firmas ?? (props.tramite.firmas?.length ?? 0) }} turnos
                        </span>
                    </div>
                    <Separator />

                    <div class="border border-slate-200 divide-y divide-slate-200 bg-white">
                        <div
                            v-for="firma in props.tramite.firmas ?? []"
                            :key="firma.id"
                            class="p-3 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs hover:bg-slate-50/70 transition-colors"
                        >
                            <div class="flex items-start gap-3 min-w-0">
                                <!-- Indicador de Orden / Turno -->
                                <div class="size-6 rounded-full bg-slate-100 border border-slate-300 flex items-center justify-center font-mono font-bold text-slate-700 shrink-0 text-[11px]">
                                    {{ firma.orden }}
                                </div>

                                <div class="space-y-0.5 min-w-0">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span class="font-bold text-slate-800">{{ firma.rol_requerido }}</span>
                                        <Badge
                                            v-if="firma.requiere_venia"
                                            variant="outline"
                                            class="text-[10px] px-1.5 py-0 bg-amber-50 text-amber-800 border-amber-300 flex items-center gap-1"
                                        >
                                            <KeyRound class="size-2.5" />
                                            <span>Requiere Venia</span>
                                        </Badge>
                                    </div>
                                    <p class="text-slate-600">
                                        {{ firma.usuario_firmante_nombre ?? 'Pendiente de asignación de turno' }}
                                    </p>
                                    <p v-if="firma.observaciones" class="text-[11px] text-red-700 bg-red-50 border border-red-200 p-1.5 rounded mt-1">
                                        <strong>Observación:</strong> {{ firma.observaciones }}
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-center gap-3 sm:text-right shrink-0">
                                <div class="text-[11px] text-slate-500 font-mono">
                                    {{ firma.fecha_firma ? formatearFecha(firma.fecha_firma) : 'Sin firmar' }}
                                </div>
                                <Badge :variant="badgeVariantPorFirma(firma.estatus)">
                                    {{ firma.estatus }}
                                </Badge>
                            </div>
                        </div>

                        <div v-if="!props.tramite.firmas || props.tramite.firmas.length === 0" class="p-4 text-center text-slate-400 italic text-xs">
                            No se ha inicializado la escalera de firmas para este trámite.
                        </div>
                    </div>
                </div>

                <!-- Sección: Expediente Documental Digital -->
                <div class="space-y-3">
                    <span class="text-xs font-bold text-slate-900 uppercase tracking-wider block">
                        Expediente Documental de Respaldo
                    </span>
                    <Separator />

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div
                            v-for="doc in props.tramite.documentos ?? []"
                            :key="doc.id"
                            class="p-3 border border-slate-200 rounded flex items-center justify-between gap-2 bg-slate-50/50 hover:bg-slate-50 transition-colors"
                        >
                            <div class="flex items-center gap-2.5 min-w-0 flex-1 text-xs">
                                <FileText class="size-5 text-red-600 shrink-0" />
                                <div class="min-w-0 flex-1">
                                    <span class="font-bold text-slate-800 block truncate">
                                        {{ etiquetaDocumento(doc.tipo_documento) }}
                                    </span>
                                    <span class="text-[10px] text-slate-400 font-mono block truncate">
                                        {{ doc.nombre_archivo }} ({{ formatearTamano(doc.tamano_bytes) }})
                                    </span>
                                </div>
                            </div>

                            <div class="flex items-center gap-1 shrink-0">
                                <Button
                                    as-child
                                    variant="outline"
                                    size="icon-sm"
                                    class="size-7 bg-white"
                                    title="Ver documento en nueva pestaña"
                                >
                                    <a
                                        :href="toUrl(documentosTramiteRoutes.show({ documento: doc.id }))"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                    >
                                        <ExternalLink class="size-3.5 text-slate-600" />
                                    </a>
                                </Button>
                                <Button
                                    as-child
                                    variant="outline"
                                    size="icon-sm"
                                    class="size-7 bg-white"
                                    title="Descargar archivo"
                                >
                                    <a :href="toUrl(documentosTramiteRoutes.download({ documento: doc.id }))">
                                        <Download class="size-3.5 text-slate-600" />
                                    </a>
                                </Button>
                            </div>
                        </div>

                        <div v-if="!props.tramite.documentos || props.tramite.documentos.length === 0" class="sm:col-span-2 p-4 text-center text-slate-400 italic text-xs border border-dashed border-slate-200 rounded">
                            Sin archivos anexos registrados en el expediente.
                        </div>
                    </div>
                </div>
            </CardContent>
        </Card>
    </div>
</template>
