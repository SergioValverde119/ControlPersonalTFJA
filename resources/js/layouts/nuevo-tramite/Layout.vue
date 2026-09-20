<!-- eslint-disable import/order -->
<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import {
    Briefcase,
    TrendingDown,
    TrendingUp,
    UserMinus,
    UserPlus,
} from '@lucide/vue';
import { computed } from 'vue';
import { Button } from '@/components/ui/button';
import { Separator } from '@/components/ui/separator';
import { toUrl } from '@/lib/utils';
import nuevoTramiteRoutes from '@/routes/nuevo-tramite';

interface NavItem {
    label: string;
    descripcion: string;
    href: string;
    icon: typeof Briefcase;
    activo: boolean;
}

const page = usePage();

const navItems = computed<NavItem[]>(() => {
    const urlActual = page.url;

    return [
        {
            label: 'Plazas Disponibles',
            descripcion: 'Consulta de vacantes autorizadas',
            href: toUrl(nuevoTramiteRoutes.plazas()),
            icon: Briefcase,
            activo: urlActual.startsWith('/nuevo-tramite/plazas'),
        },
        {
            label: 'Trámite de Alta',
            descripcion: 'Postulación y expediente inicial',
            href: toUrl(nuevoTramiteRoutes.alta()),
            icon: UserPlus,
            activo: urlActual.startsWith('/nuevo-tramite/alta'),
        },
        {
            label: 'Trámite de Baja',
            descripcion: 'Separación y término de efectos',
            href: toUrl(nuevoTramiteRoutes.baja()),
            icon: UserMinus,
            activo: urlActual.startsWith('/nuevo-tramite/baja'),
        },
        {
            label: 'Promoción',
            descripcion: 'Ascenso de categoría o nivel',
            href: toUrl(nuevoTramiteRoutes.promocion()),
            icon: TrendingUp,
            activo: urlActual.startsWith('/nuevo-tramite/promocion'),
        },
        {
            label: 'Democión',
            descripcion: 'Ajuste descendente de categoría',
            href: toUrl(nuevoTramiteRoutes.democion()),
            icon: TrendingDown,
            activo: urlActual.startsWith('/nuevo-tramite/democion'),
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
                        Módulo de Trámites y Movimientos
                    </h1>
                    <p class="text-xs text-slate-500 mt-1">
                        Gestión transaccional de plazas, captura de solicitudes y circuito de firmas institucionales.
                    </p>
                </div>
            </div>
        </div>

        <!-- Contenedor Principal Flex: Navegación + Área de Trabajo -->
        <div class="flex flex-col md:flex-row gap-6 items-start">
            <!-- Menú Lateral Secundario -->
            <aside class="w-full md:w-64 shrink-0 space-y-2 bg-slate-50/80 p-2.5 border border-slate-200">
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500 px-2 block">
                    Tipos de Trámite
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

            <!-- Área de Trabajo Central Dinámica -->
            <section class="flex-1 min-w-0 w-full">
                <slot />
            </section>
        </div>
    </div>
</template>
