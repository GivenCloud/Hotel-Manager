<script lang="ts" setup>
import { ref, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import apiClient from 'axios';
import { type Room } from '@/types/room';
import { useToast } from 'primevue/usetoast';
import Calendar from 'primevue/calendar';
import Button from 'primevue/button';
import AutoComplete from 'primevue/autocomplete';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Toast from 'primevue/toast';

const route = useRoute();
const guestId = route.params.id as string;
const toast = useToast();

// State for search bar
const searchQuery = ref<string>('');
const filteredRooms = ref<Room[]>([]);
const allRooms = ref<Room[]>([]);
const selectedRoom = ref<Room | null>(null);
const autoCompleteClass = ref<string>('');
const showResults = ref<boolean>(false);

// State for assigned rooms table
// const assignedRooms = ref<Room[]>([]);

// State for date inputs
const checkIn = ref<string | null>(null);
const checkOut = ref<string | null>(null);

// Function to fetch all rooms from the API
const fetchRooms = async () => {
  try {
    const response = await apiClient.get('http://hotel-manager.test/api/room', {
      headers: {
        Authorization: `Bearer ${localStorage.getItem('token')}` // Include the token in the header
      }
    });
    allRooms.value = response.data;
    return response.data;
  } catch (error) {
    console.error('Error fetching rooms:', error);
    return [];
  }
};

// // Function to fetch rooms assigned to a specific guest
// const fetchAssignedRooms = async (guestId: string) => {
//   try {
//     const response = await apiClient.get(`http://hotel-manager.test/api/guest/${guestId}/rooms`);
//     return response.data;
//   } catch (error) {
//     console.error(`Error fetching assigned rooms for guest ${guestId}:`, error);
//     return [];
//   }
// };

const fetchAssignedRooms = async (guestId: string) => {
  try {
    const response = await apiClient.get(`http://hotel-manager.test/api/guest/${guestId}/rooms`, {
      headers: {
        Authorization: `Bearer ${localStorage.getItem('token')}` // Include the token in the header
      }
    });
    return response.data; // Aquí debería incluir checkInDate y checkOutDate
  } catch (error) {
    console.error(`Error fetching assigned rooms for guest ${guestId}:`, error);
    return [];
  }
};


// const assignRoom = async (room: Room | null) => {
//   if (!room || !checkIn.value || !checkOut.value) {
//     toast.add({ severity: 'error', summary: 'Error', detail: 'Please fill in all required fields', life: 3000 });
//     return;
//   }

//   // Formatear las fechas al formato yyyy-mm-dd
//   const formattedCheckIn = new Date(checkIn.value).toISOString().split('T')[0];
//   const formattedCheckOut = new Date(checkOut.value).toISOString().split('T')[0];

//   try {
//     await apiClient.post(`http://hotel-manager.test/api/guest/${guestId}/rooms`, {
//       guest_id: guestId, 
//       room_id: [room.id],
//       checkInDate: formattedCheckIn, 
//       checkOutDate: formattedCheckOut,
//     });

//     assignedRooms.value.push(room);
//     selectedRoom.value = null;
//     searchQuery.value = '';
//     filteredRooms.value = [];
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

const assignRoom = async (room: Room | null) => {
  if (!room || !checkIn.value || !checkOut.value) {
    toast.add({ severity: 'error', summary: 'Error', detail: 'Please fill in all required fields', life: 3000 });
    return;
  }

  const formattedCheckIn = new Date(checkIn.value).toISOString().split('T')[0];
  const formattedCheckOut = new Date(checkOut.value).toISOString().split('T')[0];

  try {
    await apiClient.post(`http://hotel-manager.test/api/guest/${guestId}/rooms`, {
      guest_id: guestId,
      room_id: [room.id],
      checkInDate: formattedCheckIn,
      checkOutDate: formattedCheckOut,
    }, {
      headers: {
        Authorization: `Bearer ${localStorage.getItem('token')}` // Include the token in the header
      }
    });

    // Añadir la nueva reserva al estado
    assignedRooms.value.push({
      ...room,
      checkInDate: formattedCheckIn,
      checkOutDate: formattedCheckOut,
    });
    selectedRoom.value = null;
    searchQuery.value = '';
    filteredRooms.value = [];
    autoCompleteClass.value = '';
    checkIn.value = null;
    checkOut.value = null;
  } catch (error: any) {
    if (error.response && error.response.data.errors) {
      const errorMessages = error.response.data.errors;
      if (errorMessages.checkInDate) {
        toast.add({ severity: 'error', summary: 'Error', detail: errorMessages.checkInDate[0], life: 3000 });
      }
      if (errorMessages.checkOutDate) {
        toast.add({ severity: 'error', summary: 'Error', detail: errorMessages.checkOutDate[0], life: 3000 });
      }
      if (errorMessages.overlap) {
        toast.add({ severity: 'error', summary: 'Error', detail: errorMessages.overlap[0], life: 3000 });
      }
    } else {
      toast.add({ severity: 'error', summary: 'Error', detail: 'An unexpected error occurred.', life: 3000 });
    }
  }
};


// // Function to remove an assigned room
// const removeRoom = async (room: Room) => {
//   try {
//     await apiClient.delete(`http://hotel-manager.test/api/guest/${guestId}/rooms`, {
//       data: {
//         guest_id: guestId,
//         room_id: [room.id],
//       },
//     });
//     assignedRooms.value = assignedRooms.value.filter((r: Room) => r.id !== room.id);
//   } catch (error) {
//     console.error('Error removing room:', error);
//   }
// };

interface AssignedRoom {
  id: number;
  number: number;
  type_id: number;
  hotel_id: number;
  created_at: Date;
  updated_at: Date;
  checkInDate: string;
  checkOutDate: string;
}

const assignedRooms = ref<AssignedRoom[]>([]);


const removeRoom = async (roomId: number, checkInDate: string, checkOutDate: string) => {
  console.log(roomId, checkInDate, checkOutDate);
  try {
    await apiClient.delete(`http://hotel-manager.test/api/guest/${guestId}/rooms`, {
      data: {
        guest_id: guestId,
        room_id: [roomId],
        checkInDate,
        checkOutDate,
      },
        headers: {
          Authorization: `Bearer ${localStorage.getItem('token')}` // Include the token in the header
        }
    });
    assignedRooms.value = assignedRooms.value.filter((r: any) => !(r.id === roomId && r.checkInDate === checkInDate && r.checkOutDate === checkOutDate));
  } catch (error) {
    console.error('Error removing room:', error);
  }
};

// Autocomplete rooms based on search
const searchRooms = async (event: any) => {
  const query = event.query as string;
  filteredRooms.value = allRooms.value.filter((room: Room) => room.number.toString().includes(query));
};

// Handle key press event for search bar
const handleKeyEnter = async (event: KeyboardEvent) => {
  if (event.key === 'Enter' && filteredRooms.value.length > 0) {
    await assignRoom(filteredRooms.value[0]);
  }
};

// Toggle results bar visibility
const toggleResultsBar = () => {
  showResults.value = !showResults.value;
  if (!showResults.value) {
    // Reset filtered rooms when hiding results
    filteredRooms.value = [];
  } else {
    // Show all rooms when displaying results
    filteredRooms.value = [...allRooms.value];
  }
};

// Handle click on a room to assign it
const handleRoomClick = async (room: Room) => {
  await assignRoom(room);
};

// Get rooms and assigned rooms on component mount
onMounted(async () => {
  allRooms.value = await fetchRooms();
  assignedRooms.value = await fetchAssignedRooms(guestId);
});
</script>

<template>
  <div class="grid p-fluid">
    <div class="col-6 md:col-6">
      <div class="card">
        <h3>Search and Assign Rooms</h3>
        <div class="p-inputgroup">
          <AutoComplete
            v-model="searchQuery"
            :suggestions="filteredRooms"
            field="number"
            @complete="searchRooms"
            @select="assignRoom"
            placeholder="Search rooms"
            @keydown="handleKeyEnter"
            :class="autoCompleteClass"
            class="w-full"
          />
          <Button
            icon="pi pi-plus"
            class="p-button-sm p-button-success"
            @click="assignRoom(filteredRooms.length > 0 ? filteredRooms[0] : null)"
          />
          <Button
            icon="pi pi-search"
            class="p-button-sm p-button-info"
            @click="toggleResultsBar"
          />
        </div>
        <div v-if="showResults" class="p-mt-2 results-container">
          <ul class="p-list">
            <li v-for="room in filteredRooms" :key="room.id" @click="handleRoomClick(room)">
              {{ room.number }}
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
        <h3>Assigned Rooms</h3>
        <DataTable :value="assignedRooms" responsiveLayout="scroll">
          <Column field="number" header="Number"></Column>
          <Column field="checkInDate" header="Check In Date"></Column>
          <Column field="checkOutDate" header="Check Out Date"></Column>
          <Column header="Actions">
            <template #body="slotProps">
              <Button
                icon="pi pi-trash"
                class="p-button-rounded p-button-danger"
                @click="removeRoom(slotProps.data.id, slotProps.data.checkInDate, slotProps.data.checkOutDate)"
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
