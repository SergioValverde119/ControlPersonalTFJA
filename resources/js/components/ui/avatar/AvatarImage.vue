<script setup lang="ts">
import type { AvatarImageProps } from "reka-ui"
import { AvatarImage } from "reka-ui"
import { cn } from "@/lib/utils"

const props = defineProps<AvatarImageProps & { class?: string}>()
</script>

<template>
  <AvatarImage
    data-slot="avatar-image"
    v-bind="props"
    :class="cn('aspect-square size-full object-cover', props.class)"
  >
    <slot />
  </AvatarImage>
</template>
