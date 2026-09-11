<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import Heading from '@/components/Heading.vue';
//import { Badge } from '@/components/ui/badge';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { pendientes } from '@/routes/estado-tramite';

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Estado de Trámite', href: pendientes.url() },
            { title: 'Pendientes', href: pendientes.url() },
        ],
    },
});
</script>

<template>
    <Head title="Trámites Pendientes - GAD-YAR" />

    <div class="space-y-6">
        <Heading
            variant="small"
            title="Trámites en Proceso / Pendientes"
            description="Expedientes que se encuentran actualmente en curso dentro de las áreas de validación."
        />

        <Card>
            <CardHeader>
                <CardTitle class="text-sm font-semibold">Bandeja de Trámites Activos</CardTitle>
                <CardDescription class="text-xs">
                    Listado de movimientos institucionales pendientes de dictamen definitivo.
                </CardDescription>
            </CardHeader>
            <CardContent>
                <div class="rounded-md border p-6 text-center text-xs text-muted-foreground">
                    No hay trámites pendientes en esta sección o la tabla está lista para conectarse al controlador.
                </div>
            </CardContent>
        </Card>
    </div>
</template>