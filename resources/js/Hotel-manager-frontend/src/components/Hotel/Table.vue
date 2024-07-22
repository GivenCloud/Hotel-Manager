<template>
  <div class="container min-w-full min-h-full mx-auto p-4">
    <h1 class="text-2xl font-bold mb-4 text-gray-100">Hotel List</h1>

    <div class="flex justify-between items-center mb-4">
      <div>
        <label for="itemsPerPage" class="mr-2 text-white">Items per page:</label>
        <select id="itemsPerPage" v-model="itemsPerPage" class="p-2 text-black border border-gray-300 rounded-lg shadow-sm">
          <option v-for="option in [5, 10, 20]" :key="option" :value="option">{{ option }}</option>
        </select>
      </div>
      <div>
        <input type="text" v-model="searchQuery" placeholder="Search..." class="p-2 border border-gray-300 rounded-lg shadow-sm" />
      </div>
    </div>

    <div class="overflow-x-auto border border-gray-200 rounded-lg shadow-sm">
      <table class="min-w-full bg-gray-100">
        <thead>
          <tr>
            <th
              v-for="(col, index) in columns"
              :key="index"
              class="py-2 px-4 border-b border-gray-300 cursor-pointer"
              @click="sortTable(index)"
            >
              {{ col.label }}
              <span v-if="sortKey === index">
                {{ sortOrder === 1 ? '▲' : sortOrder === -1 ? '▼' : '' }}
              </span>
            </th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="item in paginatedData" :key="item.id">
            <td class="py-2 px-4 border-b border-gray-300">{{ item.name }}</td>
            <td class="py-2 px-4 border-b border-gray-300">{{ item.address }}</td>
            <td class="py-2 px-4 border-b border-gray-300">{{ item.phone }}</td>
            <td class="py-2 px-4 border-b border-gray-300">{{ item.email }}</td>
            <td class="py-2 px-4 border-b border-gray-300">
              <a :href="item.website" target="_blank" class="text-blue-500 hover:underline">{{ item.website }}</a>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <div class="flex justify-between items-center mt-4">
      <button @click="prevPage" :disabled="currentPage === 1" class="p-2 text-black border border-gray-300 rounded-lg bg-gray-100 shadow-sm">Previous</button>
      <span class="text-gray-100">Page {{ currentPage }} of {{ totalPages }}</span>
      <button @click="nextPage" :disabled="currentPage === totalPages" class="p-2 text-black border border-gray-300 rounded-lg bg-gray-100 shadow-sm">Next</button>
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
      { label: 'Website', key: 'website' }
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
  background: linear-gradient(to right, #141E30, #243B55);
  min-height: 100vh;
}

.table {
  border-radius: 0.5rem; /* Redondea las esquinas */
  box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1); /* Agrega una sombra sutil */
}
</style>
