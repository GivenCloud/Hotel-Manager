<script lang="ts" setup>
import { FilterMatchMode } from 'primevue/api';
import { ref, onMounted, onBeforeMount } from 'vue';
import { useRoom } from '../../../service/useRoom';
import { useHotel } from '@/service/useHotel';
import { useType } from '@/service/useType';
import { useToast } from 'primevue/usetoast';
import { type Room } from '@/types/room';
import axios from 'axios';

const toast = useToast();

const products = ref(null);
const productDialog = ref(false);
const deleteProductDialog = ref(false);
const deleteProductsDialog = ref(false);
const product = ref<Room>({});
const selectedProducts = ref(null);
const dt = ref(null);
const filters = ref({});
const submitted = ref(false);

const hotelsId = ref();
const typesId = ref();

const useRooms = new useRoom();
const useHotels = new useHotel();
const useTypes = new useType();

onBeforeMount(() => {
    initFilters();
});
onMounted(() => {
    useRooms.getRooms().then((data) => {
        products.value = data;
    });
    useHotels.getHotels().then((data) => {
        hotelsId.value = data.map((hotel) => ({ label: hotel.name, value: hotel.id }));
    }).catch(error => {
        console.error('Error loading hotels:', error);
        hotelsId.value = [];
    });
    useTypes.getTypes().then((data) => {
        typesId.value = data.map((type) => ({ label: type.name, value: type.id }));
    }).catch(error => {
        console.error('Error loading types:', error);
        typesId.value = [];
    });
});

const openNew = () => {
    product.value = {};
    submitted.value = false;
    productDialog.value = true;
};

const hideDialog = () => {
    productDialog.value = false;
    submitted.value = false;
};

const saveProduct = async () => {
    submitted.value = true;

    // Validar campos requeridos
    if (!product.value.number || !product.value.type_id || !product.value.hotel_id) {
        toast.add({ severity: 'error', summary: 'Error', detail: 'All fields are required', life: 3000 });
        return;
    }

    try {
        let response;

        if (product.value.id) {
            let typeId;
            let hotelId;
            if (typeof product.value.type_id === 'object') {
                typeId = product.value.type_id.value;
            } else {
                typeId = product.value.type_id;
            }

            if (typeof product.value.hotel_id === 'object') {
                hotelId = product.value.hotel_id.value;
            } else {
                hotelId = product.value.hotel_id;
            }

            // Realiza la petición PUT a la API de Laravel para actualizar el room
            response = await axios.put(`http://hotel-manager.test/api/room/${product.value.id}`, {
                number: product.value.number,
                type_id: typeId,
                hotel_id: hotelId,
            });

            // Actualiza el room en la lista local
            const index = products.value.findIndex((p:any) => p.id === product.value.id);
            if (index !== -1) {
                products.value[index] = response.data;
            }

            // Muestra una notificación de éxito
            toast.add({ severity: 'success', summary: 'Successful', detail: 'Room Updated', life: 3000 });
        } else {
            // Realiza la petición POST a la API de Laravel para crear un nuevo room
            response = await axios.post('http://hotel-manager.test/api/room', {
                number: product.value.number,
                type_id: product.value.type_id.value,
                hotel_id: product.value.hotel_id.value,
            });

            // Añade el nuevo room a la lista local
            products.value.push(response.data);

            // Muestra una notificación de éxito
            toast.add({ severity: 'success', summary: 'Successful', detail: 'Room Created', life: 3000 });
        }

        // Cierra el diálogo de creación/edición
        productDialog.value = false;
        product.value = {};

    } catch (error: unknown) {
        if ((error as any).response && (error as any).response.data && (error as any).response.data.errors) {
            // Maneja errores específicos de validación
            const errors: Record<string, string[]> = (error as any).response.data.errors;
            let errorMessage = 'Validation errors:';

            // Itera sobre los errores y agrega un mensaje para cada campo
            for (const [field, messages] of Object.entries(errors)) {
                messages.forEach((message: string) => {
                    errorMessage += `\n- ${message}`;
                });
            }

            // Muestra las notificaciones de error
            toast.add({ severity: 'error', summary: 'Validation Error', detail: errorMessage, life: 5000 });
        } else {
            // Maneja errores generales
            toast.add({ severity: 'error', summary: 'Error', detail: 'Could not save room', life: 3000 });
        }
    }
};

const editProduct = (editProduct: Room) => {
    if (editProduct) {
        product.value = { ...editProduct };
        productDialog.value = true;
    } else {
        console.error('No product data available for editing.');
    }
};

