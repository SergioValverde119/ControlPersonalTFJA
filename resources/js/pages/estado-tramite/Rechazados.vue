<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import Heading from '@/components/Heading.vue';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { rechazados } from '@/routes/estado-tramite';

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Estado de Trámite', href: rechazados.url() },
            { title: 'Observados / Rechazados', href: rechazados.url() },
        ],
    },
});
</script>

<template>
    <Head title="Trámites Observados o Rechazados" />

    <div class="space-y-6">
        <Heading
            variant="small"
            title="Trámites Observados o Rechazados"
            description="Expedientes que requieren correcciones normativas o que fueron devueltos por el área de validación."
        />

        <Card>
            <CardHeader>
                <CardTitle class="text-sm font-semibold">Bandeja de Observaciones</CardTitle>
                <CardDescription class="text-xs">
                    Listado de incidencias devueltas para aclaración documental o técnica.
                </CardDescription>
            </CardHeader>
            <CardContent>
                <div class="rounded-md border p-6 text-center text-xs text-muted-foreground">
                    No hay trámites con observaciones activas en este momento.
                </div>
            </CardContent>
        </Card>
    </div>
</template>