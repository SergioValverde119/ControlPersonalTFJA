<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { UserCheck } from '@lucide/vue';
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardFooter,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Nuevo Trámite', href: '/nuevo-tramite/plazas' },
            { title: 'Alta', href: '/nuevo-tramite/alta' },
        ],
    },
});
</script>

<template>
    <Head title="Alta de Personal - GAD-YAR" />

    <div class="space-y-6">
        <Heading
            variant="small"
            title="Alta de Personal"
            description="Registro inicial del servidor público y asignación presupuestal."
        />

        <Card>
            <CardHeader>
                <CardTitle class="text-sm font-semibold">Datos del Servidor Público</CardTitle>
                <CardDescription class="text-xs">
                    Capture la información oficial conforme a la cédula de identificación fiscal y CURP.
                </CardDescription>
            </CardHeader>
            <CardContent class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <Label class="text-xs">RFC con homoclave</Label>
                        <Input placeholder="VAHS980415XXX" class="text-xs font-mono uppercase" />
                    </div>
                    <div class="space-y-1.5">
                        <Label class="text-xs">CURP</Label>
                        <Input placeholder="VAHS980415HDFRRL01" class="text-xs font-mono uppercase" />
                    </div>
                </div>

                <div class="space-y-1.5">
                    <Label class="text-xs">Nombre Completo</Label>
                    <Input placeholder="Nombre(s) y Apellidos" class="text-xs" />
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <Label class="text-xs">Plaza Asignada</Label>
                        <Input placeholder="PLZ-2026-081" class="text-xs font-mono" />
                    </div>
                    <div class="space-y-1.5">
                        <Label class="text-xs">Fecha de Efectos</Label>
                        <Input type="date" class="text-xs" />
                    </div>
                </div>
            </CardContent>
            <CardFooter class="flex justify-end gap-2 border-t px-6 py-3">
                <Button variant="outline" size="sm" class="text-xs">Cancelar</Button>
                <Button size="sm" class="text-xs">
                    <UserCheck class="mr-1.5 h-3.5 w-3.5" />
                    Generar Movimiento de Alta
                </Button>
            </CardFooter>
        </Card>
    </div>
</template>