<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import {
    Building2,
    CheckCircle2,
    FileSpreadsheet,
    FolderKanban,
    Layout,
    LayoutDashboard,
    PanelLeft,
    Receipt,
    RefreshCw,
} from '@lucide/vue';
import { ref } from 'vue';
import AppContent from '@/components/AppContent.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Separator } from '@/components/ui/separator';
import {
    Sidebar,
    SidebarContent,
    SidebarGroup,
    SidebarGroupContent,
    SidebarGroupLabel,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
    SidebarProvider,
    SidebarTrigger,
} from '@/components/ui/sidebar';

// Tipado local para simular el tipo AppVariant
type AppVariant = 'sidebar' | 'header';

// Estado para alternar entre las dos ramas del componente
const varianteActual = ref<AppVariant>('sidebar');

const alternarVariante = () => {
    varianteActual.value = varianteActual.value === 'sidebar' ? 'header' : 'sidebar';
};
</script>

<template>
    <Head title="Laboratorio - AppContent.vue" />

    <div class="min-h-screen bg-tfja-bg font-sans text-slate-800">
        
        <!-- =========================================================================
             RAMA A: CUANDO VARIANT === 'sidebar' (Integrado con SidebarProvider)
             ========================================================================= -->
        <SidebarProvider v-if="varianteActual === 'sidebar'" default-open>
            
            <!-- Barra Lateral Fija -->
            <Sidebar collapsible="icon" variant="sidebar">
                <SidebarHeader class="p-3">
                    <div class="flex items-center gap-2">
                        <div class="flex size-7 items-center justify-center rounded bg-slate-900 text-white font-bold text-xs">
                            TFJA
                        </div>
                        <span class="font-bold text-xs text-slate-800 uppercase group-data-[collapsible=icon]:hidden">WIN-SIAF</span>
                    </div>
                </SidebarHeader>

                <SidebarContent>
                    <SidebarGroup>
                        <SidebarGroupLabel>Módulos</SidebarGroupLabel>
                        <SidebarGroupContent>
                            <SidebarMenu>
                                <SidebarMenuItem>
                                    <SidebarMenuButton is-active tooltip="Nóminas">
                                        <FileSpreadsheet />
                                        <span>Cortes de Nómina</span>
                                    </SidebarMenuButton>
                                </SidebarMenuItem>
                                <SidebarMenuItem>
                                    <SidebarMenuButton tooltip="Plazas">
                                        <Building2 />
                                        <span>Catálogo de Plazas</span>
                                    </SidebarMenuButton>
                                </SidebarMenuItem>
                            </SidebarMenu>
                        </SidebarGroupContent>
                    </SidebarGroup>
                </SidebarContent>
            </Sidebar>

            <!-- Render de AppContent en modo 'sidebar' (monta <SidebarInset>) -->
            <AppContent variant="sidebar" class="bg-tfja-bg">
                
                <header class="flex h-14 shrink-0 items-center justify-between border-b bg-white px-4">
                    <div class="flex items-center gap-2">
                        <SidebarTrigger class="-ml-1" />
                        <Separator orientation="vertical" class="mr-2 h-4" />
                        <span class="text-xs font-bold uppercase text-slate-700">Modo Activo: SidebarInset</span>
                    </div>

                    <Button variant="outline" size="sm" class="h-8 gap-1.5 text-xs" @click="alternarVariante">
                        <Layout class="size-3.5" />
                        <span>Cambiar a Modo Header</span>
                    </Button>
                </header>

                <div class="p-6 space-y-6">
                    <div class="bg-white p-4 border border-slate-300 shadow-xs flex items-center justify-between">
                        <div>
                            <h2 class="text-xs font-bold uppercase text-slate-800">
                                Renderizado mediante &lt;SidebarInset&gt;
                            </h2>
                            <p class="text-[11px] text-slate-500">
                                El ancho y márgenes se ajustan automáticamente según el estado colapsado o expandido de la barra lateral.
                            </p>
                        </div>
                        <Badge variant="outline" class="font-mono text-emerald-700 bg-emerald-50 border-emerald-300">
                            variant="sidebar"
                        </Badge>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <Card>
                            <CardHeader class="pb-2">
                                <CardTitle class="text-xs font-bold text-slate-500 uppercase">Total de Plazas</CardTitle>
                            </CardHeader>
                            <CardContent>
                                <div class="text-2xl font-bold font-mono text-slate-800">1,450</div>
                            </CardContent>
                        </Card>
                        <Card>
                            <CardHeader class="pb-2">
                                <CardTitle class="text-xs font-bold text-slate-500 uppercase">Corte Quincenal</CardTitle>
                            </CardHeader>
                            <CardContent>
                                <div class="text-2xl font-bold font-mono text-emerald-700">Qna 17/2026</div>
                            </CardContent>
                        </Card>
                        <Card>
                            <CardHeader class="pb-2">
                                <CardTitle class="text-xs font-bold text-slate-500 uppercase">Presupuesto</CardTitle>
                            </CardHeader>
                            <CardContent>
                                <div class="text-2xl font-bold font-mono text-slate-800">$48.5M MXN</div>
                            </CardContent>
                        </Card>
                    </div>
                </div>

            </AppContent>

        </SidebarProvider>

        <!-- =========================================================================
             RAMA B: CUANDO VARIANT === 'header' (Navegación Superior Clásica)
             ========================================================================= -->
        <div v-else class="min-h-screen flex flex-col">
            
            <!-- Barra Superior Convencional -->
            <header class="border-b bg-white">
                <div class="max-w-7xl mx-auto px-4 h-14 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="flex size-7 items-center justify-center rounded bg-slate-900 text-white font-bold text-xs">
                            TFJA
                        </div>
                        <span class="font-bold text-xs text-slate-800 uppercase tracking-wide">WIN-SIAF (Top Navigation)</span>
                    </div>

                    <Button variant="outline" size="sm" class="h-8 gap-1.5 text-xs" @click="alternarVariante">
                        <PanelLeft class="size-3.5" />
                        <span>Cambiar a Modo Sidebar</span>
                    </Button>
                </div>
            </header>

            <!-- Render de AppContent en modo 'header' (monta <main.max-w-7xl>) -->
            <AppContent variant="header" class="p-6">
                
                <div class="bg-white p-4 border border-slate-300 shadow-xs flex items-center justify-between">
                    <div>
                        <h2 class="text-xs font-bold uppercase text-slate-800">
                            Renderizado mediante &lt;main class="max-w-7xl"&gt;
                        </h2>
                        <p class="text-[11px] text-slate-500">
                            Lienzo centrado horizontalmente con límite de ancho fijo, ideal para portales de consulta o accesos de solo lectura.
                        </p>
                    </div>
                    <Badge variant="outline" class="font-mono text-blue-700 bg-blue-50 border-blue-300">
                        variant="header"
                    </Badge>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <Card>
                        <CardHeader class="pb-2">
                            <CardTitle class="text-xs font-bold text-slate-500 uppercase">Total de Plazas</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <div class="text-2xl font-bold font-mono text-slate-800">1,450</div>
                        </CardContent>
                    </Card>
                    <Card>
                        <CardHeader class="pb-2">
                            <CardTitle class="text-xs font-bold text-slate-500 uppercase">Corte Quincenal</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <div class="text-2xl font-bold font-mono text-emerald-700">Qna 17/2026</div>
                        </CardContent>
                    </Card>
                    <Card>
                        <CardHeader class="pb-2">
                            <CardTitle class="text-xs font-bold text-slate-500 uppercase">Presupuesto</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <div class="text-2xl font-bold font-mono text-slate-800">$48.5M MXN</div>
                        </CardContent>
                    </Card>
                </div>

            </AppContent>

        </div>

    </div>
</template>