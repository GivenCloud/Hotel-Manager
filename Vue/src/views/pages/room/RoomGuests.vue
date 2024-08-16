<script lang="ts" setup>
import { ref, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import apiClient from 'axios';
import { type Guest } from '@/types/guest';
import { useToast } from 'primevue/usetoast';
import Calendar from 'primevue/calendar';
import Button from 'primevue/button';
import AutoComplete from 'primevue/autocomplete';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Toast from 'primevue/toast';

const route = useRoute();
const roomId = route.params.id as string;
const toast = useToast();

// State for search bar
const searchQuery = ref<string>('');
const filteredGuests = ref<Guest[]>([]);
const allGuests = ref<Guest[]>([]);
const selectedGuest = ref<Guest | null>(null);
const autoCompleteClass = ref<string>('');
const showResults = ref<boolean>(false);

// State for assigned guests table
// const assignedGuests = ref<Guest[]>([]);

// State for date inputs
const checkIn = ref<string | null>(null);
const checkOut = ref<string | null>(null);

// Function to fetch all guests from the API
const fetchGuests = async () => {
  try {
    const response = await apiClient.get('http://hotel-manager.test/api/guest', {
      headers: {
        Authorization: `Bearer ${localStorage.getItem('token')}` // Include the token in the header
      }
    });
    allGuests.value = response.data;
    return response.data;
  } catch (error) {
    console.error('Error fetching guests:', error);
    return [];
  }
};

// // Function to fetch guests assigned to a specific room
// const fetchAssignedGuests = async (roomId: string) => {
//   try {
//     const response = await apiClient.get(`http://hotel-manager.test/api/room/${roomId}/guests`);
//     return response.data;
//   } catch (error) {
//     console.error(`Error fetching assigned guests for room ${roomId}:`, error);
//     return [];
//   }
// };

const fetchAssignedGuests = async (roomId: string) => {
  try {
    const response = await apiClient.get(`http://hotel-manager.test/api/room/${roomId}/guests`, {
      headers: {
        Authorization: `Bearer ${localStorage.getItem('token')}` // Include the token in the header
      }
    });
    return response.data; // Aquí debería incluir checkInDate y checkOutDate
  } catch (error) {
    console.error(`Error fetching assigned guests for room ${roomId}:`, error);
    return [];
  }
};


// const assignGuest = async (guest: Guest | null) => {
//   if (!guest || !checkIn.value || !checkOut.value) {
//     toast.add({ severity: 'error', summary: 'Error', detail: 'Please fill in all required fields', life: 3000 });
//     return;
//   }

//   // Formatear las fechas al formato yyyy-mm-dd
//   const formattedCheckIn = new Date(checkIn.value).toISOString().split('T')[0];
//   const formattedCheckOut = new Date(checkOut.value).toISOString().split('T')[0];

//   try {
//     await apiClient.post(`http://hotel-manager.test/api/room/${roomId}/guests`, {
//       room_id: roomId, 
//       guest_id: [guest.id],
//       checkInDate: formattedCheckIn, 
//       checkOutDate: formattedCheckOut,
//     });

//     assignedGuests.value.push(guest);
//     selectedGuest.value = null;
//     searchQuery.value = '';
//     filteredGuests.value = [];
//     autoCompleteClass.value = '';
//     checkIn.value = null;
//     checkOut.value = '';
//   } catch (error: any) {
//     if (error.response && error.response.data.errors) {
//       const errorMessages = error.response.data.errors;
//       // Mostrar errores específicos para checkInDate y checkOutDate
//       if (errorMessages.checkInDate) {
//         toast.add({ severity: 'error', summary: 'Error', detail: errorMessages.checkInDate[0], life: 3000 });
//       }
//       if (errorMessages.checkOutDate) {
//         toast.add({ severity: 'error', summary: 'Error', detail: errorMessages.checkOutDate[0], life: 3000 });
//       }
//       // Mostrar error de solapamiento si está presente
//       if (errorMessages.overlap) {
//         toast.add({ severity: 'error', summary: 'Error', detail: errorMessages.overlap[0], life: 3000 });
//       }
//     } else {
//       toast.add({ severity: 'error', summary: 'Error', detail: 'An unexpected error occurred.', life: 3000 });
//     }
//   }
// };

const assignGuest = async (guest: Guest | null) => {
  if (!guest || !checkIn.value || !checkOut.value) {
    toast.add({ severity: 'error', summary: 'Error', detail: 'Please fill in all required fields', life: 3000 });
    return;
  }

  const formattedCheckIn = new Date(checkIn.value).toISOString().split('T')[0];
  const formattedCheckOut = new Date(checkOut.value).toISOString().split('T')[0];

  // Validar que la fecha de check-out sea después de la fecha de check-in
  if (new Date(checkIn.value) >= new Date(checkOut.value)) {
    toast.add({ severity: 'error', summary: 'Error', detail: 'Check-out date must be after check-in date', life: 3000 });
    return;
  }

  try {
    await apiClient.post(`http://hotel-manager.test/api/room/${roomId}/guests`, {
      room_id: roomId,
      guest_id: [guest.id],
      checkInDate: formattedCheckIn,
      checkOutDate: formattedCheckOut,
    }, {
      headers: {
        Authorization: `Bearer ${localStorage.getItem('token')}` // Include the token in the header
      }
    });

    // Añadir la nueva reserva al estado
    assignedGuests.value.push({
      ...guest,
      checkInDate: formattedCheckIn,
      checkOutDate: formattedCheckOut,
    });
    selectedGuest.value = null;
    searchQuery.value = '';
    filteredGuests.value = [];
    autoCompleteClass.value = '';
    checkIn.value = null;
    checkOut.value = null;
  } catch (error: any) {
    // Mostrar detalles del error en la consola
    console.error('Error details:', error.response ? error.response.data : error.message);

    // Manejo de errores específicos y detallados
    if (error.response && error.response.data.errors) {
      const errorMessages = error.response.data.errors;

      // Manejo cuando errors es un array de strings
      if (Array.isArray(errorMessages)) {
        errorMessages.forEach((message: string) => {
          toast.add({
            severity: 'error',
            summary: 'Error',
            detail: message,
            life: 3000,
          });
        });
      } else {
        // Manejo de solapamiento de fechas
        if (errorMessages.overlap) {
          errorMessages.overlap.forEach((message: string) => {
            toast.add({
              severity: 'error',
              summary: 'Error',
              detail: message,
              life: 3000,
            });
          });
        }

        // Manejo de otros errores
        if (errorMessages.checkInDate) {
          errorMessages.checkInDate.forEach((message: string) => {
            toast.add({
              severity: 'error',
              summary: 'Error',
              detail: message,
              life: 3000,
            });
          });
        }
        if (errorMessages.checkOutDate) {
          errorMessages.checkOutDate.forEach((message: string) => {
            toast.add({
              severity: 'error',
              summary: 'Error',
              detail: message,
              life: 3000,
            });
          });
        }
      }
    } else if (error.response && error.response.data.message) {
      const errorMessage = error.response.data.message;
      toast.add({ severity: 'error', summary: 'Error', detail: errorMessage, life: 3000 });
    } else {
      toast.add({ severity: 'error', summary: 'Error', detail: 'An unexpected error occurred.', life: 3000 });
    }
  }
};





// // Function to remove an assigned guest
// const removeGuest = async (guest: Guest) => {
//   try {
//     await apiClient.delete(`http://hotel-manager.test/api/room/${roomId}/guests`, {
//       data: {
//         room_id: roomId,
//         guest_id: [guest.id],
//       },
//     });
//     assignedGuests.value = assignedGuests.value.filter((r: Guest) => r.id !== guest.id);
//   } catch (error) {
//     console.error('Error removing guest:', error);
//   }
// };



interface AssignedGuest {
  id:          number;
  name:        string;
  lastName:    string;
  dniPassport: string;
  email:       string;
  phone:       number;
  created_at:  Date;
  updated_at:  Date;
  checkInDate: string;
  checkOutDate: string;
}

const assignedGuests = ref<AssignedGuest[]>([]);


const removeGuest = async (guestId: number, checkInDate: string, checkOutDate: string) => {
  console.log(guestId, checkInDate, checkOutDate);
  try {
    await apiClient.delete(`http://hotel-manager.test/api/room/${roomId}/guests`, {
      data: {
        room_id: roomId,
        guest_id: [guestId],
        checkInDate,
        checkOutDate,
      },
      headers: {
        Authorization: `Bearer ${localStorage.getItem('token')}` // Include the token in the header
      }
    });
    assignedGuests.value = assignedGuests.value.filter((r: any) => !(r.id === guestId && r.checkInDate === checkInDate && r.checkOutDate === checkOutDate));
  } catch (error) {
    console.error('Error removing guest:', error);
  }
};

// Autocomplete guests based on search
const searchGuests = async (event: any) => {
  const query = event.query as string;
  filteredGuests.value = allGuests.value.filter((guest: Guest) => guest.name.includes(query));
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
  assignedGuests.value = await fetchAssignedGuests(roomId);
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
        <div class="p-field p-mt-2 mt-3">
          <label for="checkIn">Check In</label>
          <i class="pi pi-calendar ml-1" />
          <Calendar id="checkIn" v-model="checkIn" dateFormat="yy-mm-dd" required placeholder="Select check in date"/>
        </div>
        <div class="p-field p-mt-2 mt-3">
          <label for="checkOut">Check Out</label>
          <i class="pi pi-calendar ml-1" />
          <Calendar id="checkOut" v-model="checkOut" dateFormat="yy-mm-dd" required placeholder="Select check out date" />
        </div>
      </div>
    </div>
    <div class="col-6">
      <div class="card">
        <h3>Assigned Guests</h3>
        <DataTable :value="assignedGuests" responsiveLayout="scroll">
          <Column field="name" header="Name"></Column>
          <Column field="checkInDate" header="Check In Date"></Column>
          <Column field="checkOutDate" header="Check Out Date"></Column>
          <Column header="Actions">
            <template #body="slotProps">
              <Button
                icon="pi pi-trash"
                class="p-button-rounded p-button-danger"
                @click="removeGuest(slotProps.data.id, slotProps.data.checkInDate, slotProps.data.checkOutDate)"
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
