<script lang="ts" setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import Tag from 'primevue/tag';
import { type User } from '@/types/user';

// Estado para almacenar los datos del usuario
const user = ref<User | null>(null);

// Función para obtener los datos del usuario autenticado
const fetchUserProfile = async () => {
    try {
        const response = await axios.post('http://hotel-manager.test/api/auth/me', {}, {
            headers: {
                Authorization: `Bearer ${localStorage.getItem('token')}`,
            },
        });
        user.value = response.data;
    } catch (error) {
        console.error('Failed to load user profile:', error);
    }
};

// Ejecutar la función al montar el componente
onMounted(async () => {
    await fetchUserProfile();
});
</script>

<template>
    <div class="p-justify-center">
        <div class="p-col-12 p-md-8">
            <div class="p-card p-p-3">
                <template v-if="user">
                    <div class="flex">
                        <div class="mt-6 p-col-12 p-md-8 user-details">
                            <h2 class="user-name">{{ user.name }}</h2>
                            <div class="p-grid">
                                <div class="p-col-12">
                                    <div class="user-info">
                                        <i class="pi pi-envelope icon"></i>
                                        <span>Email: {{ user.email }}</span>
                                    </div>
                                    <div class="user-info">
                                        <i class="pi pi-calendar icon"></i>
                                        <span>Account Created: {{ new Date(user.created_at).toLocaleDateString() }}</span>
                                    </div>
                                    <div class="user-info">
                                        <i class="pi pi-pencil icon"></i>
                                        <span>Last Updated: {{ new Date(user.updated_at).toLocaleDateString() }}</span>
                                    </div>
                                    <div class="user-info">
                                        <i class="pi pi-tag icon"></i>
                                        <span>Role: <Tag style="font-size: 1em">{{ user.role }}</Tag></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>
</template>

<style scoped>
.user-details {
    padding-left: 20px;
    text-align: left;
}

.user-name {
    font-size: 1.5em;
    margin-bottom: 10px;
    font-weight: bold;
}

.user-info {
    display: flex;
    align-items: center;
    margin-bottom: 10px;
    font-size: 1.1em;
}

.icon {
    margin-right: 10px;
    color: #666;
    font-size: 1.2em;
}

a {
    color: #007bff;
    text-decoration: none;
}

a:hover {
    text-decoration: underline;
}
</style>
