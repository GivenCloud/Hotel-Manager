<script lang="ts" setup>
import { ref, computed, onMounted, onBeforeUnmount } from 'vue';
import { useLayout } from './composables/layout';
import { useRouter } from 'vue-router';
import axios from 'axios';

const { layoutConfig, onMenuToggle } = useLayout();

const outsideClickListener = ref<OutsideClickListener | null>(null);
const topbarMenuActive = ref(false);
const userMenuActive = ref(false); // Nuevo ref para el menú del usuario
const router = useRouter();

onMounted(() => {
    bindOutsideClickListener();
});

onBeforeUnmount(() => {
    unbindOutsideClickListener();
});

const onTopBarMenuButton = () => {
    topbarMenuActive.value = !topbarMenuActive.value;
};

const onUserMenuButton = () => {
    userMenuActive.value = !userMenuActive.value;
};

const onProfileClick = () => {
    userMenuActive.value = false;
    router.push('/profile');
};

const onLogoutClick = async () => {
    try {
        const token = localStorage.getItem('token');
        if (token) {
            await axios.post('http://hotel-manager.test/api/auth/logout', {}, {
                headers: {
                    Authorization: `Bearer ${token}`,
                },
            });
            localStorage.removeItem('token');
            router.push('/auth/login');
        }
    } catch (e) {
        console.error('Logout failed:', e);
    } finally {
        userMenuActive.value = false;
    }
};

const topbarMenuClasses = computed(() => ({
    'layout-topbar-menu-mobile-active': topbarMenuActive.value,
}));

const userMenuClasses = computed(() => ({
    'user-menu-active': userMenuActive.value, // Clase para mostrar/ocultar el menú del usuario
}));

interface OutsideClickListener {
    (event: MouseEvent): void;
}

const bindOutsideClickListener: OutsideClickListener = () => {
    if (!outsideClickListener.value) {
        outsideClickListener.value = (event: MouseEvent) => {
            if (isOutsideClicked(event)) {
                topbarMenuActive.value = false;
                userMenuActive.value = false;
            }
        };
        document.addEventListener('click', outsideClickListener.value);
    }
};

const unbindOutsideClickListener: OutsideClickListener = () => {
    if (outsideClickListener.value) {
        document.removeEventListener('click', outsideClickListener.value);
        outsideClickListener.value = null;
    }
};

const isOutsideClicked = (event: MouseEvent): boolean => {
    if (!topbarMenuActive.value && !userMenuActive.value) return false;

    const sidebarEl = document.querySelector('.layout-topbar-menu') as HTMLElement;
    const topbarEl = document.querySelector('.layout-topbar-menu-button') as HTMLElement;
    const userMenuEl = document.querySelector('.user-menu') as HTMLElement;

    return !(sidebarEl.contains(event.target as Node) || topbarEl.contains(event.target as Node) || userMenuEl?.contains(event.target as Node));
};
</script>

<template>
    <div class="layout-topbar">
        <router-link to="/" class="layout-topbar-logo">
            <i class="pi pi-prime mr-2" style="font-size: 2rem;"></i>
            <span>Hotel Manager</span>
        </router-link>

        <button class="p-link layout-menu-button layout-topbar-button" @click="onMenuToggle()">
            <i class="pi pi-bars"></i>
        </button>

        <button class="p-link layout-topbar-menu-button layout-topbar-button" @click="onTopBarMenuButton()">
            <i class="pi pi-ellipsis-v"></i>
        </button>

        <div class="layout-topbar-menu" :class="topbarMenuClasses">
            <button @click="onUserMenuButton()" class="p-link layout-topbar-button">
                <i class="pi pi-user"></i>
                <span>User</span>
            </button>
            <!-- Menú desplegable -->
            <div class="user-menu" :class="userMenuClasses">
                <button @click="onProfileClick" class="p-link user-menu-item">
                    <i class="pi pi-user"></i>
                    <span>Profile</span>
                </button>
                <button @click="onLogoutClick" class="p-link user-menu-item">
                    <i class="pi pi-sign-out"></i>
                    <span>Logout</span>
                </button>
            </div>
        </div>
    </div>
</template>

<style lang="scss" scoped>
.layout-topbar-menu {
    position: relative;
}

.user-menu {
    display: none;
    position: absolute;
    top: 100%;
    right: 0;
    background-color: #fff;
    border: 1px solid #ccc;
    border-radius: 4px;
    box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
    z-index: 1000;
}

.user-menu-active {
    display: block;
}

.user-menu-item {
    display: flex;
    align-items: center;
    padding: 10px 20px;
    width: 100%;
    text-align: left;
    border-bottom: 1px solid #eee;
    background: none;
    border: none;
    cursor: pointer;
}

.user-menu-item:hover {
    background-color: #f5f5f5;
}

.user-menu-item i {
    margin-right: 10px;
    font-size: 1rem;
}

.user-menu-item span {
    flex-grow: 1;
}
</style>
