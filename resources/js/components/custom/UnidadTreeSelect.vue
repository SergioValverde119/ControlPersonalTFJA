<!-- eslint-disable import/order -->
<script setup lang="ts">
import {
    Building2,
    ChevronDown,
    ChevronRight,
    Folder,
    Search,
    X,
} from '@lucide/vue';
import { computed, ref } from 'vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import {
    Collapsible,
    CollapsibleContent,
} from '@/components/ui/collapsible';
import { Input } from '@/components/ui/input';
import type { UnidadOrganizacional } from '@/types/usuarios';

type Props = {
    modelValue: number | string | null;
    unidades: UnidadOrganizacional[];
    placeholder?: string;
    disabled?: boolean;
};

const props = withDefaults(defineProps<Props>(), {
    placeholder: 'Seleccione unidad de adscripción...',
    disabled: false,
});

const emit = defineEmits<{
    (e: 'update:modelValue', value: number | null): void;
}>();

const busqueda = ref('');
const nodosAbiertos = ref<Set<number>>(new Set([1]));

const buscarNodoPorId = (
    nodos: UnidadOrganizacional[],
    id: number,
): UnidadOrganizacional | null => {
    for (const nodo of nodos) {
        if (nodo.id === id) {
            return nodo;
        }

        if (nodo.hijos?.length) {
            const encontrado = buscarNodoPorId(nodo.hijos, id);

            if (encontrado) {
                return encontrado;
            }
        }
    }

    return null;
};

const obtenerRutaJerarquica = (
    nodos: UnidadOrganizacional[],
    id: number,
    ruta: string[] = [],
): string[] | null => {
    for (const nodo of nodos) {
        const rutaActual = [...ruta, nodo.nombre];

        if (nodo.id === id) {
            return rutaActual;
        }

        if (nodo.hijos?.length) {
            const resultado = obtenerRutaJerarquica(nodo.hijos, id, rutaActual);

            if (resultado) {
                return resultado;
            }
        }
    }

    return null;
};

const nodoSeleccionado = computed(() => {
    if (!props.modelValue) {
        return null;
    }

    return buscarNodoPorId(props.unidades, Number(props.modelValue));
});

const rutaSeleccionada = computed(() => {
    if (!props.modelValue) {
        return [];
    }

    return obtenerRutaJerarquica(props.unidades, Number(props.modelValue)) ?? [];
});

const padreInmediatoSeleccionado = computed(() => {
    if (rutaSeleccionada.value.length > 1) {
        return rutaSeleccionada.value[rutaSeleccionada.value.length - 2];
    }

    return null;
});

const toggleNodo = (id: number) => {
    if (nodosAbiertos.value.has(id)) {
        nodosAbiertos.value.delete(id);
    } else {
        nodosAbiertos.value.add(id);
    }
};

const esSeleccionado = (id: number): boolean => {
    if (props.modelValue === null || props.modelValue === undefined || props.modelValue === '') {
        return false;
    }

    return Number(props.modelValue) === id;
};

const toggleSeleccion = (id: number) => {
    if (esSeleccionado(id)) {
        emit('update:modelValue', null);
    } else {
        emit('update:modelValue', id);
    }
};

const limpiar = () => {
    emit('update:modelValue', null);
    busqueda.value = '';
};

interface NodoPlano {
    id: number;
    nombre: string;
    clave?: string;
    padreNombre: string | null;
}

const aplanarConPadre = (
    nodos: UnidadOrganizacional[],
    padreNombre: string | null = null,
): NodoPlano[] => {
    let lista: NodoPlano[] = [];

    for (const nodo of nodos) {
        lista.push({
            id: nodo.id,
            nombre: nodo.nombre,
            clave: nodo.clave,
            padreNombre,
        });

        if (nodo.hijos?.length) {
            lista = lista.concat(aplanarConPadre(nodo.hijos, nodo.nombre));
        }
    }

    return lista;
};

const resultadosBusqueda = computed(() => {
    const q = busqueda.value.trim().toLowerCase();

    if (!q) {
        return [];
    }

    return aplanarConPadre(props.unidades).filter((n) => {
        const coincideNombre = n.nombre.toLowerCase().includes(q);
        const coincideClave = n.clave ? n.clave.toLowerCase().includes(q) : false;

        return coincideNombre || coincideClave;
    });
});
</script>