const confirmDeleteProduct = (editProduct: Room) => {
    product.value = editProduct;
    deleteProductDialog.value = true;
};

const deleteProduct = async () => {
    try {
        // Realiza la petición DELETE a la API de Laravel
        await axios.delete(`http://hotel-manager.test/api/room/${product.value.id}`);

        // Filtra el producto eliminado de la lista local
        products.value = products.value.filter((val: { id: any; }) => val.id !== product.value.id);
        
        // Cierra el diálogo de confirmación
        deleteProductDialog.value = false;
        
        // Limpia el producto seleccionado
        product.value = {};
        
        // Muestra una notificación de éxito
        toast.add({ severity: 'success', summary: 'Successful', detail: 'Room Deleted', life: 3000 });
    } catch (error) {
        // Maneja errores en la petición DELETE
        console.error('Error deleting room:', error);
        toast.add({ severity: 'error', summary: 'Error', detail: 'Could not delete room', life: 3000 });
    }
};

const confirmDeleteSelected = () => {
    deleteProductsDialog.value = true;
};

const deleteSelectedProducts = async () => {
    try {
        // Realiza todas las peticiones DELETE concurrentemente
        await Promise.all(selectedProducts.value.map((room: Room) => 
            axios.delete(`http://hotel-manager.test/api/room/${room.id}`)
        ));

        // Filtra los roomes locales eliminando los seleccionados
        products.value = products.value.filter((val:any) => !selectedProducts.value.includes(val));
        
        // Cierra el diálogo de eliminación
        deleteProductsDialog.value = false;
        selectedProducts.value = null;

        // Muestra una notificación de éxito
        toast.add({ severity: 'success', summary: 'Successful', detail: 'Rooms Deleted', life: 3000 });
    } catch (error) {
        // Maneja errores en la petición DELETE
        console.error('Error deleting rooms:', error);
        toast.add({ severity: 'error', summary: 'Error', detail: 'Could not delete rooms', life: 3000 });
    }
};

const initFilters = () => {
    filters.value = {
        global: { value: null, matchMode: FilterMatchMode.CONTAINS }
    };
};

const getHotelName = (hotelId: number) => {  
    if (hotelsId.value) {
        const hotel = hotelsId.value.find((hotel: { value: number; }) => hotel.value === hotelId);
        return hotel ? hotel.label : 'Unknown Hotel';
    }
    return 'Unknown Hotel';
};

const getTypeName = (typeId: number) => {
    if (typesId.value) {
        const type = typesId.value.find((t: { value: number; }) => t.value === typeId);
        return type ? type.label : 'Unknown Type';
    }
    return 'Unknown Type';
};

</script>

