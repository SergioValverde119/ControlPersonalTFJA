<!-- eslint-disable import/order -->
<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import {
    Briefcase,
    Building2,
    RotateCcw,
    Search,
    UserPlus,
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
import { toUrl } from '@/lib/utils';
import nuevoTramiteRoutes from '@/routes/nuevo-tramite';
import type { Plaza, PlazasDisponiblesProps } from '@/types/tramites';

const props = defineProps<PlazasDisponiblesProps>();

const buscar = ref(props.filtros?.buscar ?? '');

const formatearMoneda = (cantidad?: number | null): string => {
    if (!cantidad) {
        return '—';
    }

    return new Intl.NumberFormat('es-MX', {
        style: 'currency',
        currency: 'MXN',
    }).format(cantidad);
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

    router.get(
        toUrl(nuevoTramiteRoutes.plazas()),
        params,
        {
            preserveState: true,
            replace: true,
        },
    );
};

const limpiarFiltros = () => {
    buscar.value = '';

    router.get(
        toUrl(nuevoTramiteRoutes.plazas()),
        {},
        {
            preserveState: true,
            replace: true,
        },
    );
};

const iniciarPostulacion = (plaza: Plaza) => {
    router.get(
        toUrl(
            nuevoTramiteRoutes.alta({
                query: {
                    plaza_destino_id: plaza.id,
                },
            }),
        ),
    );
};
</script>

<template>
    <Head title="Plazas Disponibles - Nuevo Trámite TFJA" />

    <Card>
        <CardHeader class="border-b">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                <div>
                    <CardTitle>Plazas Autorizadas Disponibles</CardTitle>
                    <CardDescription>
                        Catálogo de vacantes presupuestales activas listas para postulación de personal y apertura de expediente.
                    </CardDescription>
                </div>

                <Badge variant="outline" class="w-fit text-xs font-mono">
                    {{ props.plazas?.total ?? 0 }} vacantes encontradas
                </Badge>
            </div>
        </CardHeader>

        <CardContent class="pt-4 space-y-4">
            <!-- Filtros de Búsqueda -->
            <div class="p-3 bg-slate-50 border border-slate-200 flex flex-col sm:flex-row gap-3 items-end">
                <div class="space-y-1.5 flex-1 text-xs">
                    <Label for="filtro-buscar-plazas" class="font-bold text-slate-700 uppercase">
                        Búsqueda de Vacante
                    </Label>
                    <Input
                        id="filtro-buscar-plazas"
                        v-model="buscar"
                        type="search"
                        placeholder="Buscar por puesto, número de plaza o unidad..."
                        class="bg-white"
                        @keydown.enter="aplicarFiltros"
                    />
                </div>

                <div class="flex items-center gap-2">
                    <Button
                        size="sm"
                        variant="default"
                        class="gap-1.5"
                        @click="aplicarFiltros"
                    >
                        <Search class="size-3.5" />
                        <span>Buscar</span>
                    </Button>
                    <Button
                        size="icon-sm"
                        variant="outline"
                        title="Limpiar Búsqueda"
                        @click="limpiarFiltros"
                    >
                        <RotateCcw class="size-3.5 text-slate-600" />
                    </Button>
                </div>
            </div>

            <!-- Tabla de Plazas Vacantes -->
            <div class="border border-slate-300 overflow-x-auto">
                <table class="w-full text-xs text-left border-collapse">
                    <thead>
                        <tr class="border-b border-slate-300 bg-slate-100 text-slate-700 font-bold uppercase">
                            <th class="p-3">Número de Plaza</th>
                            <th class="p-3">Denominación del Puesto</th>
                            <th class="p-3">Nivel / Rango</th>
                            <th class="p-3">Adscripción</th>
                            <th class="p-3 text-right">Percepción Bruta</th>
                            <th class="p-3 text-center">Estatus</th>
                            <th class="p-3 text-right">Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="plaza in props.plazas?.data ?? []"
                            :key="plaza.id"
                            class="border-b border-slate-200 hover:bg-slate-50 transition-colors"
                        >
                            <td class="p-3 font-mono font-semibold text-slate-800">
                                {{ plaza.numero_plaza }}
                            </td>
                            <td class="p-3 font-semibold text-slate-800">
                                {{ plaza.puesto }}
                            </td>
                            <td class="p-3">
                                <Badge variant="outline" class="font-mono text-[11px] px-1.5 py-0 bg-white text-slate-700 border-slate-300">
                                    {{ plaza.nivel }}
                                </Badge>
                            </td>
                            <td class="p-3 text-slate-600">
                                <div class="flex items-start gap-1.5">
                                    <Building2 class="size-3.5 text-slate-400 mt-0.5 shrink-0" />
                                    <span>{{ plaza.unidad?.nombre ?? plaza.adscripcion ?? 'Por asignar' }}</span>
                                </div>
                            </td>
                            <td class="p-3 text-right font-mono text-slate-700">
                                {{ formatearMoneda(plaza.remuneracion_bruta) }}
                            </td>
                            <td class="p-3 text-center">
                                <Badge :variant="plaza.estatus === 'VACANTE' ? 'approved' : 'review'">
                                    {{ plaza.estatus }}
                                </Badge>
                            </td>
                            <td class="p-3 text-right">
                                <Button
                                    size="sm"
                                    variant="default"
                                    class="gap-1.5 h-7 text-xs"
                                    @click="iniciarPostulacion(plaza)"
                                >
                                    <UserPlus class="size-3.5" />
                                    <span>Postular</span>
                                </Button>
                            </td>
                        </tr>

                        <tr v-if="!props.plazas?.data || props.plazas.data.length === 0">
                            <td colspan="7" class="p-8 text-center text-slate-500">
                                <Briefcase class="size-8 mx-auto text-slate-300 mb-2" />
                                <p class="font-semibold">No se encontraron plazas disponibles</p>
                                <p class="text-[11px] text-slate-400 mt-1">
                                    Prueba ajustando los términos de búsqueda o limpiando los filtros.
                                </p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            <div
                v-if="props.plazas"
                class="flex flex-col sm:flex-row items-center justify-between gap-3 text-[11px] text-slate-500 pt-2"
            >
                <span>
                    Mostrando {{ props.plazas.from ?? 0 }} a {{ props.plazas.to ?? 0 }} de {{ props.plazas.total }} registros
                </span>

                <div v-if="props.plazas.last_page > 1" class="flex flex-wrap items-center gap-1">
                    <template v-for="(link, index) in props.plazas.links" :key="index">
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
