<script lang="ts" setup>
import { ref, onMounted, computed } from 'vue';
import AppMenuItem from './AppMenuItem.vue';
import apiClient from '@/axios';

const model = ref([
    {
        label: 'Home',
        items: [
            { label: 'Dashboard', icon: 'pi pi-fw pi-desktop', to: '/dashboard' },
            { label: 'Home', icon: 'pi pi-fw pi-home', to: '/' }
        ]
    },
    {
        label: 'Items',
        items: [
            { label: 'Hotels', icon: 'pi pi-fw pi-building', to: '/items/hotel' },
            { label: 'Guests', icon: 'pi pi-fw pi-users', to: '/items/guest' },
            { label: 'Rooms', icon: 'pi pi-fw pi-box', to: '/items/room' },
            { label: 'Types', icon: 'pi pi-fw pi-list', to: '/items/type' },
            { label: 'Services', icon: 'pi pi-fw pi-table', to: '/items/service' },
            { label: 'Categories', icon: 'pi pi-fw pi-tags', to: '/items/category' }
        ]
    }
]);

const userRole = ref<string | null>(null);
const loadingRole = ref(true); // Loading state for the role

onMounted(async () => {
    try {
        const response = await apiClient.post('http://hotel-manager.test/api/auth/me', {}, {
            headers: {
                Authorization: `Bearer ${localStorage.getItem('token')}` // Include the token in the header
            }
        });

        const user = response.data;
        userRole.value = user.role; // Assign the user role
    } catch (error) {
        console.error('Error fetching user data:', error);
    } finally {
        loadingRole.value = false; // End the role loading
    }
});

// Filter the model based on the user role
const filteredModel = computed(() => {
    if (loadingRole.value) {
        return []; // Do not render the menu while loading
    }

    if (userRole.value === 'user') {
        return [
            {
                label: 'Home',
                items: [
                    { label: 'Home', icon: 'pi pi-fw pi-home', to: '/' }
                ]
            }
        ];
    }

    return model.value; // Show the full menu for other roles (like admin)
});
</script>

<template>
    <ul class="layout-menu">
        <div v-if="loadingRole">
            <!-- Message while loading the role -->
            <li>Loading menu options...</li>
        </div>
        <div v-else>
            <!-- Render the filtered menu -->
            <div v-for="(item, i) in filteredModel" :key="i">
                <app-menu-item v-if="!item.separator" :item="item" :index="i"></app-menu-item>
                <li v-if="item.separator" class="menu-separator"></li>
            </div>
        </div>
    </ul>
</template>

<style lang="scss" scoped></style>
