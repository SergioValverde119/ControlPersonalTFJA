<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { Filter, MoreHorizontal, PlusCircle, Search } from '@lucide/vue';
import Heading from '@/components/Heading.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { Input } from '@/components/ui/input';

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Nuevo Trámite', href: '/nuevo-tramite/plazas' },
            { title: 'Plazas disponibles', href: '/nuevo-tramite/plazas' },
        ],
    },
});

const plazas = [
    { codigo: 'PLZ-2026-081', puesto: 'Secretario de Acuerdos', adscripcion: 'Primera Sala Regional', nivel: 'N24', tipo: 'Presupuestal', estatus: 'Vacante' },
    { codigo: 'PLZ-2026-094', puesto: 'Actuario Judicial', adscripcion: 'Sala Especializada', nivel: 'N18', tipo: 'Presupuestal', estatus: 'Vacante' },
    { codigo: 'PLZ-2026-112', puesto: 'Analista Administrativo', adscripcion: 'Dirección de Nóminas', nivel: 'N14', tipo: 'Confianza', estatus: 'En Proceso' },
    { codigo: 'PLZ-2026-130', puesto: 'Oficial Jurisdiccional', adscripcion: 'Junta de Gobierno', nivel: 'N12', tipo: 'Honorarios', estatus: 'Vacante' },
];
</script>

<template>
    <Head title="Plazas Disponibles - GAD-YAR" />

    <div class="space-y-6">
        <Heading
            variant="small"
            title="Catálogo de Plazas Disponibles"
            description="Consulta de vacantes autorizadas listas para trámite de adscripción."
        />

        <!-- Controles de búsqueda -->
        <div class="flex items-center justify-between gap-4">
            <div class="relative flex-1 max-w-sm">
                <Search class="absolute left-2.5 top-2.5 h-4 w-4 text-muted-foreground" />
                <Input placeholder="Buscar por código, puesto o sala..." class="pl-8 text-xs" />
            </div>
            <div class="flex items-center gap-2">
                <Button variant="outline" size="sm" class="text-xs">
                    <Filter class="mr-1.5 h-3.5 w-3.5" />
                    Filtrar
                </Button>
                <Button size="sm" class="text-xs">
                    <PlusCircle class="mr-1.5 h-3.5 w-3.5" />
                    Nueva Vacante
                </Button>
            </div>
        </div>

        <!-- Tabla estructurada con tags HTML nativos y tokens Tailwind -->
        <div class="rounded-lg border border-border bg-card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="border-b border-border bg-muted/50 text-muted-foreground">
                        <tr>
                            <th class="h-10 px-4 font-medium">Código</th>
                            <th class="h-10 px-4 font-medium">Puesto</th>
                            <th class="h-10 px-4 font-medium">Adscripción</th>
                            <th class="h-10 px-4 font-medium">Nivel</th>
                            <th class="h-10 px-4 font-medium">Estatus</th>
                            <th class="h-10 px-4 font-medium text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border">
                        <tr
                            v-for="plaza in plazas"
                            :key="plaza.codigo"
                            class="transition-colors hover:bg-muted/40"
                        >
                            <td class="p-4 font-mono font-semibold text-foreground">{{ plaza.codigo }}</td>
                            <td class="p-4 font-medium text-foreground">{{ plaza.puesto }}</td>
                            <td class="p-4 text-muted-foreground">{{ plaza.adscripcion }}</td>
                            <td class="p-4 font-mono">{{ plaza.nivel }}</td>
                            <td class="p-4">
                                <Badge
                                    :variant="plaza.estatus === 'Vacante' ? 'default' : 'secondary'"
                                    class="text-[10px]"
                                >
                                    {{ plaza.estatus }}
                                </Badge>
                            </td>
                            <td class="p-4 text-right">
                                <DropdownMenu>
                                    <DropdownMenuTrigger :as-child="true">
                                        <Button variant="ghost" size="icon" class="h-7 w-7">
                                            <MoreHorizontal class="h-4 w-4" />
                                            <span class="sr-only">Acciones</span>
                                        </Button>
                                    </DropdownMenuTrigger>
                                    <DropdownMenuContent align="end" class="w-40 text-xs">
                                        <DropdownMenuItem class="cursor-pointer">
                                            Asignar a trámite
                                        </DropdownMenuItem>
                                        <DropdownMenuItem class="cursor-pointer">
                                            Ver detalle
                                        </DropdownMenuItem>
                                    </DropdownMenuContent>
                                </DropdownMenu>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>