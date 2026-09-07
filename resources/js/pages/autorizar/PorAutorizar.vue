<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import Heading from '@/components/Heading.vue';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { porAutorizar } from '@/routes/autorizar';

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Autorizar', href: porAutorizar.url() },
            { title: 'Por Autorizar', href: porAutorizar.url() },
        ],
    },
});
</script>

<template>
    <Head title="Trámites Listos para Autorización" />

    <div class="space-y-6">
        <Heading
            variant="small"
            title="Trámites Listos para Autorización"
            description="Expedientes con visto bueno normativo listos para autorización y afectación presupuestal."
        />

        <Card>
            <CardHeader>
                <CardTitle class="text-sm font-semibold">Mesa de Autorización</CardTitle>
                <CardDescription class="text-xs">
                    Folios listos para firma electrónica y afectación definitiva en nómina.
                </CardDescription>
            </CardHeader>
            <CardContent>
                <div class="rounded-md border p-6 text-center text-xs text-muted-foreground">
                    No hay trámites en espera de autorización definitiva.
                </div>
            </CardContent>
        </Card>
    </div>
</template>