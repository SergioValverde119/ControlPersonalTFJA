<!-- eslint-disable import/order -->
<script setup lang="ts">
import type { ConsultaGeneralProps } from '../../types/tramites';
import ConsultaGeneral from './ConsultaGeneral.vue';

const props = defineProps<ConsultaGeneralProps>();
</script>

<template>
    <ConsultaGeneral v-bind="props" />
</template>
