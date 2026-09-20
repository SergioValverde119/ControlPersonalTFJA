<!-- eslint-disable import/order -->
<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import {
    Archive,
    Clock,
    FileSearch,
    FileX2,
} from '@lucide/vue';
import { computed } from 'vue';
import { Button } from '@/components/ui/button';
import { Separator } from '@/components/ui/separator';
import { toUrl } from '@/lib/utils';
import estadoTramiteRoutes from '@/routes/estado-tramite';

interface NavItem {
    label: string;
    descripcion: string;
    href: string;
    icon: typeof FileSearch;
    activo: boolean;
}

const page = usePage();

const navItems = computed<NavItem[]>(() => {
    const urlActual = page.url;

    return [
        {
            label: 'Consulta General',
            descripcion: 'Padrón global de solicitudes',
            href: toUrl(estadoTramiteRoutes.consulta()),
            icon: FileSearch,
            activo: urlActual.startsWith('/estado-tramite/consulta') || urlActual === '/estado-tramite',
        },
        {
            label: 'Trámites Pendientes',
            descripcion: 'En circuito activo de firmas',
            href: toUrl(estadoTramiteRoutes.pendientes()),
            icon: Clock,
            activo: urlActual.startsWith('/estado-tramite/pendientes'),
        },
        {
            label: 'Trámites Devueltos',
            descripcion: 'Con observaciones o rechazo',
            href: toUrl(estadoTramiteRoutes.rechazados()),
            icon: FileX2,
            activo: urlActual.startsWith('/estado-tramite/rechazados'),
        },
        {
            label: 'Histórico Concluido',
            descripcion: 'Trámites firmados y aplicados',
            href: toUrl(estadoTramiteRoutes.historico()),
            icon: Archive,
            activo: urlActual.startsWith('/estado-tramite/historico'),
        },
    ];
});
</script>

<template>
    <div class="space-y-6">
        <!-- Encabezado Institucional del Módulo -->
        <div class="border-b border-slate-200 pb-4">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-xl font-bold tracking-tight text-slate-900">
                        Estado y Seguimiento de Trámites
                    </h1>
                    <p class="text-xs text-slate-500 mt-1">
                        Auditoría de expedientes digitales, trazabilidad de solicitudes y estatus en el circuito de autorización.
                    </p>
                </div>
            </div>
        </div>

        <!-- Contenedor Principal: Menú Lateral + Slot de Trabajo -->
        <div class="flex flex-col md:flex-row gap-6 items-start">
            <aside class="w-full md:w-64 shrink-0 space-y-2 bg-slate-50/80 p-2.5 border border-slate-200">
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500 px-2 block">
                    Bandejas de Seguimiento
                </span>

                <Separator />

                <nav class="space-y-1">
                    <Button
                        v-for="item in navItems"
                        :key="item.label"
                        as-child
                        :variant="item.activo ? 'secondary' : 'ghost'"
                        size="sm"
                        class="w-full justify-start h-auto py-2.5 px-3 text-left transition-colors"
                        :class="{
                            'bg-white font-semibold text-slate-900 shadow-sm border border-slate-200': item.activo,
                            'text-slate-600 hover:text-slate-900': !item.activo,
                        }"
                    >
                        <Link :href="item.href" preserve-scroll>
                            <component
                                :is="item.icon"
                                class="size-4 mr-2.5 shrink-0"
                                :class="item.activo ? 'text-slate-900' : 'text-slate-400'"
                            />
                            <div class="min-w-0 flex-1">
                                <div class="text-xs truncate">{{ item.label }}</div>
                                <div class="text-[10px] text-slate-400 truncate font-normal">
                                    {{ item.descripcion }}
                                </div>
                            </div>
                        </Link>
                    </Button>
                </nav>
            </aside>

            <!-- Inyección de las Vistas del Módulo -->
            <section class="flex-1 min-w-0 w-full">
                <slot />
            </section>
        </div>
    </div>
</template>
