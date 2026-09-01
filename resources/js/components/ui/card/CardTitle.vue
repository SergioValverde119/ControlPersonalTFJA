<script setup lang="ts">
import type { HTMLAttributes } from "vue"
import { cn } from "@/lib/utils"

const props = defineProps<{
  class?: HTMLAttributes["class"]
}>()
</script>

<template>
  <h3
    data-slot="card-title"
    :class="cn('text-sm font-bold text-tfja-blue tracking-tight leading-snug', props.class)"
    
    
  >
    <slot />
  </h3>
</template>
