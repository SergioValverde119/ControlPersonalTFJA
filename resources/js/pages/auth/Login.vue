<script setup lang="ts">
import { ref } from 'vue';
import { useForm, Head } from '@inertiajs/vue3';

const showPassword = ref(false);

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post('/login', {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <Head title="Acceso al Sistema - SIGTP" />

    <div class="min-h-screen flex flex-col md:flex-row antialiased font-sans bg-white">
        
        <!-- Panel Izquierdo: Formulario de Autenticación Institucional -->
        <div class="w-full md:w-5/12 lg:w-4/12 bg-white flex flex-col justify-between p-8 sm:p-12 z-10 border-r border-slate-200">
            
            <div>
                <!-- Identidad Gráfica Institucional: Logo TFJA y 90 Años -->
                <div class="flex items-center gap-4 mb-8">
                    <img src="/logo-color-h.svg" 
                         alt="Tribunal Federal de Justicia Administrativa" 
                         class="h-14 w-auto object-contain max-w-[200px]">
                    <div class="h-10 w-[1px] bg-slate-300"></div>
                    <img src="/logo-90-color.png" 
                         alt="90 Años TFJA" 
                         class="h-12 w-auto object-contain">
                </div>

                <!-- Bloque Placa del Sistema (Estilo WIN-SIAF) -->
                <div class="bg-[#01273E] text-white p-3.5 mb-8 text-center rounded-none border-b-2 border-[#B67238]">
                    <p class="font-sans font-bold text-sm tracking-wider uppercase">
                        SIGTP
                    </p>
                    <p class="text-[11px] uppercase tracking-widest text-slate-200 font-sans mt-0.5">
                        Sistema Institucional de Control de Personal
                    </p>
                </div>

                <!-- Título de Sección -->
                <h2 class="text-xs font-bold text-slate-700 uppercase tracking-wide mb-4">
                    Acceso
                </h2>

                <form @submit.prevent="submit" class="space-y-4">
                    
                    <!-- Campo: Correo Institucional -->
                    <div>
                        <label for="email" class="block text-xs text-slate-600 mb-1">
                            Correo Electrónico Institucional
                        </label>
                        <input type="email" 
                               id="email" 
                               v-model="form.email" 
                               required 
                               autofocus
                               placeholder="usuario@tfja.gob.mx"
                               class="w-full text-xs font-sans border border-slate-300 rounded-none px-3 py-2 focus:outline-none focus:border-[#01273E] bg-[#feffea] transition-colors">
                        <span v-if="form.errors.email" class="text-red-600 text-[11px] mt-1 block">
                            {{ form.errors.email }}
                        </span>
                    </div>

                    <!-- Campo: Contraseña con Toggle de Visibilidad -->
                    <div>
                        <div class="flex justify-between items-center mb-1">
                            <label for="password" class="block text-xs text-slate-600">Contraseña</label>
                            <a href="/forgot-password" class="text-[11px] text-slate-500 hover:text-[#01273E] transition-colors">
                                Recuperar Contraseña
                            </a>
                        </div>
                        
                        <div class="relative">
                            <input :type="showPassword ? 'text' : 'password'" 
                                   id="password" 
                                   v-model="form.password" 
                                   required
                                   class="w-full text-xs font-sans border border-slate-300 rounded-none pl-3 pr-10 py-2 focus:outline-none focus:border-[#01273E] bg-[#feffea] transition-colors">
                            
                            <button type="button" 
                                    @click="showPassword = !showPassword"
                                    aria-label="Mostrar u ocultar contraseña"
                                    class="absolute inset-y-0 right-0 px-3 flex items-center text-slate-500 hover:text-slate-700 cursor-pointer">
                                <svg v-if="!showPassword" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/>
                                </svg>
                            </button>
                        </div>

                        <span v-if="form.errors.password" class="text-red-600 text-[11px] mt-1 block">
                            {{ form.errors.password }}
                        </span>
                    </div>

                    <!-- Botón de Envío -->
                    <div class="pt-3">
                        <button type="submit" 
                                :disabled="form.processing"
                                class="w-full bg-[#01273E] hover:bg-[#001D2E] text-white font-bold text-xs py-2.5 px-4 rounded-none flex items-center justify-center gap-2 transition-colors cursor-pointer disabled:opacity-50">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                            </svg>
                            <span>Entrar</span>
                        </button>
                    </div>

                </form>
            </div>

            <!-- Pie de Página Institucional -->
            <div class="pt-6 border-t border-slate-200 text-center">
                <p class="text-[10px] text-slate-400 uppercase tracking-wide">
                    Tribunal Federal de Justicia Administrativa &mdash; {{ new Date().getFullYear() }}
                </p>
            </div>

        </div>

        <!-- Panel Derecho: Fotografía / Gráfico Institucional con Capa Azul -->
        <div class="hidden md:block md:w-7/12 lg:w-8/12 bg-cover bg-center relative bg-slate-900"
             style="background-image: url('https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?q=80&w=2070&auto=format&fit=crop');">
            <div class="absolute inset-0 bg-[#01273E]/45 backdrop-blur-[0.5px]"></div>
        </div>

    </div>
</template>