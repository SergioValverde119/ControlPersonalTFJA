<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { home } from '@/routes';

defineProps<{
    title?: string;
    description?: string;
}>();
</script>

<template>
    <div
        class="flex min-h-svh flex-col items-center justify-center bg-slate-100 p-4 sm:p-6 md:p-10 select-none"
    >
        <div class="w-full max-w-md">
            <!-- Tarjeta Única con el color institucional (bg-tfja-bg) -->
            <div class="rounded-2xl border border-slate-300/80 bg-tfja-bg p-6 sm:p-8 shadow-xl">
                <div class="flex flex-col gap-6">
                    
                    <!-- Cabecera Institucional: Escudo TFJA, 90 Años y GAD-YAR -->
                    <div class="flex flex-col items-center gap-3 text-center">

                        <div class="mt-1">
                            <span class="text-base font-black tracking-wider text-tfja-blue uppercase">
                                GAD-YAR
                            </span>
                        </div>
                        <Link
                            :href="home()"
                            class="flex items-center justify-center gap-3 cursor-pointer"
                        >
                        
                            <img
                                src="/logo-color-h.svg"
                                alt="Tribunal Federal de Justicia Administrativa"
                                class="h-10 w-auto object-contain max-w-40"
                            />
                            <div class="h-7 w-px bg-slate-400/50"></div>
                            <img
                                src="/logo-90-color.png"
                                alt="90 Años TFJA"
                                class="h-9 w-auto object-contain"
                            />
                            

                        </Link>

                        

                        <!-- Título y descripción dinámica según la vista -->
                        <div v-if="title || description" class="space-y-1 text-center mt-1">
                            <h1 v-if="title" class="text-xs font-bold text-slate-700 uppercase tracking-wide">
                                {{ title }}
                            </h1>
                            <p v-if="description" class="text-center text-xs text-slate-500">
                                {{ description }}
                            </p>
                        </div>
                    </div>

                    <!-- Aquí se renderiza directamente el formulario de Login -->
                    <slot />

                </div>
            </div>

            
        </div>
    </div>
</template>