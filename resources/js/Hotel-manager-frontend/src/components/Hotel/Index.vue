<template>
    <div>
        <div>
            <NavBar />
        </div>
        <div>
            <div>
                <Table />
            </div>
        </div>
    </div>
</template>

<script lang="ts" setup>
import NavBar from '../NavBar.vue';
import Table from './Table.vue';
import { useHotel } from '../../composables/useHotel';

const { hotels } = useHotel();
</script>

<style scoped>
/* Fondo gradiente para el contenedor principal */

</style>