<template>
    <div class="grid">
        <div class="col-12">
            <div class="card">
                <Toolbar class="mb-4">
                    <template v-slot:start>
                        <div class="my-2">
                            <Button label="New" icon="pi pi-plus" class="mr-2" severity="success" @click="openNew" />
                            <Button label="Delete" icon="pi pi-trash" severity="danger" @click="confirmDeleteSelected" :disabled="!selectedProducts || !selectedProducts.length" />
                        </div>
                    </template>
                </Toolbar>

                <DataTable
                    ref="dt"
                    :value="products"
                    v-model:selection="selectedProducts"
                    dataKey="id"
                    :paginator="true"
                    :rows="10"
                    :filters="filters"
                    paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
                    :rowsPerPageOptions="[5, 10, 25]"
                    currentPageReportTemplate="Showing {first} to {last} of {totalRecords} products"
                >
                    <template #header>
                        <div class="flex flex-column md:flex-row md:justify-content-between md:align-items-center">
                            <h5 class="m-0">Manage Rooms</h5>
                            <IconField iconPosition="left" class="block mt-2 md:mt-0">
                                <InputIcon class="pi pi-search" />
                                <InputText class="w-full sm:w-auto" v-model="filters['global'].value" placeholder="Search..." />
                            </IconField>
                        </div>
                    </template>

                    <Column selectionMode="multiple" headerStyle="width: 3rem"></Column>
                    <Column field="number" header="Number" :sortable="true" headerStyle="width:14%; min-width:10rem;">
                        <template #body="slotProps">
                            <span class="p-column-title">Number</span>
                            {{ slotProps.data.number }}
                        </template>
                    </Column>
                    <Column field="type_id" header="Type" :sortable="true" headerStyle="width:14%; min-width:10rem;">
                        <template #body="slotProps">
                            <span class="p-column-title">Type</span>
                            <Tag>{{ getTypeName(slotProps.data.type_id) }}</Tag>
                        </template>
                    </Column>
                    <Column field="hotel_id" header="Hotel" :sortable="true" headerStyle="width:14%; min-width:10rem;">
                        <template #body="slotProps">
                            <span class="p-column-title">Hotel</span>
                            {{ getHotelName(slotProps.data.hotel_id) }}
                        </template>
                    </Column>
                    <Column headerStyle="min-width:10rem;">
                        <template #body="slotProps">
                            <Button icon="pi pi-pencil" class="mr-2" severity="success" rounded @click="editProduct(slotProps.data)" />
                            <RouterLink :to="`/items/room/${slotProps.data.id}/guests`"><Button icon="pi pi-users" class="mr-2" rounded/></RouterLink>
                            <Button icon="pi pi-trash" class="mt-2" severity="warning" rounded @click="confirmDeleteProduct(slotProps.data)" />
                        </template>
                    </Column>
                </DataTable>

                <Dialog v-model:visible="productDialog" :style="{ width: '450px' }" header="Product Details" :modal="true" class="p-fluid">
                    <img :src="'/demo/images/product/' + product.image" :alt="product.image" v-if="product.image" width="150" class="mt-0 mx-auto mb-5 block shadow-2" />
                    <div class="field">
                        <label for="number">Number</label>
                        <InputText id="number" v-model.trim="product.number" required="true" autofocus :invalid="submitted && !product.number" />
                        <small class="p-invalid" v-if="submitted && !product.number">Number is required.</small>
                    </div>

                    <div class="field">
                        <label for="type_id" class="mb-3">Type_id</label>
                        <Dropdown id="type_id" v-model="product.type_id" :options="typesId" optionLabel="label" placeholder="Select a Type">
                            <template #value="slotProps">
                                <div v-if="slotProps.value && slotProps.value.value">
                                    <span :class="'product-badge'">{{ slotProps.value.label }}</span>
                                </div>
                                <div v-else-if="slotProps.value && !slotProps.value.value">
                                    <span :class="'product-badge'">{{ getTypeName(slotProps.value) }}</span>
                                </div>
                                <span v-else>
                                    {{ slotProps.placeholder }}
                                </span>
                            </template>
                        </Dropdown>
                    </div>

                    <div class="field">
                        <label for="hotel_id" class="mb-3">Hotel_id</label>
                        <Dropdown id="hotel_id" v-model="product.hotel_id" :options="hotelsId" optionLabel="label" placeholder="Select a Hotel_id">
                            <template #value="slotProps">
                                <div v-if="slotProps.value && slotProps.value.value">
                                    <span :class="'product-badge'">{{ slotProps.value.label }}</span>
                                </div>
                                <div v-else-if="slotProps.value && !slotProps.value.value">
                                    <span :class="'product-badge'">{{ getHotelName(slotProps.value) }}</span>
                                </div>
                                <span v-else>
                                    {{ slotProps.placeholder }}
                                </span>
                            </template>
                        </Dropdown>
                    </div>
                    
                    <template #footer>
                        <Button label="Cancel" icon="pi pi-times" text="" @click="hideDialog" />
                        <Button label="Save" icon="pi pi-check" text="" @click="saveProduct" />
                    </template>
                </Dialog>

                <Dialog v-model:visible="deleteProductDialog" :style="{ width: '450px' }" header="Confirm" :modal="true">
                    <div class="flex align-items-center justify-content-center">
                        <i class="pi pi-exclamation-triangle mr-3" style="font-size: 2rem" />
                        <span v-if="product">Are you sure you want to delete <b>{{ product.number }}</b>?</span>
                    </div>
                    <template #footer>
                        <Button label="No" icon="pi pi-times" text @click="deleteProductDialog = false" />
                        <Button label="Yes" icon="pi pi-check" text @click="deleteProduct" />
                    </template>
                </Dialog>

                <Dialog v-model:visible="deleteProductsDialog" :style="{ width: '450px' }" header="Confirm" :modal="true">
                    <div class="flex align-items-center justify-content-center">
                        <i class="pi pi-exclamation-triangle mr-3" style="font-size: 2rem" />
                        <span v-if="product">Are you sure you want to delete the selected products?</span>
                    </div>
                    <template #footer>
                        <Button label="No" icon="pi pi-times" text @click="deleteProductsDialog = false" />
                        <Button label="Yes" icon="pi pi-check" text @click="deleteSelectedProducts" />
                    </template>
                </Dialog>
            </div>
        </div>
    </div>
</template>