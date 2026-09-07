<script setup lang="ts">
import { AlertTriangle, ShieldAlert } from '@lucide/vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import type { Usuario } from '@/types/usuarios';

defineProps<{
    open: boolean;
    usuario: Usuario | null;
    processing: boolean;
}>();

const emit = defineEmits<{
    (e: 'update:open', val: boolean): void;
    (e: 'confirm'): void;
}>();
</script>

<template>
    <Dialog :open="open" @update:open="(val) => emit('update:open', val)">
        <DialogContent class="max-w-md">
            <DialogHeader>
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-destructive/10 text-destructive">
                        <AlertTriangle class="h-5 w-5" />
                    </div>

                    <div>
                        <DialogTitle class="text-base font-bold text-foreground">
                            Confirmar Baja de Usuario
                        </DialogTitle>
                        <DialogDescription class="text-xs mt-0.5">
                            Esta acción suspenderá el acceso del servidor público al sistema.
                        </DialogDescription>
                    </div>
                </div>
            </DialogHeader>

            <!-- Ficha del registro a inhabilitar -->
            <div v-if="usuario" class="rounded-lg border border-border/80 bg-muted/40 p-3 space-y-2 text-xs">
                <div class="flex items-center justify-between">
                    <span class="font-semibold text-foreground text-sm">{{ usuario.name }}</span>
                    <Badge variant="outline" class="text-[10px] uppercase font-mono">
                        ID: #{{ usuario.id }}
                    </Badge>
                </div>

                <div class="text-muted-foreground font-mono text-[11px]">
                    {{ usuario.email }}
                </div>

                <div
                    v-if="usuario.roles && usuario.roles.length"
                    class="flex items-center gap-1.5 pt-1 text-[11px] text-muted-foreground"
                >
                    <ShieldAlert class="h-3.5 w-3.5 text-amber-600 dark:text-amber-400 shrink-0" />
                    <span>Rol asignado: <strong>{{ usuario.roles[0].nombre }}</strong></span>
                </div>
            </div>

            <DialogFooter class="pt-2 gap-2 sm:gap-0">
                <Button
                    type="button"
                    variant="outline"
                    size="sm"
                    class="text-xs"
                    :disabled="processing"
                    @click="emit('update:open', false)"
                >
                    Cancelar
                </Button>

                <Button
                    type="button"
                    variant="destructive"
                    size="sm"
                    class="text-xs"
                    :disabled="processing"
                    @click="emit('confirm')"
                >
                    {{ processing ? 'Inhabilitando...' : 'Confirmar Baja Lógica' }}
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>