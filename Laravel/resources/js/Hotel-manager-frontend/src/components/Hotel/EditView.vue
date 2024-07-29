<template>
  <HotelForm :isEditMode="true" :hotelId="hotelId" />
</template>

<script lang="ts">
import { defineComponent } from 'vue';
import HotelForm from './HotelForm.vue';
import { useRoute } from 'vue-router';

export default defineComponent({
  components: {
    HotelForm
  },
  setup() {
    const route = useRoute();
    // Asegúrate de que `hotelId` sea un string
    const hotelId = Array.isArray(route.params.id) ? route.params.id[0] : route.params.id;

    return {
      hotelId
    };
  }
});
</script>
