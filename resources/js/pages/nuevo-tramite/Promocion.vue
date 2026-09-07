<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { TrendingUp } from '@lucide/vue';
import Heading from '@/components/Heading.vue';
import { Badge } from '@/components/ui/badge';
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
            { title: 'Promoción', href: '/nuevo-tramite/promocion' },
        ],
    },
});
</script>

<template>
    <Head title="Promoción de Puesto - GAD-YAR" />

    <div class="space-y-6">
        <Heading
            variant="small"
            title="Promoción / Ascenso Tabular"
            description="Reclasificación de puesto hacia un nivel y tabulador superior."
        />

        <Card>
            <CardHeader>
                <CardTitle class="text-sm font-semibold">Comparativa de Movimiento</CardTitle>
                <CardDescription class="text-xs">
                    Verifique los datos de origen y la vacante destino a promover.
                </CardDescription>
            </CardHeader>
            <CardContent class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Plaza Origen -->
                    <div class="rounded-md border p-3 bg-muted/30 space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="text-[11px] font-semibold text-muted-foreground uppercase tracking-wide">Plaza Actual</span>
                            <Badge variant="outline" class="text-[10px]">Nivel N18</Badge>
                        </div>
                        <p class="text-xs font-semibold">Actuario Judicial</p>
                        <p class="text-[11px] text-muted-foreground">Sala Regional Centro</p>
                    </div>

                    <!-- Plaza Destino -->
                    <div class="rounded-md border border-primary/40 p-3 bg-primary/5 space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="text-[11px] font-semibold text-primary uppercase tracking-wide">Plaza Destino</span>
                            <Badge class="text-[10px]">Nivel N24</Badge>
                        </div>
                        <p class="text-xs font-semibold">Secretario de Acuerdos</p>
                        <p class="text-[11px] text-muted-foreground">Primera Sala Regional</p>
                    </div>
                </div>

                <div class="space-y-1.5">
                    <Label class="text-xs">Dictamen de Comisión o Justificación</Label>
                    <Input placeholder="No. de acuerdo de la Junta de Gobierno..." class="text-xs" />
                </div>
            </CardContent>
            <CardFooter class="flex justify-end gap-2 border-t px-6 py-3">
                <Button variant="outline" size="sm" class="text-xs">Cancelar</Button>
                <Button size="sm" class="text-xs">
                    <TrendingUp class="mr-1.5 h-3.5 w-3.5" />
                    Formalizar Promoción
                </Button>
            </CardFooter>
        </Card>
    </div>
</template>