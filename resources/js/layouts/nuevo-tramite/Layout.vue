<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { buttonVariants } from '@/components/ui/button';
import { Separator } from '@/components/ui/separator';
import { useCurrentUrl } from '@/composables/useCurrentUrl';
import { toUrl } from '@/lib/utils';
// Importación directa generada por Wayfinder:
import { alta, baja, democion, plazas, promocion } from '@/routes/nuevo-tramite';
import type { NavItem } from '@/types';

const sidebarNavItems: NavItem[] = [
    {
        title: 'Plazas disponibles',
        href: plazas(),
    },
    {
        title: 'Alta',
        href: alta(),
    },
    {
        title: 'Baja',
        href: baja(),
    },
    {
        title: 'Promoción',
        href: promocion(),
    },
    {
        title: 'Democión',
        href: democion(),
    },
];

const { isCurrentOrParentUrl } = useCurrentUrl();
</script>

<template>
    <div class="px-4 py-6">
        <div class="flex flex-col lg:flex-row lg:space-x-10">
            <!-- Barra lateral -->
            <aside class="w-full max-w-xl lg:w-56 shrink-0">
                <div class="mb-3 space-y-1">
                    <h2 class="text-sm font-bold tracking-tight text-foreground">
                        Nuevo Trámite
                    </h2>
                    <p class="text-xs text-muted-foreground">
                        Movimientos de personal
                    </p>
                </div>

                <Separator class="mb-4" />

                <nav class="flex flex-col space-y-2" aria-label="Nuevo trámite">
                    <Link
                        v-for="item in sidebarNavItems"
                        :key="toUrl(item.href)"
                        :href="item.href"
                        :class="[
                            buttonVariants({
                                variant: isCurrentOrParentUrl(item.href) ? 'default' : 'outline',
                                size: 'sm',
                            }),
                            'w-full justify-start text-xs font-medium transition-all',
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

            <!-- Contenedor de la vista -->
            <div class="flex-1 min-w-0">
                <section class="max-w-2xl space-y-6">
                    <slot />
                </section>
            </div>
        </div>
    </div>
</template>