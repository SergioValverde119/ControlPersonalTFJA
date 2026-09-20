<!-- eslint-disable import/order -->
<script setup lang="ts">
import {
    AlertCircle,
    FileCheck2,
    UploadCloud,
    X,
} from '@lucide/vue';
import { ref } from 'vue';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';

type Props = {
    id: string;
    label: string;
    descripcion?: string;
    modelValue: File | null;
    error?: string;
    required?: boolean;
    disabled?: boolean;
};

const props = withDefaults(defineProps<Props>(), {
    descripcion: 'Archivo en formato PDF (máximo 10MB)',
    error: '',
    required: false,
    disabled: false,
});

const emit = defineEmits<{
    (e: 'update:modelValue', value: File | null): void;
}>();

const inputRef = ref<HTMLInputElement | null>(null);
const errorLocal = ref<string>('');

const formatearTamano = (bytes: number): string => {
    if (bytes === 0) {
        return '0 KB';
    }

    const k = 1024;
    const sizes = ['Bytes', 'KB', 'KB', 'MB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));

    return `${parseFloat((bytes / Math.pow(k, i)).toFixed(1))} ${sizes[i]}`;
};

const procesarArchivo = (file: File | null) => {
    errorLocal.value = '';

    if (!file) {
        emit('update:modelValue', null);

        return;
    }

    if (file.type !== 'application/pdf' && !file.name.toLowerCase().endsWith('.pdf')) {
        errorLocal.value = 'El archivo seleccionado debe ser un documento PDF válido.';
        emit('update:modelValue', null);

        return;
    }

    if (file.size > 10 * 1024 * 1024) {
        errorLocal.value = 'El archivo excede el tamaño máximo permitido de 10MB.';
        emit('update:modelValue', null);

        return;
    }

    emit('update:modelValue', file);
};

const onFileChange = (e: Event) => {
    const target = e.target as HTMLInputElement;
    const file = target.files && target.files.length > 0 ? target.files[0] : null;

    procesarArchivo(file);
};

const quitarArchivo = () => {
    errorLocal.value = '';

    if (inputRef.value) {
        inputRef.value.value = '';
    }

    emit('update:modelValue', null);
};

const abrirSelector = () => {
    if (props.disabled) {
        return;
    }

    inputRef.value?.click();
};
</script>

<template>
    <div class="space-y-1.5 text-xs">
        <div class="flex items-center justify-between">
            <Label :for="props.id" class="font-bold text-slate-700 uppercase">
                {{ props.label }}
                <span v-if="props.required" class="text-red-600">*</span>
            </Label>
            <span class="text-[10px] text-slate-400 font-normal">
                {{ props.descripcion }}
            </span>
        </div>

        <input
            :id="props.id"
            ref="inputRef"
            type="file"
            accept="application/pdf,.pdf"
            class="hidden"
            :disabled="props.disabled"
            @change="onFileChange"
        />

        <!-- Estado 1: Archivo Seleccionado y Válido -->
        <div
            v-if="props.modelValue"
            class="p-2.5 bg-slate-50 border border-slate-200 rounded flex items-center justify-between gap-2"
        >
            <div class="flex items-center gap-2 min-w-0 flex-1">
                <FileCheck2 class="size-4 text-emerald-600 shrink-0" />
                <div class="min-w-0 flex-1">
                    <p class="font-medium text-slate-800 truncate text-xs">
                        {{ props.modelValue.name }}
                    </p>
                    <p class="text-[10px] text-slate-400 font-mono mt-0.5">
                        {{ formatearTamano(props.modelValue.size) }}
                    </p>
                </div>
            </div>

            <Button
                type="button"
                variant="ghost"
                size="icon-sm"
                class="size-6 text-slate-400 hover:text-red-600 hover:bg-red-50"
                :disabled="props.disabled"
                title="Quitar archivo"
                @click="quitarArchivo"
            >
                <X class="size-3.5" />
            </Button>
        </div>

        <!-- Estado 2: Caja de Subida / Selección -->
        <div
            v-else
            class="border border-dashed border-slate-300 rounded p-3 text-center cursor-pointer hover:bg-slate-50 hover:border-slate-400 transition-colors"
            :class="{ 'border-red-400 bg-red-50/40': errorLocal || props.error }"
            @click="abrirSelector"
        >
            <div class="flex flex-col items-center justify-center gap-1">
                <UploadCloud class="size-5 text-slate-400" />
                <p class="text-[11px] text-slate-600 font-medium">
                    Haz clic para adjuntar documento PDF
                </p>
            </div>
        </div>

        <!-- Errores de Validación -->
        <div v-if="errorLocal || props.error" class="flex items-center gap-1 text-[11px] text-red-600 pt-0.5">
            <AlertCircle class="size-3 shrink-0" />
            <span>{{ errorLocal || props.error }}</span>
        </div>
    </div>
</template>
