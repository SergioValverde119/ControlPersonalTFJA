<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { UserMinus } from '@lucide/vue';
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
            { title: 'Baja', href: '/nuevo-tramite/baja' },
        ],
    },
});
</script>

<template>
    <Head title="Baja de Personal - GAD-YAR" />

    <div class="space-y-6">
        <Heading
            variant="small"
            title="Baja de Servidor Público"
            description="Conclusión de nombramiento, renuncia expresa o separación del cargo."
        />

        <Card>
            <CardHeader>
                <CardTitle class="text-sm font-semibold">Identificación y Causa de Baja</CardTitle>
                <CardDescription class="text-xs">
                    Localice al empleado para vincular el oficio de separación y fecha de corte de nómina.
                </CardDescription>
            </CardHeader>
            <CardContent class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <Label class="text-xs">No. de Empleado / RFC</Label>
                        <Input placeholder="Buscar empleado..." class="text-xs font-mono" />
                    </div>
                    <div class="space-y-1.5">
                        <Label class="text-xs">Último Día de Labores</Label>
                        <Input type="date" class="text-xs" />
                    </div>
                </div>

                <div class="space-y-1.5">
                    <Label class="text-xs">Motivo de Separación</Label>
                    <Input placeholder="Ej. Renuncia voluntaria, término de nombramiento..." class="text-xs" />
                </div>

                <div class="space-y-1.5">
                    <Label class="text-xs">Oficio de Entrega-Recepción / Respaldo</Label>
                    <Input placeholder="No. de oficio institucional" class="text-xs" />
                </div>
            </CardContent>
            <CardFooter class="flex justify-end gap-2 border-t px-6 py-3">
                <Button variant="outline" size="sm" class="text-xs">Cancelar</Button>
                <Button variant="destructive" size="sm" class="text-xs">
                    <UserMinus class="mr-1.5 h-3.5 w-3.5" />
                    Confirmar Trámite de Baja
                </Button>
            </CardFooter>
        </Card>
    </div>
</template>