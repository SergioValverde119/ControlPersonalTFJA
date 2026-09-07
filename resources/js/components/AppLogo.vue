<script setup lang="ts">
const appTitle = 'GAD-YAR';
</script>

<template>
    <div class="flex items-center gap-x-3 select-none">
        <!-- Escudo / Logotipo Oficial -->
        <img
            src="/logo-color-h.svg"
            alt="Logotipo Oficial"
            class="h-8 w-auto object-contain transition-all duration-200 group-hover:brightness-130 group-hover:contrast-150"
        />

        <!-- Logotipo Conmemorativo -->
        <img
            src="/logo-90-color.png"
            alt="Logotipo Conmemorativo"
            class="h-8 w-auto object-contain transition-all duration-200 group-hover:brightness-120 group-hover:contrast-125"
        />

        <!-- Separador institucional y Nombre del Sistema -->
        <div class="border-l border-white/20 pl-3">
            <span
                class="text-sm font-bold tracking-wide text-white transition-colors duration-200 group-hover:text-tfja-bronze"
            >
                {{ appTitle }}
            </span>
        </div>
    </div>
</template>