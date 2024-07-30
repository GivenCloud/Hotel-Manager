<script lang="ts" setup>
import { ref, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import axios from 'axios';
import { type Hotel } from '@/types/hotel';
import { useToast } from 'primevue/usetoast';

const route = useRoute();
const serviceId = route.params.id as string;
const toast = useToast();

// State for search bar
const searchQuery = ref<string>('');
const filteredHotels = ref<Hotel[]>([]);
const allHotels = ref<Hotel[]>([]);
const selectedHotel = ref<Hotel | null>(null);
const autoCompleteClass = ref<string>('');
const showResults = ref<boolean>(false);

// State for assigned hotels table
const assignedHotels = ref<Hotel[]>([]);

// Function to fetch all hotels from the API
const fetchHotels = async () => {
  try {
    const response = await axios.get('http://hotel-manager.test/api/hotel');
    allHotels.value = response.data;
    return response.data;
  } catch (error) {
    console.error('Error fetching hotels:', error);
    return [];
  }
};

// Function to fetch hotels assigned to a specific service
const fetchAssignedHotels = async (serviceId: string) => {
  try {
    const response = await axios.get(`http://hotel-manager.test/api/service/${serviceId}/hotels`);
    return response.data;
  } catch (error) {
    console.error(`Error fetching assigned hotels for service ${serviceId}:`, error);
    return [];
  }
};

// Function to assign a hotel to a service
const assignHotel = async (hotel: Hotel | null) => {
  if (!hotel) return;

  // Check if the hotel is already assigned
  if (assignedHotels.value.some((h: Hotel) => h.id === hotel.id)) {
    autoCompleteClass.value = 'p-invalid';
    toast.add({ severity: 'error', summary: 'Error', detail: 'The hotel is already assigned', life: 3000 });
    return;
  }

  try {
    await axios.post(`http://hotel-manager.test/api/service/${serviceId}/hotels`, {
      service_id: serviceId,
      hotel_id: [hotel.id],
    });
    assignedHotels.value.push(hotel);
    selectedHotel.value = null;
    searchQuery.value = '';
    filteredHotels.value = [];
    autoCompleteClass.value = '';
  } catch (error) {
    console.error('Error assigning hotel:', error);
  }
};

// Function to remove an assigned hotel
const removeHotel = async (hotel: Hotel) => {
  try {
    await axios.delete(`http://hotel-manager.test/api/service/${serviceId}/hotels`, {
      data: {
        service_id: serviceId,
        hotel_id: [hotel.id],
      },
    });
    assignedHotels.value = assignedHotels.value.filter((h: Hotel) => h.id !== hotel.id);
  } catch (error) {
    console.error('Error removing hotel:', error);
  }
};

// Autocomplete hotels based on search
const searchHotels = async (event: any) => {
  const query = event.query as string;
  filteredHotels.value = allHotels.value.filter((hotel: Hotel) => hotel.name.toLowerCase().includes(query.toLowerCase()));
};

// Handle key press event for search bar
const handleKeyEnter = async (event: KeyboardEvent) => {
  if (event.key === 'Enter' && filteredHotels.value.length > 0) {
    await assignHotel(filteredHotels.value[0]);
  }
};

// Toggle results bar visibility
const toggleResultsBar = () => {
  showResults.value = !showResults.value;
  if (!showResults.value) {
    // Reset filtered hotels when hiding results
    filteredHotels.value = [];
  } else {
    // Show all hotels when displaying results
    filteredHotels.value = [...allHotels.value];
  }
};

// Handle click on a hotel to assign it
const handleHotelClick = async (hotel: Hotel) => {
  await assignHotel(hotel);
};

// Get hotels and assigned hotels on component mount
onMounted(async () => {
  allHotels.value = await fetchHotels();
  assignedHotels.value = await fetchAssignedHotels(serviceId);
});
</script>

<template>
  <div class="grid p-fluid">
    <div class="col-6 md:col-6">
      <div class="card">
        <h3>Search and Assign Hotels</h3>
        <div class="p-inputgroup">
          <AutoComplete
            v-model="searchQuery"
            :suggestions="filteredHotels"
            field="name"
            @complete="searchHotels"
            @select="assignHotel"
            placeholder="Search hotels"
            @keydown="handleKeyEnter"
            :class="autoCompleteClass"
            class="w-full"
          />
          <Button
            icon="pi pi-plus"
            class="p-button-sm p-button-success"
            @click="assignHotel(filteredHotels.length > 0 ? filteredHotels[0] : null)"
          />
          <Button
            icon="pi pi-search"
            class="p-button-sm p-button-info"
            @click="toggleResultsBar"
          />
        </div>
        <div v-if="showResults" class="p-mt-2 results-container">
          <ul class="p-list">
            <li v-for="hotel in filteredHotels" :key="hotel.id" @click="handleHotelClick(hotel)">
              {{ hotel.name }}
            </li>
          </ul>
        </div>
      </div>
    </div>
    <div class="col-6">
      <div class="card">
        <h3>Assigned Hotels</h3>
        <DataTable :value="assignedHotels" responsiveLayout="scroll">
          <Column field="name" header="Name"></Column>
          <Column header="Actions">
            <template #body="slotProps">
              <Button
                icon="pi pi-trash"
                class="p-button-rounded p-button-danger"
                @click="removeHotel(slotProps.data)"
              />
            </template>
          </Column>
        </DataTable>
      </div>
    </div>
    <Toast />
  </div>
</template>

<style scoped>
.p-invalid .p-autocomplete {
  border: 1px solid red;
}
.p-list {
  list-style: none;
  padding: 0;
  margin: 0;
}
.p-list li {
  padding: 8px;
  border-bottom: 1px solid #ddd;
  cursor: pointer;
}
.p-list li:hover {
  background-color: #f0f0f0;
}
.results-container {
  max-height: 300px; /* Adjust height as needed */
  overflow-y: auto;
}
</style>
