<!-- eslint-disable import/order -->
<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { buttonVariants } from '@/components/ui/button';
import { Separator } from '@/components/ui/separator';
import { useCurrentUrl } from '@/composables/useCurrentUrl';
import { toUrl } from '@/lib/utils';
// Importaremos las rutas con Wayfinder en el siguiente paso
import { consulta, historico, pendientes, rechazados } from '@/routes/estado-tramite';
import type { NavItem } from '@/types';

const sidebarNavItems: NavItem[] = [
    {
        title: 'Consulta General',
        href: consulta(),
    },
    {
        title: 'Pendientes / En Proceso',
        href: pendientes(),
    },
    {
        title: 'Observados / Rechazados',
        href: rechazados(),
    },
    {
        title: 'Histórico de Trámites',
        href: historico(),
    },
];

const { isCurrentOrParentUrl } = useCurrentUrl();
</script>

<template>
    <div class="px-4 py-6">
        <div class="flex flex-col lg:flex-row lg:space-x-10">
            <!-- Menú lateral del módulo -->
            <aside class="w-full max-w-xl lg:w-56 shrink-0">
                <div class="mb-3 space-y-1">
                    <h2 class="text-sm font-bold tracking-tight text-foreground">
                        Estado de Trámite
                    </h2>
                    <p class="text-xs text-muted-foreground">
                        Seguimiento y auditoría de folios
                    </p>
                </div>

                <Separator class="mb-4" />

                <nav class="flex flex-col space-y-2" aria-label="Estado de trámite">
                    <Link
                        v-for="item in sidebarNavItems"
                        :key="toUrl(item.href)"
                        :href="item.href"
                        prefetch
                        cache-for="1m"
                        :class="[
                            buttonVariants({
                                variant: isCurrentOrParentUrl(item.href) ? 'default' : 'outline',
                                size: 'sm',
                            }),
                            'w-full justify-start text-xs font-medium transition-all focus-visible:outline-none',
                            isCurrentOrParentUrl(item.href)
                                ? 'shadow-sm font-semibold'
                                : 'bg-background hover:bg-muted text-muted-foreground hover:text-foreground border-border/80'
                        ]"
                    >
                        {{ item.title }}
                    </Link>
                </nav>
            </aside>

            <Separator class="my-6 lg:hidden" />

            <!-- Contenido dinámico de cada sub-vista -->
            <div class="flex-1 min-w-0">
                <section class="max-w-4xl space-y-6">
                    <slot />
                </section>
            </div>
        </div>
    </div>
</template>