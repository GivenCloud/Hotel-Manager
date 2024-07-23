<template>
  <div class="bg-gray-800 min-h-screen flex items-center justify-center">
    <div class="bg-gray-900 text-white p-8 rounded-lg shadow-lg w-full max-w-md">
      <h1 class="text-2xl font-bold mb-6">{{ formTitle }}</h1>
      <form @submit.prevent="handleSubmit" class="space-y-4">
        <div>
          <label for="name" class="block text-sm font-medium">Name</label>
          <input
            type="text"
            id="name"
            v-model="hotel.name"
            class="mt-1 block w-full bg-gray-700 text-white border border-gray-600 rounded-md py-2 px-3 shadow-sm focus:outline-none focus:ring focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
          />
        </div>
        <div>
          <label for="address" class="block text-sm font-medium">Address</label>
          <input
            type="text"
            id="address"
            v-model="hotel.address"
            class="mt-1 block w-full bg-gray-700 text-white border border-gray-600 rounded-md py-2 px-3 shadow-sm focus:outline-none focus:ring focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
          />
        </div>
        <div>
          <label for="phone" class="block text-sm font-medium">Phone</label>
          <input
            type="text"
            id="phone"
            v-model="hotel.phone"
            class="mt-1 block w-full bg-gray-700 text-white border border-gray-600 rounded-md py-2 px-3 shadow-sm focus:outline-none focus:ring focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
          />
        </div>
        <div>
          <label for="email" class="block text-sm font-medium">Email</label>
          <input
            type="email"
            id="email"
            v-model="hotel.email"
            class="mt-1 block w-full bg-gray-700 text-white border border-gray-600 rounded-md py-2 px-3 shadow-sm focus:outline-none focus:ring focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
          />
        </div>
        <div>
          <label for="website" class="block text-sm font-medium">Website</label>
          <input
            type="text"
            id="website"
            v-model="hotel.website"
            class="mt-1 block w-full bg-gray-700 text-white border border-gray-600 rounded-md py-2 px-3 shadow-sm focus:outline-none focus:ring focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
          />
        </div>
        <div class="flex justify-between">
          <button
            type="submit"
            class="bg-indigo-600 text-white py-2 px-4 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50"
          >
            {{ submitButtonText }}
          </button>
          <router-link :to="{ name: 'Hotel' }" class="bg-gray-600 text-white py-2 px-4 rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-opacity-50">
            Back
          </router-link>
        </div>
        <!-- Mostrar los errores como una lista desordenada -->
        <div v-if="errorMessages.length" class="text-red-400 mt-4">
          <ul class="list-disc pl-5">
            <li v-for="(error, index) in errorMessages" :key="index">{{ error }}</li>
          </ul>
        </div>
      </form>
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent, reactive, ref, onMounted, type PropType } from 'vue';
import { useRoute, useRouter } from 'vue-router';

interface ErrorData {
  errors: Record<string, string[]>;
  error?: string;
}

export default defineComponent({
  props: {
    isEditMode: {
      type: Boolean as PropType<boolean>,
      default: false,
    },
    hotelId: {
      type: String as PropType<string>,
      default: '',
    }
  },
  setup(props) {
    const route = useRoute();
    const router = useRouter();

    const hotel = reactive({
      name: '',
      address: '',
      phone: '',
      email: '',
      website: ''
    });

    const errorMessages = ref<string[]>([]);

    const fetchHotel = async () => {
      if (!props.isEditMode || !props.hotelId) return;

      try {
        const response = await fetch(`http://hotel-manager.test/api/hotel/${props.hotelId}`);
        if (!response.ok) {
          throw new Error('Failed to fetch hotel');
        }
        const data = await response.json();
        Object.assign(hotel, data);
      } catch (error) {
        console.error('Fetch error:', error);
        errorMessages.value = ['Error al cargar los datos del hotel'];
      }
    };

    function flattenArray<T>(arr: T[][]): T[] {
      return arr.reduce((acc: T[], val: T[]) => acc.concat(val), []);
    }

    const handleSubmit = async () => {
      const requestBody = {
        name: hotel.name,
        address: hotel.address,
        phone: hotel.phone,
        email: hotel.email,
        website: hotel.website
      };

      console.log('Request Body:', requestBody);

      try {
        const method = props.isEditMode ? 'PUT' : 'POST';
        const url = props.isEditMode
          ? `http://hotel-manager.test/api/hotel/${props.hotelId}`
          : `http://hotel-manager.test/api/hotel`;

        const response = await fetch(url, {
          method,
          headers: {
            'Content-Type': 'application/json'
          },
          body: JSON.stringify(requestBody)
        });

        if (!response.ok) {
          const errorData: ErrorData = await response.json();
          console.error('Error:', errorData);
          if (response.status === 422 && errorData.errors) {
            // Asegurarse de que errorData.errors es un objeto donde cada propiedad es un array de mensajes
            errorMessages.value = flattenArray(Object.values(errorData.errors));
          } else {
            errorMessages.value = [errorData.error || 'An unexpected error occurred'];
          }
          throw new Error(`HTTP error! status: ${response.status}`);
        }

        router.push({ name: 'Hotel' });
      } catch (error) {
        console.error('Caught error:', error);
      }
    };

    if (props.isEditMode) {
      onMounted(fetchHotel);
    }

    return {
      hotel,
      errorMessages,
      submitButtonText: props.isEditMode ? 'Update' : 'Create',
      formTitle: props.isEditMode ? 'Edit Hotel' : 'Create Hotel',
      handleSubmit,
    };
  }
});
</script>

<style>
.error {
    color: red;
}
</style>
