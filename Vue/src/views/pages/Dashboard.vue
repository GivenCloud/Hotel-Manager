<script lang="ts" setup>
import { useToast } from 'primevue/usetoast';
import { useHotel } from '@/service/useHotel';
import { useGuest } from '@/service/useGuest';
import { useRoom } from '@/service/useRoom';
import { useType } from '@/service/useType';
import { useService } from '@/service/useService';
import { useCategory } from '@/service/useCategory';
import { type Hotel } from '@/types/hotel';
import { type Guest } from '@/types/guest';
import { type Room } from '@/types/room';
import { type Type } from '@/types/type';
import { type Service } from '@/types/service';
import { type Category } from '@/types/category';
import { onMounted, ref } from 'vue';
import { Line } from 'vue-chartjs';
import { Chart as ChartJS, Title, Tooltip, Legend, LineElement, PointElement, CategoryScale, LinearScale } from 'chart.js';

ChartJS.register(Title, Tooltip, Legend, LineElement, PointElement, CategoryScale, LinearScale);

const toast = useToast();
const useHotels = new useHotel();
const useGuests = new useGuest();
const useRooms = new useRoom();
const useTypes = new useType();
const useServices = new useService();
const useCategories = new useCategory();

const hotels = ref<Hotel[]>([]);
const guests = ref<Guest[]>([]);
const rooms = ref<Room[]>([]);
const types = ref<Type[]>([]);
const services = ref<Service[]>([]);
const categories = ref<Category[]>([]);

const selectedCategory = ref<'hotels' | 'guests' | 'rooms' | 'types' | 'services' | 'categories'>('hotels');
const categoryOptions = [
    { label: 'Hotels', value: 'hotels' },
    { label: 'Guests', value: 'guests' },
    { label: 'Rooms', value: 'rooms' },
    { label: 'Types', value: 'types' },
    { label: 'Services', value: 'services' },
    { label: 'Categories', value: 'categories' }
];

const previousCounts = ref({
    hotels: null as number | null,
    guests: null as number | null,
    rooms: null as number | null,
    types: null as number | null,
    services: null as number | null,
    categories: null as number | null,
});

const differences = ref({
    hotels: null as number | null,
    guests: null as number | null,
    rooms: null as number | null,
    types: null as number | null,
    services: null as number | null,
    categories: null as number | null,
});

const differenceColors = ref({
    hotels: '',
    guests: '',
    rooms: '',
    types: '',
    services: '',
    categories: '',
});

// Mapeo de íconos
const iconMap = {
    hotels: 'pi pi-building',
    guests: 'pi pi-users',
    rooms: 'pi pi-box',
    types: 'pi pi-list',
    services: 'pi pi-table',
    categories: 'pi pi-tag'
};

// Mapeo de colores
const colorMap = {
    hotels: 'bg-blue-100 text-blue-500',
    guests: 'bg-green-100 text-green-500',
    rooms: 'bg-red-100 text-red-500',
    types: 'bg-yellow-100 text-yellow-500',
    services: 'bg-cyan-100 text-cyan-500',
    categories: 'bg-purple-100 text-purple-500'
};

const getPreviousCount = (key: string): number | null => {
    const count = localStorage.getItem(key);
    return count ? parseInt(count, 10) : null;
};

const setPreviousCount = (key: string, count: number) => {
    localStorage.setItem(key, count.toString());
};

const calculateDifference = (current: number, previous: number | null): { difference: number, color: string } => {
    let diff = 0;
    let color = 'text-gray-500'; // Gris para sin cambio

    if (previous !== null) {
        diff = current - previous;
        color = diff > 0 ? 'text-green-500' : (diff < 0 ? 'text-red-500' : 'text-gray-500');
    }

    return { difference: diff, color };
};

const updateHistoricalData = (key: string, count: number) => {
    const historicalData = localStorage.getItem(`${key}HistoricalData`);
    let data = historicalData ? JSON.parse(historicalData) : [];
    const today = new Date().toISOString().split('T')[0]; // YYYY-MM-DD

    // Añadir el dato de hoy si no existe
    if (!data.some((entry: { date: string }) => entry.date === today)) {
        data.push({ date: today, count });
    } else {
        // Actualizar el dato de hoy si ya existe
        data = data.map((entry: { date: string, count: number }) => entry.date === today ? { date: today, count } : entry);
    }

    localStorage.setItem(`${key}HistoricalData`, JSON.stringify(data));
};

