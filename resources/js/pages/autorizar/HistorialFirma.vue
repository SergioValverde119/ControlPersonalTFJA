<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import Heading from '@/components/Heading.vue';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { historialFirma } from '@/routes/autorizar';

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Autorizar', href: historialFirma.url() },
            { title: 'Historial de Firma', href: historialFirma.url() },
        ],
    },
});
</script>

<template>
    <Head title="Historial de Firma y Autorización" />

    <div class="space-y-6">
        <Heading
            variant="small"
            title="Historial de Firma Electrónica"
            description="Registro auditable de folios autorizados por la Dirección de Nóminas y áreas competentes."
        />

        <Card>
            <CardHeader>
                <CardTitle class="text-sm font-semibold">Bitácora de Firmas</CardTitle>
                <CardDescription class="text-xs">
                    Consulta de sellos y firmas digitales asociadas a los movimientos de personal.
                </CardDescription>
            </CardHeader>
            <CardContent>
                <div class="rounded-md border p-6 text-center text-xs text-muted-foreground">
                    El historial se actualizará conforme se firmen los expedientes.
                </div>
            </CardContent>
        </Card>
    </div>
</template>