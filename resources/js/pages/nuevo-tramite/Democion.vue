<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { AlertCircle, ArrowDownRight } from '@lucide/vue';
import Heading from '@/components/Heading.vue';
import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert';
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
            { title: 'Democión', href: '/nuevo-tramite/democion' },
        ],
    },
});
</script>

<template>
    <Head title="Democión / Reclasificación - GAD-YAR" />

    <div class="space-y-6">
        <Heading
            variant="small"
            title="Democión / Reclasificación"
            description="Movimiento administrativo por reestructuración o ajuste funcional."
        />

        <Alert>
            <AlertCircle class="h-4 w-4" />
            <AlertTitle class="text-xs font-semibold">Aviso Administrativo</AlertTitle>
            <AlertDescription class="text-xs">
                Este trámite modificará el nivel tabular y las percepciones asociadas a partir de la fecha de efectos capturada.
            </AlertDescription>
        </Alert>

        <Card>
            <CardHeader>
                <CardTitle class="text-sm font-semibold">Cédula de Ajuste Funcional</CardTitle>
                <CardDescription class="text-xs">
                    Registro de cambio tabular o reasignación conforme a la normativa vigente.
                </CardDescription>
            </CardHeader>
            <CardContent class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <Label class="text-xs">Servidor Público</Label>
                        <Input placeholder="Nombre o RFC..." class="text-xs" />
                    </div>
                    <div class="space-y-1.5">
                        <Label class="text-xs">Nueva Plaza Destino</Label>
                        <Input placeholder="Código de plaza..." class="text-xs font-mono" />
                    </div>
                </div>

                <div class="space-y-1.5">
                    <Label class="text-xs">Causa Normativa / Dictamen</Label>
                    <Input placeholder="Detalle del acuerdo o solicitud..." class="text-xs" />
                </div>
            </CardContent>
            <CardFooter class="flex justify-end gap-2 border-t px-6 py-3">
                <Button variant="outline" size="sm" class="text-xs">Cancelar</Button>
                <Button size="sm" class="text-xs">
                    <ArrowDownRight class="mr-1.5 h-3.5 w-3.5" />
                    Registrar Reclasificación
                </Button>
            </CardFooter>
        </Card>
    </div>
</template>