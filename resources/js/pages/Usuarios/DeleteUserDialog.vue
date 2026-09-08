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
        <DialogContent class="sm:max-w-md border-t-4 border-t-red-600" :show-close-button="!props.processing">
            <DialogHeader>
                <div class="flex items-center gap-2">
                    <AlertTriangle class="size-5 text-red-600" />
                    <DialogTitle class="text-red-700">Confirmar Eliminación</DialogTitle>
                </div>
                <DialogDescription>
                    Esta acción procederá con el borrado del usuario y revocará sus accesos al sistema institucional.
                </DialogDescription>
            </DialogHeader>

            <div v-if="props.usuario" class="p-3 bg-red-50 border border-red-200 text-xs text-red-800 space-y-1">
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
                    {{ props.processing ? 'Eliminando...' : 'Confirmar Eliminación' }}
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>