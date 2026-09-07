<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { Search } from '@lucide/vue';
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { consulta } from '@/routes/estado-tramite';

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Estado de Trámite', href: consulta.url() },
            { title: 'Consulta General', href: consulta.url() },
        ],
    },
});
</script>

<template>
    <Head title="Consulta General de Trámites - GAD-YAR" />

    <div class="space-y-6">
        <Heading
            variant="small"
            title="Consulta General de Folios"
            description="Localice expedientes, movimientos de personal y estado presupuestal en tiempo real."
        />

        <Card>
            <CardHeader>
                <CardTitle class="text-sm font-semibold">Criterios de Búsqueda</CardTitle>
                <CardDescription class="text-xs">
                    Ingrese el número de folio institucional, RFC o CURP del servidor público.
                </CardDescription>
            </CardHeader>
            <CardContent class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="space-y-1.5 md:col-span-2">
                        <Label class="text-xs">Folio o RFC</Label>
                        <Input placeholder="Ej. TRM-2026-0045 o VAHS980415..." class="text-xs font-mono" />
                    </div>
                    <div class="flex items-end">
                        <Button size="sm" class="w-full text-xs">
                            <Search class="mr-1.5 h-3.5 w-3.5" />
                            Buscar Folio
                        </Button>
                    </div>
                </div>
            </CardContent>
        </Card>
    </div>
</template>