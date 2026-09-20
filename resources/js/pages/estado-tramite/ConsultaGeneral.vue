<!-- eslint-disable import/order -->
<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import {
    Calendar,
    Eye,
    FileSearch,
    FileText,
    RotateCcw,
    Search,
    UserCheck,
} from '@lucide/vue';
import { ref } from 'vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
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
import { toUrl } from '@/lib/utils';
import estadoTramiteRoutes from '@/routes/estado-tramite';
import type {
    ConsultaGeneralProps,
    EstatusTramite,
    TipoTramite,
    Tramite,
} from '../../types/tramites';

const props = defineProps<ConsultaGeneralProps>();

const buscar = ref(props.filtros?.buscar ?? '');
const tipo = ref<string>(props.filtros?.tipo ?? 'TODOS');
const estatus = ref<string>(props.filtros?.estatus ?? 'TODOS');

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

const badgeVariantPorTipo = (t: TipoTramite) => {
    switch (t) {
        case 'ALTA':
            return 'default';
        case 'BAJA':
            return 'destructive';
        case 'PROMOCION':
            return 'approved';
        case 'DEMOCION':
            return 'review';
        default:
            return 'secondary';
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

const formatearEtiquetaPaginacion = (label: string): string => {
    if (label.includes('&laquo;') || label.toLowerCase().includes('previous') || label.toLowerCase().includes('anterior')) {
        return '«';
    }

    if (label.includes('&raquo;') || label.toLowerCase().includes('next') || label.toLowerCase().includes('siguiente')) {
        return '»';
    }

    return label;
};

const aplicarFiltros = () => {
    const params: Record<string, string> = {};

    if (buscar.value.trim()) {
        params.buscar = buscar.value.trim();
    }

    if (tipo.value && tipo.value !== 'TODOS') {
        params.tipo = tipo.value;
    }

    if (estatus.value && estatus.value !== 'TODOS') {
        params.estatus = estatus.value;
    }

    router.get(
        toUrl(estadoTramiteRoutes.consulta()),
        params,
        {
            preserveState: true,
            replace: true,
        },
    );
};

const limpiarFiltros = () => {
    buscar.value = '';
    tipo.value = 'TODOS';
    estatus.value = 'TODOS';

    router.get(
        toUrl(estadoTramiteRoutes.consulta()),
        {},
        {
            preserveState: true,
            replace: true,
        },
    );
};
</script>

<template>
    <Head title="Consulta General de Trámites - TFJA" />

    <Card>
        <CardHeader class="border-b">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                <div>
                    <CardTitle>Padrón Global de Trámites</CardTitle>
                    <CardDescription>
                        Seguimiento general de solicitudes de personal, movimientos presupuestales y estatus del circuito.
                    </CardDescription>
                </div>

                <Badge variant="outline" class="w-fit text-xs font-mono">
                    {{ props.tramites?.total ?? 0 }} registros
                </Badge>
            </div>
        </CardHeader>

        <CardContent class="pt-4 space-y-4">
            <!-- Barra de Filtros Reactivos -->
            <div class="p-3 bg-slate-50 border border-slate-200 grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
                <div class="space-y-1.5 sm:col-span-5 text-xs">
                    <Label for="filtro-buscar-tramite" class="font-bold text-slate-700 uppercase">
                        Búsqueda General
                    </Label>
                    <Input
                        id="filtro-buscar-tramite"
                        v-model="buscar"
                        type="search"
                        placeholder="Buscar por folio, servidor público, CURP o RFC..."
                        class="bg-white"
                        @keydown.enter="aplicarFiltros"
                    />
                </div>

                <div class="space-y-1.5 sm:col-span-2 text-xs">
                    <Label class="font-bold text-slate-700 uppercase">Tipo</Label>
                    <Select v-model="tipo">
                        <SelectTrigger size="sm" class="w-full bg-white">
                            <SelectValue />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="TODOS">Todos los tipos</SelectItem>
                            <SelectItem value="ALTA">Alta</SelectItem>
                            <SelectItem value="BAJA">Baja</SelectItem>
                            <SelectItem value="PROMOCION">Promoción</SelectItem>
                            <SelectItem value="DEMOCION">Democión</SelectItem>
                        </SelectContent>
                    </Select>
                </div>

                <div class="space-y-1.5 sm:col-span-3 text-xs">
                    <Label class="font-bold text-slate-700 uppercase">Estatus del Circuito</Label>
                    <Select v-model="estatus">
                        <SelectTrigger size="sm" class="w-full bg-white">
                            <SelectValue />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="TODOS">Todos los estatus</SelectItem>
                            <SelectItem value="PENDIENTE">Pendiente de Firma</SelectItem>
                            <SelectItem value="EN_REVISION">En Revisión</SelectItem>
                            <SelectItem value="AUTORIZADO">Autorizado</SelectItem>
                            <SelectItem value="DEVUELTO">Devuelto con Observaciones</SelectItem>
                            <SelectItem value="CONCLUIDO">Concluido / Aplicado</SelectItem>
                        </SelectContent>
                    </Select>
                </div>

                <div class="flex items-center gap-2 sm:col-span-2">
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

            <!-- Tabla de Trámites -->
            <div class="border border-slate-300 overflow-x-auto">
                <table class="w-full text-xs text-left border-collapse">
                    <thead>
                        <tr class="border-b border-slate-300 bg-slate-100 text-slate-700 font-bold uppercase">
                            <th class="p-3">Folio y Trámite</th>
                            <th class="p-3">Servidor Propuesto</th>
                            <th class="p-3">Plaza y Adscripción</th>
                            <th class="p-3">Fecha de Efectos</th>
                            <th class="p-3 text-center">Estatus</th>
                            <th class="p-3 text-right">Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="tramite in props.tramites?.data ?? []"
                            :key="tramite.id"
                            class="border-b border-slate-200 hover:bg-slate-50 transition-colors"
                        >
                            <!-- Folio y Tipo -->
                            <td class="p-3">
                                <div class="font-mono font-bold text-slate-900">
                                    {{ tramite.folio }}
                                </div>
                                <div class="pt-1">
                                    <Badge :variant="badgeVariantPorTipo(tramite.tipo)" class="text-[10px] px-1.5 py-0">
                                        {{ tramite.tipo }}
                                    </Badge>
                                </div>
                            </td>

                            <!-- Servidor Propuesto -->
                            <td class="p-3">
                                <div class="font-semibold text-slate-800">
                                    {{ tramite.nombre_completo ?? `${tramite.nombre} ${tramite.primer_apellido} ${tramite.segundo_apellido ?? ''}`.trim() }}
                                </div>
                                <div class="font-mono text-[10px] text-slate-500 mt-0.5">
                                    CURP: {{ tramite.curp }}
                                </div>
                            </td>

                            <!-- Plaza Destino / Adscripción -->
                            <td class="p-3 text-slate-600">
                                <div v-if="tramite.plaza_destino" class="space-y-0.5">
                                    <span class="font-medium text-slate-800 block">
                                        {{ tramite.plaza_destino.puesto }}
                                    </span>
                                    <span class="text-[11px] text-slate-500 block truncate">
                                        {{ tramite.plaza_destino.unidad?.nombre ?? tramite.plaza_destino.adscripcion ?? 'Sin adscripción' }}
                                    </span>
                                    <Badge variant="outline" class="font-mono text-[10px] px-1 py-0 bg-white text-slate-600">
                                        Plaza {{ tramite.plaza_destino.numero_plaza }}
                                    </Badge>
                                </div>
                                <span v-else class="text-slate-400 italic">No especificada</span>
                            </td>

                            <!-- Fecha de Efectos -->
                            <td class="p-3 font-mono text-slate-700">
                                <div class="flex items-center gap-1.5">
                                    <Calendar class="size-3.5 text-slate-400 shrink-0" />
                                    <span>{{ formatearFecha(tramite.fecha_efectos_propuesta) }}</span>
                                </div>
                            </td>

                            <!-- Estatus -->
                            <td class="p-3 text-center">
                                <Badge :variant="badgeVariantPorEstatus(tramite.estatus)">
                                    {{ tramite.estatus }}
                                </Badge>
                                <div v-if="tramite.total_firmas" class="text-[10px] text-slate-400 mt-1 font-mono">
                                    Firmas: {{ tramite.firmas_completadas ?? 0 }} / {{ tramite.total_firmas }}
                                </div>
                            </td>

                            <!-- Acción: Ver Expediente -->
                            <td class="p-3 text-right">
                                <Button
                                    as-child
                                    size="sm"
                                    variant="outline"
                                    class="h-7 text-xs gap-1.5"
                                >
                                    <Link :href="toUrl(estadoTramiteRoutes.show({ tramite: tramite.id }))">
                                        <Eye class="size-3.5" />
                                        <span>Expediente</span>
                                    </Link>
                                </Button>
                            </td>
                        </tr>

                        <!-- Sin Registros -->
                        <tr v-if="!props.tramites?.data || props.tramites.data.length === 0">
                            <td colspan="6" class="p-8 text-center text-slate-500">
                                <FileSearch class="size-8 mx-auto text-slate-300 mb-2" />
                                <p class="font-semibold">No se encontraron trámites registrados</p>
                                <p class="text-[11px] text-slate-400 mt-1">
                                    Verifica los filtros seleccionados o realiza una búsqueda con otros términos.
                                </p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            <div
                v-if="props.tramites"
                class="flex flex-col sm:flex-row items-center justify-between gap-3 text-[11px] text-slate-500 pt-2"
            >
                <span>
                    Mostrando {{ props.tramites.from ?? 0 }} a {{ props.tramites.to ?? 0 }} de {{ props.tramites.total }} trámites
                </span>

                <div v-if="props.tramites.last_page > 1" class="flex flex-wrap items-center gap-1">
                    <template v-for="(link, index) in props.tramites.links" :key="index">
                        <Button
                            v-if="!link.url"
                            size="sm"
                            variant="outline"
                            class="h-7 min-w-7 px-2 text-xs opacity-40 cursor-not-allowed"
                            disabled
                        >
                            <span>{{ formatearEtiquetaPaginacion(link.label) }}</span>
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
                                {{ formatearEtiquetaPaginacion(link.label) }}
                            </Link>
                        </Button>
                    </template>
                </div>
            </div>
        </CardContent>
    </Card>
</template>
