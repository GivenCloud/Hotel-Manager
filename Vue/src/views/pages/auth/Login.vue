<script lang="ts" setup>
import { ref, computed } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';
import { useLayout } from '@/layout/composables/layout';
import AppConfig from '@/layout/AppConfig.vue';

// Composición
const { layoutConfig } = useLayout();
const email = ref('');
const password = ref('');
const checked = ref(false);
const error = ref(''); // Variable para mostrar errores

const router = useRouter();

const logoUrl = computed(() => {
    return `/layout/images/${layoutConfig.darkTheme.value ? 'logo-white' : 'logo-dark'}.svg`;
});

const login = async () => {
    try {
        console.log('Attempting login with email:', email.value);
        const response = await axios.post('http://hotel-manager.test/api/auth/login', {
            email: email.value,
            password: password.value
        });

        console.log('Login successful', response.data);

        localStorage.setItem('token', response.data.access_token);

        console.log('Token stored in localStorage');

        console.log('Redirecting to home page');
        await router.push({ path: '/' });

        console.log('Redirection successful');

    } catch (err: any) {
        console.error('Login error:', err);
        error.value = err.response?.data?.message || 'An error occurred';
    }
};

// Nueva función para redirigir a la página de registro
const goToRegister = () => {
    router.push({ path: '/auth/register' });
};
</script>

<template>
    <div>
        <div class="surface-ground flex align-items-center justify-content-center min-h-screen min-w-screen overflow-hidden">
            <div class="flex flex-column align-items-center justify-content-center">
                <img :src="logoUrl" alt="Logo" class="mb-5 w-6rem flex-shrink-0" />
                <div style="border-radius: 56px; padding: 0.3rem; background: linear-gradient(180deg, var(--primary-color) 10%, rgba(33, 150, 243, 0) 30%)">
                    <div class="w-full surface-card py-8 px-5 sm:px-8" style="border-radius: 53px">
                        <div class="text-center mb-5">
                            <div class="text-900 text-3xl font-medium mb-3">Welcome!</div>
                            <span class="text-600 font-medium">Sign in to continue</span>
                        </div>

                        <div>
                            <label for="email1" class="block text-900 text-xl font-medium mb-2">Email</label>
                            <InputText id="email1" type="text" placeholder="Email address" class="w-full md:w-30rem mb-5" style="padding: 1rem" v-model="email" />

                            <label for="password1" class="block text-900 font-medium text-xl mb-2">Password</label>
                            <Password id="password1" v-model="password" placeholder="Password" :toggleMask="true" class="w-full mb-3" inputClass="w-full" :inputStyle="{ padding: '1rem' }"></Password>

                            <div class="flex align-items-center justify-content-between mb-5 gap-5">
                                <div class="flex align-items-center">
                                    <Checkbox v-model="checked" id="rememberme1" binary class="mr-2"></Checkbox>
                                    <label for="rememberme1">Remember me</label>
                                </div>
                                <a class="font-medium no-underline ml-2 text-right cursor-pointer" style="color: var(--primary-color)">Forgot password?</a>
                            </div>
                            <Button label="Sign In" class="w-full p-3 text-xl" @click="login"></Button>
                            <p v-if="error" class="text-red-600">{{ error }}</p> <!-- Mostrar errores -->

                            <!-- Enlace para registrarse -->
                            <div class="text-center mt-5">
                                <span class="text-600 font-medium">Don't have an account?</span>
                                <a @click="goToRegister" class="font-medium no-underline ml-2 text-right cursor-pointer" style="color: var(--primary-color)">Sign up</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <AppConfig simple />
    </div>
</template>

<style scoped>
.pi-eye {
    transform: scale(1.6);
    margin-right: 1rem;
}

.pi-eye-slash {
    transform: scale(1.6);
    margin-right: 1rem;
}
</style>
