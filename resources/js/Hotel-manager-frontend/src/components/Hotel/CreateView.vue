<template>
    <HotelForm :isEditMode="false" />
</template>
  
<script lang="ts">
import { defineComponent } from 'vue';
import HotelForm from './HotelForm.vue';
  
export default defineComponent({
    components: {
        HotelForm
    }
});
</script>
  