<script lang="ts" setup>
import { ref, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import axios from 'axios';
import { type Guest } from '@/types/guest';
import { useToast } from 'primevue/usetoast';

const route = useRoute();
const serviceId = route.params.id as string;
const toast = useToast();

// State for search bar
const searchQuery = ref<string>('');
const filteredGuests = ref<Guest[]>([]);
const allGuests = ref<Guest[]>([]);
const selectedGuest = ref<Guest | null>(null);
const autoCompleteClass = ref<string>('');
const showResults = ref<boolean>(false);

// State for assigned guests table
const assignedGuests = ref<Guest[]>([]);

// Function to fetch all guests from the API
const fetchGuests = async () => {
  try {
    const response = await axios.get('http://hotel-manager.test/api/guest');
    allGuests.value = response.data;
    return response.data;
  } catch (error) {
    console.error('Error fetching guests:', error);
    return [];
  }
};

// Function to fetch guests assigned to a specific service
const fetchAssignedGuests = async (serviceId: string) => {
  try {
    const response = await axios.get(`http://hotel-manager.test/api/service/${serviceId}/guests`);
    return response.data;
  } catch (error) {
    console.error(`Error fetching assigned guests for service ${serviceId}:`, error);
    return [];
  }
};

// Function to assign a guest to a service
const assignGuest = async (guest: Guest | null) => {
  if (!guest) return;

  // Check if the guest is already assigned
  if (assignedGuests.value.some((g: Guest) => g.id === guest.id)) {
    autoCompleteClass.value = 'p-invalid';
    toast.add({ severity: 'error', summary: 'Error', detail: 'The guest is already assigned', life: 3000 });
    return;
  }

  try {
    await axios.post(`http://hotel-manager.test/api/service/${serviceId}/guests`, {
      service_id: serviceId,
      guest_id: [guest.id],
    });
    assignedGuests.value.push(guest);
    selectedGuest.value = null;
    searchQuery.value = '';
    filteredGuests.value = [];
    autoCompleteClass.value = '';
  } catch (error) {
    console.error('Error assigning guest:', error);
  }
};

// Function to remove an assigned guest
const removeGuest = async (guest: Guest) => {
  try {
    await axios.delete(`http://hotel-manager.test/api/service/${serviceId}/guests`, {
      data: {
        service_id: serviceId,
        guest_id: [guest.id],
      },
    });
    assignedGuests.value = assignedGuests.value.filter((g: Guest) => g.id !== guest.id);
  } catch (error) {
    console.error('Error removing guest:', error);
  }
};

// Autocomplete guests based on search
const searchGuests = async (event: any) => {
  const query = event.query as string;
  filteredGuests.value = allGuests.value.filter((guest: Guest) => guest.name.toLowerCase().includes(query.toLowerCase()));
};

// Handle key press event for search bar
const handleKeyEnter = async (event: KeyboardEvent) => {
  if (event.key === 'Enter' && filteredGuests.value.length > 0) {
    await assignGuest(filteredGuests.value[0]);
  }
};

// Toggle results bar visibility
const toggleResultsBar = () => {
  showResults.value = !showResults.value;
  if (!showResults.value) {
    // Reset filtered guests when hiding results
    filteredGuests.value = [];
  } else {
    // Show all guests when displaying results
    filteredGuests.value = [...allGuests.value];
  }
};

// Handle click on a guest to assign it
const handleGuestClick = async (guest: Guest) => {
  await assignGuest(guest);
};

// Get guests and assigned guests on component mount
onMounted(async () => {
  allGuests.value = await fetchGuests();
  assignedGuests.value = await fetchAssignedGuests(serviceId);
});
</script>

<template>
  <div class="grid p-fluid">
    <div class="col-6 md:col-6">
      <div class="card">
        <h3>Search and Assign Guests</h3>
        <div class="p-inputgroup">
          <AutoComplete
            v-model="searchQuery"
            :suggestions="filteredGuests"
            field="name"
            @complete="searchGuests"
            @select="assignGuest"
            placeholder="Search guests"
            @keydown="handleKeyEnter"
            :class="autoCompleteClass"
            class="w-full"
          />
          <Button
            icon="pi pi-plus"
            class="p-button-sm p-button-success"
            @click="assignGuest(filteredGuests.length > 0 ? filteredGuests[0] : null)"
          />
          <Button
            icon="pi pi-search"
            class="p-button-sm p-button-info"
            @click="toggleResultsBar"
          />
        </div>
        <div v-if="showResults" class="p-mt-2 results-container">
          <ul class="p-list">
            <li v-for="guest in filteredGuests" :key="guest.id" @click="handleGuestClick(guest)">
              {{ guest.name }}
            </li>
          </ul>
        </div>
      </div>
    </div>
    <div class="col-6">
      <div class="card">
        <h3>Assigned Guests</h3>
        <DataTable :value="assignedGuests" responsiveLayout="scroll">
          <Column field="name" header="Name"></Column>
          <Column header="Actions">
            <template #body="slotProps">
              <Button
                icon="pi pi-trash"
                class="p-button-rounded p-button-danger"
                @click="removeGuest(slotProps.data)"
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
