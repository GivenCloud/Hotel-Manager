<template>
  <div class="bg-gray-800 min-h-screen p-4">
    <h1 class="text-3xl font-bold mb-6 text-gray-100">Hotel List</h1>

    <div class="flex justify-between items-center mb-4">
      <div class="flex items-center">
        <label for="itemsPerPage" class="mr-2 text-white">Items per page:</label>
        <select
          id="itemsPerPage"
          v-model="itemsPerPage"
          class="p-2 bg-gray-700 text-white border border-gray-600 rounded-lg shadow-sm focus:outline-none focus:ring focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
        >
          <option v-for="option in [5, 10, 20]" :key="option" :value="option">{{ option }}</option>
        </select>
      </div>
      <div class="flex items-center">
        <router-link
          :to="{ name: 'HotelCreate' }"
          class="p-2 bg-gray-700 text-white border border-gray-600 rounded-lg shadow-sm hover:bg-gray-600 disabled:opacity-50 mr-4"
        >
          Create new hotel
        </router-link>
        <div>
          <input
            type="text"
            v-model="searchQuery"
            placeholder="Search..."
            class="p-2 bg-gray-700 text-white border border-gray-600 rounded-lg shadow-sm focus:outline-none focus:ring focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
          />
        </div>
      </div>
    </div>

    <div class="overflow-x-auto border border-gray-700 rounded-lg shadow-lg">
      <table class="min-w-full table-auto bg-gray-900 text-white">
        <thead>
          <tr>
            <th
              v-for="(col, index) in columns"
              :key="index"
              class="py-3 px-4 border-b border-gray-700 cursor-pointer hover:bg-gray-700"
              @click="sortTable(index)"
            >
              {{ col.label }}
              <span v-if="sortKey === index" class="ml-2">
                {{ sortOrder === 1 ? '▲' : sortOrder === -1 ? '▼' : '' }}
              </span>
            </th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="item in paginatedData" :key="item.id" class="hover:bg-gray-800">
            <td class="py-3 px-4 border-b border-gray-700">{{ item.name }}</td>
            <td class="py-3 px-4 border-b border-gray-700">{{ item.address }}</td>
            <td class="py-3 px-4 border-b border-gray-700">{{ item.phone }}</td>
            <td class="py-3 px-4 border-b border-gray-700">{{ item.email }}</td>
            <td class="py-3 px-4 border-b border-gray-700">
              <a :href="item.website" target="_blank" class="text-blue-400 hover:underline">{{ item.website }}</a>
            </td>
            <td class="py-3 px-4 border-b border-gray-700">
              <router-link :to="{ name: 'HotelEdit', params: { id: item.id } }" class="text-blue-400 hover:underline">Edit </router-link>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <div class="flex justify-between items-center mt-4">
      <button
        @click="prevPage"
        :disabled="currentPage === 1"
        class="p-2 bg-gray-700 text-white border border-gray-600 rounded-lg shadow-sm hover:bg-gray-600 disabled:opacity-50"
      >
        Previous
      </button>
      <span class="text-gray-100">Page {{ currentPage }} of {{ totalPages }}</span>
      <button
        @click="nextPage"
        :disabled="currentPage === totalPages"
        class="p-2 bg-gray-700 text-white border border-gray-600 rounded-lg shadow-sm hover:bg-gray-600 disabled:opacity-50"
      >
        Next
      </button>
    </div>
  </div>
</template>


<script lang="ts">
import { defineComponent, ref, computed, watch } from 'vue';
import { useHotel } from '../../composables/useHotel';
import { type Hotel } from "../../interfaces/Hotel/hotel.interface"

export default defineComponent({
  name: 'DataTable',
  setup() {
    const { hotels } = useHotel();
    const items = ref(hotels.value);

    watch(() => hotels.value, (newHotels) => {
      items.value = newHotels;
    }, { immediate: true });

    const columns = ref([
      { label: 'Name', key: 'name' },
      { label: 'Address', key: 'address' },
      { label: 'Phone', key: 'phone' },
      { label: 'Email', key: 'email' },
      { label: 'Website', key: 'website' },
      { label: 'Actions', key: 'actions' },
    ]);

    const itemsPerPage = ref(10);
    const currentPage = ref(1);
    const searchQuery = ref('');
    const sortKey = ref<number>(-1);
    const sortOrder = ref<number>(0);

    const sortedData = computed(() => {
      return items.value
        .filter(item => {
          const searchLower = searchQuery.value.toLowerCase();
          return (
            item.name.toLowerCase().includes(searchLower) ||
            item.address.toLowerCase().includes(searchLower) ||
            item.phone.toLowerCase().includes(searchLower) ||
            item.email.toLowerCase().includes(searchLower) ||
            item.website.toLowerCase().includes(searchLower)
          );
        })
        .sort((a: Hotel, b: Hotel) => {
          if (sortKey.value === -1 || sortOrder.value === 0) return 0;
          const key = columns.value[sortKey.value].key as keyof Hotel;
          const order = sortOrder.value;
          if (a[key] < b[key]) return -order;
          if (a[key] > b[key]) return order;
          return 0;
        });
    });

    const paginatedData = computed(() => {
      const start = (currentPage.value - 1) * itemsPerPage.value;
      const end = start + itemsPerPage.value;
      return sortedData.value.slice(start, end);
    });

    const totalPages = computed(() => {
      return Math.ceil(sortedData.value.length / itemsPerPage.value);
    });

    const nextPage = () => {
      if (currentPage.value < totalPages.value) {
        currentPage.value++;
      }
    };

    const prevPage = () => {
      if (currentPage.value > 1) {
        currentPage.value--;
      }
    };

    const sortTable = (index: number) => {
      if (sortKey.value === index) {
        sortOrder.value = sortOrder.value === 0 ? 1 : (sortOrder.value === 1 ? -1 : 0);
      } else {
        sortKey.value = index;
        sortOrder.value = 1;
      }
    };

    return {
      items,
      columns,
      itemsPerPage,
      currentPage,
      searchQuery,
      sortKey,
      sortOrder,
      paginatedData,
      totalPages,
      nextPage,
      prevPage,
      sortTable
    };
  }
});
</script>

<style scoped>
.container {
  background: #222831;
  min-height: 100vh;
}

.table {
  border-radius: 0.5rem; /* Redondea las esquinas */
  background-color: #EEEEEE;
}
</style>