<template>
    <div class="space-y-2 text-xs">
        <!-- Indicador de Unidad Seleccionada -->
        <div
            v-if="nodoSeleccionado"
            class="p-2.5 bg-emerald-50 border border-emerald-200 flex items-center justify-between gap-2 rounded"
        >
            <div class="min-w-0 flex-1">
                <span class="font-bold text-emerald-900 truncate block">
                    {{ nodoSeleccionado.nombre }}
                </span>
                <p v-if="padreInmediatoSeleccionado" class="text-[11px] text-emerald-700 truncate mt-0.5">
                    Depende de: {{ padreInmediatoSeleccionado }}
                </p>
            </div>

            <Button
                type="button"
                variant="ghost"
                size="icon-sm"
                class="size-6 text-emerald-700 hover:text-emerald-900 hover:bg-emerald-100"
                :disabled="props.disabled"
                title="Quitar adscripción"
                @click="limpiar"
            >
                <X class="size-3.5" />
            </Button>
        </div>

        <!-- Buscador Rápido -->
        <div class="relative">
            <Search class="absolute left-2.5 top-2.5 size-3.5 text-slate-400" />
            <Input
                v-model="busqueda"
                type="search"
                placeholder="Buscar unidad, ponencia, sala o región..."
                class="pl-8 text-xs bg-white"
                :disabled="props.disabled"
            />
        </div>

        <!-- Vista Jerárquica y Resultados -->
        <div class="border border-slate-200 bg-white max-h-64 overflow-y-auto divide-y divide-slate-100">
            <!-- Resultados de Búsqueda -->
            <template v-if="busqueda.trim().length > 0">
                <div v-if="resultadosBusqueda.length === 0" class="p-4 text-center text-slate-400 italic">
                    No se encontraron dependencias con "{{ busqueda }}".
                </div>

                <div
                    v-for="item in resultadosBusqueda"
                    :key="item.id"
                    class="p-2 hover:bg-slate-50 flex items-center justify-between gap-3 cursor-pointer transition-colors"
                    :class="{ 'bg-emerald-50/70 font-semibold': esSeleccionado(item.id) }"
                    @click="toggleSeleccion(item.id)"
                >
                    <div class="min-w-0 flex-1">
                        <span class="text-slate-800 truncate block">{{ item.nombre }}</span>
                        <p v-if="item.padreNombre" class="text-[10px] text-slate-400 truncate mt-0.5">
                            Depende de: {{ item.padreNombre }}
                        </p>
                    </div>

                    <Checkbox
                        :model-value="esSeleccionado(item.id)"
                        :checked="esSeleccionado(item.id)"
                        :disabled="props.disabled"
                        aria-label="Seleccionar unidad"
                        @click.stop="toggleSeleccion(item.id)"
                    />
                </div>
            </template>

            <!-- Recorrido del Árbol -->
            <template v-else>
                <div v-if="props.unidades.length === 0" class="p-4 text-center text-slate-400 italic">
                    Sin estructura de unidades disponible.
                </div>

                <!-- Nivel 1: Órgano Central -->
                <div v-for="u1 in props.unidades" :key="u1.id" class="p-1">
                    <Collapsible :open="nodosAbiertos.has(u1.id)">
                        <div
                            class="flex items-center justify-between gap-1 p-1 rounded hover:bg-slate-50 transition-colors"
                            :class="{ 'bg-emerald-50/80': esSeleccionado(u1.id) }"
                        >
                            <button
                                v-if="u1.hijos?.length"
                                type="button"
                                class="p-1 text-slate-400 hover:text-slate-700 hover:bg-slate-100 rounded"
                                @click.stop="toggleNodo(u1.id)"
                            >
                                <component
                                    :is="nodosAbiertos.has(u1.id) ? ChevronDown : ChevronRight"
                                    class="size-3.5 shrink-0"
                                />
                            </button>
                            <div v-else class="size-5 shrink-0" />

                            <div
                                class="flex items-center justify-between flex-1 gap-2 cursor-pointer py-0.5 pr-1 select-none"
                                @click="toggleSeleccion(u1.id)"
                            >
                                <div class="flex items-center gap-1.5 min-w-0">
                                    <Building2 class="size-3.5 text-slate-500 shrink-0" />
                                    <span class="truncate font-semibold text-slate-800">{{ u1.nombre }}</span>
                                </div>

                                <Checkbox
                                    :model-value="esSeleccionado(u1.id)"
                                    :checked="esSeleccionado(u1.id)"
                                    :disabled="props.disabled"
                                    aria-label="Seleccionar unidad"
                                    @click.stop="toggleSeleccion(u1.id)"
                                />
                            </div>
                        </div>

                        <!-- Nivel 2: Regiones / Territorios -->
                        <CollapsibleContent v-if="u1.hijos?.length" class="pl-3 border-l border-slate-200 ml-2 space-y-0.5">
                            <div v-for="u2 in u1.hijos" :key="u2.id">
                                <Collapsible :open="nodosAbiertos.has(u2.id)">
                                    <div
                                        class="flex items-center justify-between gap-1 p-1 rounded hover:bg-slate-50 transition-colors"
                                        :class="{ 'bg-emerald-50/80': esSeleccionado(u2.id) }"
                                    >
                                        <button
                                            v-if="u2.hijos?.length"
                                            type="button"
                                            class="p-1 text-slate-400 hover:text-slate-700 hover:bg-slate-100 rounded"
                                            @click.stop="toggleNodo(u2.id)"
                                        >
                                            <component
                                                :is="nodosAbiertos.has(u2.id) ? ChevronDown : ChevronRight"
                                                class="size-3.5 shrink-0"
                                            />
                                        </button>
                                        <div v-else class="size-5 shrink-0" />

                                        <div
                                            class="flex items-center justify-between flex-1 gap-2 cursor-pointer py-0.5 pr-1 select-none"
                                            @click="toggleSeleccion(u2.id)"
                                        >
                                            <div class="flex items-center gap-1.5 min-w-0">
                                                <Folder class="size-3.5 text-slate-400 shrink-0" />
                                                <span class="truncate font-medium text-slate-700">{{ u2.nombre }}</span>
                                            </div>

                                            <Checkbox
                                                :model-value="esSeleccionado(u2.id)"
                                                :checked="esSeleccionado(u2.id)"
                                                :disabled="props.disabled"
                                                aria-label="Seleccionar unidad"
                                                @click.stop="toggleSeleccion(u2.id)"
                                            />
                                        </div>
                                    </div>

                                    <!-- Nivel 3: Salas / Sedes -->
                                    <CollapsibleContent v-if="u2.hijos?.length" class="pl-3 border-l border-slate-200 ml-2 space-y-0.5">
                                        <div v-for="u3 in u2.hijos" :key="u3.id">
                                            <Collapsible :open="nodosAbiertos.has(u3.id)">
                                                <div
                                                    class="flex items-center justify-between gap-1 p-1 rounded hover:bg-slate-50 transition-colors"
                                                    :class="{ 'bg-emerald-50/80': esSeleccionado(u3.id) }"
                                                >
                                                    <button
                                                        v-if="u3.hijos?.length"
                                                        type="button"
                                                        class="p-1 text-slate-400 hover:text-slate-700 hover:bg-slate-100 rounded"
                                                        @click.stop="toggleNodo(u3.id)"
                                                    >
                                                        <component
                                                            :is="nodosAbiertos.has(u3.id) ? ChevronDown : ChevronRight"
                                                            class="size-3 shrink-0"
                                                        />
                                                    </button>
                                                    <div v-else class="size-5 shrink-0" />

                                                    <div
                                                        class="flex items-center justify-between flex-1 gap-2 cursor-pointer py-0.5 pr-1 select-none"
                                                        @click="toggleSeleccion(u3.id)"
                                                    >
                                                        <span class="truncate text-slate-600">{{ u3.nombre }}</span>

                                                        <Checkbox
                                                            :model-value="esSeleccionado(u3.id)"
                                                            :checked="esSeleccionado(u3.id)"
                                                            :disabled="props.disabled"
                                                            aria-label="Seleccionar unidad"
                                                            @click.stop="toggleSeleccion(u3.id)"
                                                        />
                                                    </div>
                                                </div>

                                                <!-- Nivel 4: Ponencias / Áreas -->
                                                <CollapsibleContent v-if="u3.hijos?.length" class="pl-3 border-l border-slate-200 ml-2 space-y-0.5">
                                                    <div
                                                        v-for="u4 in u3.hijos"
                                                        :key="u4.id"
                                                        class="flex items-center justify-between gap-2 p-1.5 rounded hover:bg-slate-50 cursor-pointer select-none transition-colors"
                                                        :class="{ 'bg-emerald-50/80': esSeleccionado(u4.id) }"
                                                        @click="toggleSeleccion(u4.id)"
                                                    >
                                                        <span class="text-slate-600 truncate pl-4">{{ u4.nombre }}</span>

                                                        <Checkbox
                                                            :model-value="esSeleccionado(u4.id)"
                                                            :checked="esSeleccionado(u4.id)"
                                                            :disabled="props.disabled"
                                                            aria-label="Seleccionar unidad"
                                                            @click.stop="toggleSeleccion(u4.id)"
                                                        />
                                                    </div>
                                                </CollapsibleContent>
                                            </Collapsible>
                                        </div>
                                    </CollapsibleContent>
                                </Collapsible>
                            </div>
                        </CollapsibleContent>
                    </Collapsible>
                </div>
            </template>
        </div>
    </div>
</template>
