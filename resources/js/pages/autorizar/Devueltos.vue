<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import Heading from '@/components/Heading.vue';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { devueltos } from '@/routes/autorizar';

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Autorizar', href: devueltos.url() },
            { title: 'Devoluciones', href: devueltos.url() },
        ],
    },
});
</script>

<template>
    <Head title="Trámites Devueltos" />

    <div class="space-y-6">
        <Heading
            variant="small"
            title="Trámites Devueltos por Autorización"
            description="Expedientes rechazados durante la mesa de firma que requieren ajuste administrativo."
        />

        <Card>
            <CardHeader>
                <CardTitle class="text-sm font-semibold">Bandeja de Devoluciones</CardTitle>
                <CardDescription class="text-xs">
                    Movimientos devueltos a las áreas operativas con observaciones de rechazo.
                </CardDescription>
            </CardHeader>
            <CardContent>
                <div class="rounded-md border p-6 text-center text-xs text-muted-foreground">
                    No existen devoluciones registradas.
                </div>
            </CardContent>
        </Card>
    </div>
</template>