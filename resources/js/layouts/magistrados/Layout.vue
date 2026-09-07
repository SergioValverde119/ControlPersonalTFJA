<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import { Separator } from '@/components/ui/separator';
import { useCurrentUrl } from '@/composables/useCurrentUrl';
import { toUrl } from '@/lib/utils';
import type { NavItem } from '@/types';

const sidebarNavItems: NavItem[] = [
    {
        title: 'UI Playground',
        href: '/playground',
    },
    {
        title: 'Pendientes',
        href: '/playground/pendientes',
    },
    {
        title: 'Autorizados',
        href: '/playground/autorizados',
    },
    {
        title: 'Observaciones',
        href: '/playground/observaciones',
    },
    {
        title: 'Historial',
        href: '/playground/historial',
    },
];

const { isCurrentOrParentUrl } = useCurrentUrl();
</script>

<template>
    <div class="w-full py-6 pl-0 pr-6">
        <!-- Título sin descripción -->
        <Heading title="Magistratura Visitante" class="mb-6" />

        <div class="flex flex-col lg:flex-row lg:space-x-8">
            <!-- Barra lateral pegada a la izquierda -->
            <aside class="w-full lg:w-48 shrink-0">
                <nav
                    class="flex flex-col space-y-1 space-x-0"
                    aria-label="Magistratura"
                >
                    <Button
                        v-for="item in sidebarNavItems"
                        :key="toUrl(item.href)"
                        variant="ghost"
                        :class="[
                            'w-full justify-start',
                            { 'bg-muted': isCurrentOrParentUrl(item.href) },
                        ]"
                        as-child
                    >
                        <Link :href="item.href">
                            <component v-if="item.icon" :is="item.icon" class="mr-2 h-4 w-4" />
                            {{ item.title }}
                        </Link>
                    </Button>
                </nav>
            </aside>

            <Separator class="my-6 lg:hidden" />

            <!-- Contenedor principal de contenido -->
            <div class="flex-1 min-w-0">
                <slot />
            </div>
        </div>
    </div>
</template>