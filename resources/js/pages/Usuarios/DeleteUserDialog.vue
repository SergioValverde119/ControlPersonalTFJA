<!-- eslint-disable import/order -->
<script setup lang="ts">
import { AlertTriangle } from '@lucide/vue';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import type { Usuario } from '@/types/usuarios';

type Props = {
    open: boolean;
    usuario: Usuario | null;
    processing: boolean;
};

const props = defineProps<Props>();

const emit = defineEmits<{
    (e: 'update:open', value: boolean): void;
    (e: 'confirm'): void;
}>();
</script>

<template>
    <Dialog
        :open="props.open"
        @update:open="(val) => emit('update:open', val)"
    >
        <DialogContent class="sm:max-w-md border-t-4 border-t-amber-600" :show-close-button="!props.processing">
            <DialogHeader>
                <div class="flex items-center gap-2">
                    <AlertTriangle class="size-5 text-amber-600" />
                    <DialogTitle class="text-amber-800">Confirmar Desactivación</DialogTitle>
                </div>
                <DialogDescription>
                    Esta acción suspenderá el acceso del servidor público al sistema sin alterar su historial ni sus registros de auditoría.
                </DialogDescription>
            </DialogHeader>

            <div v-if="props.usuario" class="p-3 bg-amber-50 border border-amber-200 text-xs text-amber-900 space-y-1">
                <p><strong>Servidor Público:</strong> {{ props.usuario.name }}</p>
                <p><strong>Correo Oficial:</strong> {{ props.usuario.email }}</p>
            </div>

            <DialogFooter class="gap-2">
                <DialogClose as-child>
                    <Button
                        type="button"
                        variant="outline"
                        size="sm"
                        :disabled="props.processing"
                    >
                        Cancelar
                    </Button>
                </DialogClose>
                <Button
                    type="button"
                    variant="destructive"
                    size="sm"
                    :disabled="props.processing"
                    @click="emit('confirm')"
                >
                    {{ props.processing ? 'Desactivando...' : 'Confirmar Desactivación' }}
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>