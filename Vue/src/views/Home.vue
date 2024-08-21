<script lang="ts" setup>
import { ref, onMounted } from 'vue';
import { useToast } from 'primevue/usetoast';
import { useHotel } from '@/service/useHotel';

const toast = useToast();
const dataviewValue = ref(null);
const layout = ref('grid');
const sortKey = ref(null);
const sortOrder = ref(null);
const sortField = ref(null);
const sortOptions = ref([
    { label: 'Stars High to Low', value: '!stars' },
    { label: 'Stars Low to High', value: 'stars' }
]);

const useHotels = new useHotel();

// Estado de carga para evitar mostrar contenido hasta que los datos estén listos
const loading = ref(true);

onMounted(async () => {
    try {
        dataviewValue.value = await useHotels.getHotels();
    } catch (error) {
        toast.add({ severity: 'error', summary: 'Error', detail: 'Failed to load hotels', life: 3000 });
    } finally {
        loading.value = false; // Se termina la carga
    }
});

interface SortOption {
    label: string;
    value: string;
}

const onSortChange = (event: { value: SortOption }) => {
    const value: string = event.value.value;
    const sortValue: SortOption = event.value;

    if (value.indexOf('!') === 0) {
        sortOrder.value = -1;
        sortField.value = value.substring(1, value.length);
        sortKey.value = sortValue;
    } else {
        sortOrder.value = 1;
        sortField.value = value;
        sortKey.value = sortValue;
    }
};
</script>

<template>
    <div class="grid">
        <div class="col-12">
            <div class="card">
                <!-- Mostrar un spinner o mensaje de carga mientras loading sea true -->
                <div v-if="loading" class="flex justify-center items-center">
                    <p>Loading hotels...</p>
                </div>

                <!-- Mostrar el contenido solo si loading es false -->
                <div v-else>
                    <DataView :value="dataviewValue" :layout="layout" :paginator="true" :rows="9" :sortOrder="sortOrder" :sortField="sortField">
                        <template #header>
                            <h5 class="justify-center text-center text-3xl">Hotels</h5>
                            <div class="grid grid-nogutter">
                                <div class="col-6 text-left">
                                    <Dropdown v-model="sortKey" :options="sortOptions" optionLabel="label" placeholder="Sort By Stars" @change="onSortChange($event)" />
                                </div>
                                <div class="col-6 text-right">
                                    <DataViewLayoutOptions v-model="layout" />
                                </div>
                            </div>
                        </template>

                        <template #list="slotProps">
                            <div class="grid grid-nogutter">
                                <div v-for="(item, index) in slotProps.items" :key="index" class="col-12">
                                    <router-link :to="`/items/hotel/${item.id}`">
                                        <div class="flex flex-column sm:flex-row sm:align-items-center p-4 gap-3 transition-transform transform hover:scale-105 hover:shadow-lg" :class="{ 'border-top-1 surface-border': index !== 0 }">
                                            <div class="md:w-10rem relative">
                                                <img class="block xl:block mx-auto border-round w-full" :src="`${item.image}`" :alt="item.name" />
                                            </div>
                                            <div class="flex flex-column md:flex-row justify-content-between md:align-items-center flex-1 gap-4">
                                                <div class="flex flex-row md:flex-column justify-content-between align-items-start gap-2">
                                                    <div>
                                                        <span class="font-medium text-secondary text-sm">{{ item.address }}</span>
                                                        <div class="text-lg font-medium text-900 mt-2">{{ item.name }}</div>
                                                    </div>
                                                    <div class="surface-100 p-1" style="border-radius: 30px">
                                                        <div class="surface-0 flex align-items-center gap-2 justify-content-center py-1 px-2" style="border-radius: 30px; box-shadow: 0px 1px 2px 0px rgba(0, 0, 0, 0.04), 0px 1px 2px 0px rgba(0, 0, 0, 0.06)">
                                                            <span class="text-900 font-medium text-sm">{{ item.stars }}</span>
                                                            <i class="pi pi-star-fill text-yellow-500"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="flex flex-column md:align-items-end gap-5">
                                                </div>
                                            </div>
                                        </div>
                                    </router-link>
                                </div>
                            </div>
                        </template>

                        <template #grid="slotProps">
                            <div class="grid grid-nogutter">
                                <div v-for="(item, index) in slotProps.items" :key="index" class="col-12 sm:col-6 md:col-4 p-2">
                                    <router-link :to="`/items/hotel/${item.id}`">
                                        <div class="p-4 border-1 surface-border surface-card border-round flex flex-column transition-transform transform hover:scale-105 hover:shadow-lg h-full">
                                            <div class="surface-50 flex justify-content-center border-round p-3">
                                                <div class="relative mx-auto">
                                                    <img class="border-round w-full" :src="`${item.image}`" :alt="item.name" style="max-width: 300px" />
                                                </div>
                                            </div>
                                            <div class="pt-4">
                                                <div class="flex flex-row justify-content-between align-items-start gap-2">
                                                    <div>
                                                        <span class="font-medium text-secondary text-sm">{{ item.address }}</span>
                                                        <div class="text-lg font-medium text-900 mt-1">{{ item.name }}</div>
                                                    </div>
                                                    <div class="surface-100 p-1" style="border-radius: 30px">
                                                        <div class="surface-0 flex align-items-center gap-2 justify-content-center py-1 px-2" style="border-radius: 30px; box-shadow: 0px 1px 2px 0px rgba(0, 0, 0, 0.04), 0px 1px 2px 0px rgba(0, 0, 0, 0.06)">
                                                            <span class="text-900 font-medium text-sm">{{ item.stars }}</span>
                                                            <i class="pi pi-star-fill text-yellow-500"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="flex flex-column gap-4 mt-4">
                                                </div>
                                            </div>
                                        </div>
                                    </router-link>
                                </div>
                            </div>
                        </template>
                    </DataView>
                </div>
            </div>
        </div>
    </div>
</template>
