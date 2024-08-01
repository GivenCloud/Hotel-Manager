<script lang="ts" setup>
import { ref, onMounted, computed } from 'vue';
import { useRoute } from 'vue-router';
import { useHotel } from '@/service/useHotel';
import { type Hotel } from '@/types/hotel';
import Tag from 'primevue/tag';

const route = useRoute(); 
const hotelId = computed(() => {
    const id = route.params.id;
    return id ? Number(id) : null;
});
const hotel = ref<Hotel | null>(null);
const useHotels = new useHotel();

const services = ref<string[] | null>(null);



onMounted(async () => {
    try {
        if (hotelId.value !== null) {
            hotel.value = await useHotels.getHotelById(hotelId.value);
            services.value = await useHotels.getServicesByHotelId(hotelId.value);
        }
    } catch (error) {
        console.error('Failed to load hotel details:', error);
    }
});
</script>

<template>
    <div class="p-justify-center">
        <div class="p-col-12 p-md-8">
            <div class="p-card p-p-3">
                <template v-if="hotel">
                    <div class="flex">
                        <div class="m-5">
                            <img :src="hotel.image" alt="Hotel Image" class="hotel-image" />
                        </div>
                        <div class="mt-6 p-col-12 p-md-8 hotel-details">
                            <h2 class="hotel-name">{{ hotel.name }}</h2>
                            <div class="p-grid">
                                <div class="p-col-12">
                                    <div class="hotel-info">
                                        <i class="pi pi-map-marker icon"></i>
                                        <span>{{ hotel.address }}</span>
                                    </div>
                                    <div class="hotel-info">
                                        <i class="pi pi-star icon"></i>
                                        <span>{{ hotel.stars }} Stars</span>
                                    </div>
                                    <div class="hotel-info">
                                        <i class="pi pi-phone icon"></i>
                                        <span>{{ hotel.phone }}</span>
                                    </div>
                                    <div class="hotel-info">
                                        <i class="pi pi-envelope icon"></i>
                                        <span>{{ hotel.email }}</span>
                                    </div>
                                    <div class="hotel-info">
                                        <i class="pi pi-globe icon"></i>
                                        <span><a :href="hotel.website" target="_blank">{{ hotel.website }}</a></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div v-if="services && services.length > 0" class="m-5">
                        <strong class="text-lg">Services available:</strong>
                        <div class="ml-2 mt-2 flex">
                            <div v-for="service in services" :key="service" class="mt-1 ml-2">
                                <Tag style="font-size: 1em">{{ service.name }}</Tag>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>
</template>

<style scoped>
.hotel-image {
    width: 100%;
    max-width: 350px;
    border-radius: 8px;
    margin: 0 auto;
}

.hotel-details {
    padding-left: 20px;
    text-align: left;
}

.hotel-name {
    font-size: 1.5em;
    margin-bottom: 10px;
    font-weight: bold;
}

.hotel-info {
    display: flex;
    align-items: center;
    margin-bottom: 10px;
    font-size: 1.1em;
}

.icon {
    margin-right: 10px;
    color: #666;
    font-size: 1.2em;
}

a {
    color: #007bff;
    text-decoration: none;
}

a:hover {
    text-decoration: underline;
}
</style>
