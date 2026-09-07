<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import Heading from '@/components/Heading.vue';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { historico } from '@/routes/estado-tramite';

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Estado de Trámite', href: historico.url() },
            { title: 'Histórico de Trámites', href: historico.url() },
        ],
    },
});
</script>

<template>
    <Head title="Histórico de Trámites" />

    <div class="space-y-6">
        <Heading
            variant="small"
            title="Histórico General de Trámites"
            description="Archivo general de movimientos de personal concluidos, autorizados o archivados."
        />

        <Card>
            <CardHeader>
                <CardTitle class="text-sm font-semibold">Bitácora Histórica</CardTitle>
                <CardDescription class="text-xs">
                    Consulta de registros pasados con efectos presupuestales y laborales asentados.
                </CardDescription>
            </CardHeader>
            <CardContent>
                <div class="rounded-md border p-6 text-center text-xs text-muted-foreground">
                    El archivo histórico se poblará automáticamente conforme avancen los cierres de ejercicio.
                </div>
            </CardContent>
        </Card>
    </div>
</template>