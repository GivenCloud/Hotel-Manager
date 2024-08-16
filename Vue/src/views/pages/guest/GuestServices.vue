<script lang="ts" setup>
import { ref, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import apiClient from 'axios';
import { type Service } from '@/types/service';
import { useToast } from 'primevue/usetoast';

const route = useRoute();
const guestId = route.params.id as string;
const toast = useToast();

// State for search bar
const searchQuery = ref<string>('');
const filteredServices = ref<Service[]>([]);
const allServices = ref<Service[]>([]);
const selectedService = ref<Service | null>(null);
const autoCompleteClass = ref<string>('');
const showResults = ref<boolean>(false);

// State for assigned services table
const assignedServices = ref<Service[]>([]);

// Function to fetch all services from the API
const fetchServices = async () => {
  try {
    const response = await apiClient.get('http://hotel-manager.test/api/service', {
      headers: {
        Authorization: `Bearer ${localStorage.getItem('token')}` // Include the token in the header
      }
    });
    allServices.value = response.data;
    return response.data;
  } catch (error) {
    console.error('Error fetching services:', error);
    return [];
  }
};

// Function to fetch services assigned to a specific guest
const fetchAssignedServices = async (guestId: string) => {
  try {
    const response = await apiClient.get(`http://hotel-manager.test/api/guest/${guestId}/services`, {
      headers: {
        Authorization: `Bearer ${localStorage.getItem('token')}` // Include the token in the header
      }
    });
    return response.data;
  } catch (error) {
    console.error(`Error fetching assigned services for guest ${guestId}:`, error);
    return [];
  }
};

// Function to assign a service to a guest
const assignService = async (service: Service | null) => {
  if (!service) return;

  // Check if the service is already assigned
  if (assignedServices.value.some((g: Service) => g.id === service.id)) {
    autoCompleteClass.value = 'p-invalid';
    toast.add({ severity: 'error', summary: 'Error', detail: 'The service is already assigned', life: 3000 });
    return;
  }

  try {
    await apiClient.post(`http://hotel-manager.test/api/guest/${guestId}/services`, {
      guest_id: guestId,
      service_id: [service.id],
    }, {
      headers: {
        Authorization: `Bearer ${localStorage.getItem('token')}` // Include the token in the header
      }
    });
    assignedServices.value.push(service);
    selectedService.value = null;
    searchQuery.value = '';
    filteredServices.value = [];
    autoCompleteClass.value = '';
  } catch (error) {
    console.error('Error assigning service:', error);
  }
};

// Function to remove an assigned service
const removeService = async (service: Service) => {
  try {
    await apiClient.delete(`http://hotel-manager.test/api/guest/${guestId}/services`, {
      data: {
        guest_id: guestId,
        service_id: [service.id],
      },
      headers: {
        Authorization: `Bearer ${localStorage.getItem('token')}` // Include the token in the header
      }
    });
    assignedServices.value = assignedServices.value.filter((g: Service) => g.id !== service.id);
  } catch (error) {
    console.error('Error removing service:', error);
  }
};

// Autocomplete services based on search
const searchServices = async (event: any) => {
  const query = event.query as string;
  filteredServices.value = allServices.value.filter((service: Service) => service.name.toLowerCase().includes(query.toLowerCase()));
};

// Handle key press event for search bar
const handleKeyEnter = async (event: KeyboardEvent) => {
  if (event.key === 'Enter' && filteredServices.value.length > 0) {
    await assignService(filteredServices.value[0]);
  }
};

// Toggle results bar visibility
const toggleResultsBar = () => {
  showResults.value = !showResults.value;
  if (!showResults.value) {
    // Reset filtered services when hiding results
    filteredServices.value = [];
  } else {
    // Show all services when displaying results
    filteredServices.value = [...allServices.value];
  }
};

// Handle click on a service to assign it
const handleServiceClick = async (service: Service) => {
  await assignService(service);
};

// Get services and assigned services on component mount
onMounted(async () => {
  allServices.value = await fetchServices();
  assignedServices.value = await fetchAssignedServices(guestId);
});
</script>

<template>
  <div class="grid p-fluid">
    <div class="col-6 md:col-6">
      <div class="card">
        <h3>Search and Assign Services</h3>
        <div class="p-inputgroup">
          <AutoComplete
            v-model="searchQuery"
            :suggestions="filteredServices"
            field="name"
            @complete="searchServices"
            @select="assignService"
            placeholder="Search services"
            @keydown="handleKeyEnter"
            :class="autoCompleteClass"
            class="w-full"
          />
          <Button
            icon="pi pi-plus"
            class="p-button-sm p-button-success"
            @click="assignService(filteredServices.length > 0 ? filteredServices[0] : null)"
          />
          <Button
            icon="pi pi-search"
            class="p-button-sm p-button-info"
            @click="toggleResultsBar"
          />
        </div>
        <div v-if="showResults" class="p-mt-2 results-container">
          <ul class="p-list">
            <li v-for="service in filteredServices" :key="service.id" @click="handleServiceClick(service)">
              {{ service.name }}
            </li>
          </ul>
        </div>
      </div>
    </div>
    <div class="col-6">
      <div class="card">
        <h3>Assigned Services</h3>
        <DataTable :value="assignedServices" responsiveLayout="scroll">
          <Column field="name" header="Name"></Column>
          <Column field="description" header="Description"></Column>
          <Column header="Actions">
            <template #body="slotProps">
              <Button
                icon="pi pi-trash"
                class="p-button-rounded p-button-danger"
                @click="removeService(slotProps.data)"
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
