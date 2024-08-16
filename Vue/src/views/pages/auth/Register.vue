<script lang="ts" setup>
import { ref, computed } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';
import { useLayout } from '@/layout/composables/layout';
import AppConfig from '@/layout/AppConfig.vue';

// Composición
const { layoutConfig } = useLayout();
const name = ref(''); // Nuevo campo para el nombre
const email = ref('');
const password = ref('');
const confirmPassword = ref('');
const error = ref(''); // Variable para mostrar errores

const router = useRouter();

const logoUrl = computed(() => {
    return `/layout/images/${layoutConfig.darkTheme.value ? 'logo-white' : 'logo-dark'}.svg`;
});

const register = async () => {
    if (password.value !== confirmPassword.value) {
        error.value = "Passwords do not match";
        return;
    }

    try {
        console.log('Attempting registration with name:', name.value, 'email:', email.value);
        const responseRegister = await axios.post('http://hotel-manager.test/api/auth/register', {
            name: name.value,  // Incluye el nombre en la petición
            email: email.value,
            password: password.value,
            password_confirmation: confirmPassword.value, // Incluye la confirmación de la contraseña
        });

        console.log('Registration successful', responseRegister.data);

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

    } catch (e: any) {
        console.error('Registration error:', e);
        error.value = e.response?.data?.message || 'An error occurred';
    }
};

// Función para redirigir a la página de login
const goToLogin = () => {
    router.push({ path: '/auth/login' });
};
</script>

<template>
    <div>
        <div class="surface-ground flex align-items-center justify-content-center min-h-screen min-w-screen overflow-hidden">
            <div class="flex flex-column align-items-center justify-content-center">
                <img :src="logoUrl" alt="Logo" class="mb-5 w-6rem flex-shrink-0" />
                <div style="border-radius: 56px; padding: 0.3rem; background: linear-gradient(180deg, var(--primary-color) 10%, rgba(33, 150, 243, 0) 30%)">
                    <div class="w-full surface-card py-4 px-5 sm:px-8" style="border-radius: 53px">
                        <div class="text-center mb-5">
                            <div class="text-900 text-3xl font-medium mb-3">Join Us!</div>
                            <span class="text-600 font-medium">Create an account to get started</span>
                        </div>

                        <div>
                            <!-- Campo Nombre -->
                            <label for="name1" class="block text-900 text-xl font-medium mb-2">Name</label>
                            <InputText id="name1" type="text" placeholder="Your name" class="w-full md:w-30rem mb-4" style="padding: 1rem" v-model="name" />

                            <!-- Campo Email -->
                            <label for="email1" class="block text-900 text-xl font-medium mb-2">Email</label>
                            <InputText id="email1" type="text" placeholder="Email address" class="w-full md:w-30rem mb-4" style="padding: 1rem" v-model="email" />

                            <!-- Campo Contraseña -->
                            <label for="password1" class="block text-900 font-medium text-xl mb-2">Password</label>
                            <Password id="password1" v-model="password" placeholder="Password" :toggleMask="true" class="w-full mb-4" inputClass="w-full" :inputStyle="{ padding: '1rem' }"></Password>

                            <!-- Campo Confirmar Contraseña -->
                            <label for="confirmPassword" class="block text-900 font-medium text-xl mb-2">Confirm Password</label>
                            <Password id="confirmPassword" v-model="confirmPassword" placeholder="Confirm Password" :toggleMask="true" class="w-full mb-4" inputClass="w-full" :inputStyle="{ padding: '1rem' }"></Password>

                            <Button label="Sign Up" class="w-full p-3 text-xl" @click="register"></Button>
                            <p v-if="error" class="text-red-600 mt-3">{{ error }}</p> <!-- Mostrar errores -->

                            <!-- Enlace para iniciar sesión -->
                            <div class="text-center mt-5">
                                <span class="text-600 font-medium">Already have an account?</span>
                                <a @click="goToLogin" class="font-medium no-underline ml-2 text-right cursor-pointer" style="color: var(--primary-color)">Sign in</a>
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