const loadData = async () => {
    try {
        const [currentHotels, currentGuests, currentRooms, currentTypes, currentServices, currentCategories] = await Promise.all([
            useHotels.getHotels(),
            useGuests.getGuests(),
            useRooms.getRooms(),
            useTypes.getTypes(),
            useServices.getServices(),
            useCategories.getCategories()
        ]);

        hotels.value = currentHotels;
        guests.value = currentGuests;
        rooms.value = currentRooms;
        types.value = currentTypes;
        services.value = currentServices;
        categories.value = currentCategories;

        const keys = ['hotels', 'guests', 'rooms', 'types', 'services', 'categories'] as const;
        keys.forEach(key => {
            const current = eval(`${key}.value.length`);
            const previous = getPreviousCount(`previous${key.charAt(0).toUpperCase() + key.slice(1)}Count`);
            const result = calculateDifference(current, previous);

            differences.value[key] = result.difference;
            differenceColors.value[key] = result.color;

            setPreviousCount(`previous${key.charAt(0).toUpperCase() + key.slice(1)}Count`, current);
            updateHistoricalData(key, current); // Actualizar datos históricos
        });
    } catch (error) {
        toast.add({ severity: 'error', summary: 'Error', detail: 'Failed to load data', life: 3000 });
    }
};

const getDataForChart = () => {
    const keys = ['hotels', 'guests', 'rooms', 'types', 'services', 'categories'] as const;
    const dataMap = {} as { [key: string]: { dates: string[], counts: number[] } };

    keys.forEach(key => {
        const historicalData = localStorage.getItem(`${key}HistoricalData`);
        const data = historicalData ? JSON.parse(historicalData) : [];
        dataMap[key] = {
            dates: data.map((entry: { date: string }) => entry.date),
            counts: data.map((entry: { count: number }) => entry.count)
        };
    });

    const selectedData = dataMap[selectedCategory.value];

    return {
        labels: selectedData.dates,
        datasets: [{
            label: `${selectedCategory.value.charAt(0).toUpperCase() + selectedCategory.value.slice(1)} Count Over Time`,
            data: selectedData.counts,
            fill: false,
            borderColor: '#42A5F5',
            tension: 0.1
        }]
    };
};

const chartOptions = {
    responsive: true,
    plugins: {
        legend: {
            position: 'top' as const,
        },
        tooltip: {
            callbacks: {
                label: function(context: any) {
                    let label = context.dataset.label || '';
                    if (label) {
                        label += ': ';
                    }
                    if (context.parsed.y !== null) {
                        label += context.parsed.y;
                    }
                    return label;
                }
            }
        }
    },
    scales: {
        y: {
            beginAtZero: true,
            ticks: {
                precision: 0,
                stepSize: 1
            }
        }
    }
};

onMounted(() => {
    loadData();
});
</script>

<template>
    <div>
        <div class="grid">
            <div class="col-12 lg:col-6 xl:col-3" v-for="(data, key) in {
                hotels: hotels,
                guests: guests,
                rooms: rooms,
                types: types,
                services: services,
                categories: categories
            }" :key="key">
                <div class="card mb-0">
                    <template v-if="data">
                        <div class="flex justify-content-between mb-3">
                            <div>
                                <span class="block text-500 font-medium mb-3">{{ key.charAt(0).toUpperCase() + key.slice(1) }}</span>
                                <div class="text-900 font-medium text-xl">
                                    {{ data.length }}
                                </div>
                            </div>
                            <div :class="colorMap[key] + ' flex align-items-center justify-content-center border-round'" style="width: 2.5rem; height: 2.5rem">
                                <i :class="iconMap[key]" class="text-xl"></i>
                            </div>
                        </div>
                        <div v-if="differences[key] !== null" :class="differenceColors[key]" class="text-sm">
                            <template v-if="differences[key] > 0">
                                {{ differences[key] }} {{ differences[key] === 1 ? `new ${key.slice(0, -1)}` : `new ${key}` }}
                            </template>
                            <template v-else-if="differences[key] < 0">
                                {{ Math.abs(differences[key]) }} {{ Math.abs(differences[key]) === 1 ? `less ${key.slice(0, -1)}` : `less ${key}` }}
                            </template>
                            <template v-else>
                                No change
                            </template>
                        </div>
                    </template>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="p-d-flex p-jc-between p-ai-center mb-4 mt-5 ml-1">
            <span class="p-text-bold mr-2">Select Category:</span>
            <Dropdown
                v-model:modelValue="selectedCategory"
                :options="categoryOptions"
                option-label="label"
                option-value="value"
                class="w-12rem"
            />
        </div>
        <div class="p-d-flex p-jc-center mt-4">
            <div style="width: 100%; max-width: 600px;">
                <Line :data="getDataForChart()" :options="chartOptions" />
            </div>
        </div>
        </div>
    </div>
</template>

<style scoped>
.p-text-bold {
    font-weight: bold;
}
</style>
