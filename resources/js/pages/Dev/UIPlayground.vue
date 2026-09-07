<script setup lang="ts">
import { Head } from '@inertiajs/vue3';

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Magistratura Visitante',
                href: '/playground',
            },
            {
                title: 'UI Playground',
                href: '/playground',
            },
        ],
    },
});
</script>

<template>
    <Head title="UI Playground - GAD-YAR" />

    <div class="space-y-6">
        <div class="rounded-lg border border-slate-200 bg-white p-6 shadow-xs">
            <h3 class="text-sm font-semibold text-slate-800">
                Área de Trabajo en UI Playground
            </h3>
            <p class="text-xs text-slate-500 mt-1">
                La barra superior y la barra lateral ahora provienen de la regla anidada en <code>app.ts</code>. Cero código duplicado y 100% apego al framework.
            </p>
        </div>
    </div>
</template>