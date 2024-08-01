<script lang="ts" setup>
import { useLayout } from './composables/layout';

const { layoutConfig } = useLayout();
</script>

<template>
    <div class="layout-footer">
        <i class="pi pi-prime mr-2" style="font-size: 2rem;"></i>
        by
        <span class="font-medium ml-2">Cloud</span>
    </div>
</template>
<style lang="scss" scoped></style>
