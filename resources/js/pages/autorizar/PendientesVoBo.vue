<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import Heading from '@/components/Heading.vue';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { pendientesVobo }  from '@/routes/autorizar';

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Autorizar', href: pendientesVobo.url() },
            { title: 'Pendientes de Vo.Bo.', href: pendientesVobo.url() },
        ],
    },
});
</script>

<template>
    <Head title="Pendientes de Visto Bueno - GAD-YAR" />

    <div class="space-y-6">
        <Heading
            variant="small"
            title="Trámites Pendientes de Visto Bueno"
            description="Expedientes que requieren revisión jerárquica previa antes de pasar a la firma definitiva."
        />

        <Card>
            <CardHeader>
                <CardTitle class="text-sm font-semibold">Bandeja de Vo.Bo.</CardTitle>
                <CardDescription class="text-xs">
                    Listado de movimientos institucionales en espera de validación de mandos medios.
                </CardDescription>
            </CardHeader>
            <CardContent>
                <div class="rounded-md border p-6 text-center text-xs text-muted-foreground">
                    No hay solicitudes pendientes de visto bueno en este momento.
                </div>
            </CardContent>
        </Card>
    </div>
</template>