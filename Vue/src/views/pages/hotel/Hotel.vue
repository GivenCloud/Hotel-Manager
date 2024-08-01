<script lang="ts" setup>
import { FilterMatchMode } from 'primevue/api';
import { ref, onMounted, onBeforeMount } from 'vue';
import { useHotel } from '../../../service/useHotel';
import { useToast } from 'primevue/usetoast';
import { type Hotel } from '@/types/hotel';
import axios from 'axios';

const toast = useToast();
const products = ref<Hotel[]>([]);
const productDialog = ref(false);
const deleteProductDialog = ref(false);
const deleteProductsDialog = ref(false);
const product = ref<Hotel | null>(null);
const selectedProducts = ref<Hotel[]>([]);
const dt = ref(null);
const filters = ref({});
const submitted = ref(false);

const useHotels = new useHotel();

onBeforeMount(() => {
    initFilters();
});

onMounted(async () => {
    try {
        products.value = await useHotels.getHotels();
    } catch (error) {
        toast.add({ severity: 'error', summary: 'Error', detail: 'Failed to load hotels', life: 3000 });
    }
});

const openNew = () => {
    product.value = {} as Hotel;
    submitted.value = false;
    productDialog.value = true;
};

const hideDialog = () => {
    productDialog.value = false;
    submitted.value = false;
};

const saveProduct = async () => {
    submitted.value = true;
    const { name, address, stars, phone, email, website, image } = product.value || {};

    if (!name || !address || !stars || !phone || !email || !website || !image) {
        toast.add({ severity: 'error', summary: 'Error', detail: 'All fields are required', life: 3000 });
        return;
    }

    try {
        const response = product.value?.id 
            ? await axios.put(`http://hotel-manager.test/api/hotel/${product.value.id}`, { name, address, stars, phone, email, website, image })
            : await axios.post('http://hotel-manager.test/api/hotel', { name, address, stars, phone, email, website, image });

        const newProduct = response.data;

        if (product.value?.id) {
            const index = products.value.findIndex(p => p.id === product.value.id);
            if (index !== -1) products.value[index] = newProduct;
            toast.add({ severity: 'success', summary: 'Successful', detail: 'Hotel Updated', life: 3000 });
        } else {
            products.value.push(newProduct);
            toast.add({ severity: 'success', summary: 'Successful', detail: 'Hotel Created', life: 3000 });
        }

        hideDialog();
    } catch (error: any) {
        handleApiError(error);
    }
};

const handleApiError = (error: any) => {
    if (error.response && error.response.data && error.response.data.errors) {
        let errorMessage = 'Validation errors:';
        for (const [field, messages] of Object.entries(error.response.data.errors)) {
            messages.forEach((message: string) => errorMessage += `\n- ${message}`);
        }
        toast.add({ severity: 'error', summary: 'Validation Error', detail: errorMessage, life: 5000 });
    } else {
        toast.add({ severity: 'error', summary: 'Error', detail: 'Could not process request', life: 3000 });
    }
};

const editProduct = (editProduct: Hotel) => {
    product.value = { ...editProduct };
    productDialog.value = true;
};

const confirmDeleteProduct = (editProduct: Hotel) => {
    product.value = editProduct;
    deleteProductDialog.value = true;
};

const deleteProduct = async () => {
    try {
        await axios.delete(`http://hotel-manager.test/api/hotel/${product.value?.id}`);
        products.value = products.value.filter(p => p.id !== product.value?.id);
        deleteProductDialog.value = false;
        toast.add({ severity: 'success', summary: 'Successful', detail: 'Hotel Deleted', life: 3000 });
    } catch (error) {
        handleApiError(error);
    }
};

const confirmDeleteSelected = () => {
    deleteProductsDialog.value = true;
};

const deleteSelectedProducts = async () => {
    try {
        await Promise.all(selectedProducts.value.map((hotel: Hotel) => 
            axios.delete(`http://hotel-manager.test/api/hotel/${hotel.id}`)
        ));
        products.value = products.value.filter((p: any) => !selectedProducts.value.some(selected => selected.id === p.id));
        deleteProductsDialog.value = false;
        selectedProducts.value = [];
        toast.add({ severity: 'success', summary: 'Successful', detail: 'Hotels Deleted', life: 3000 });
    } catch (error) {
        handleApiError(error);
    }
};

const initFilters = () => {
    filters.value = {
        global: { value: null, matchMode: FilterMatchMode.CONTAINS }
    };
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
                            <h5 class="m-0">Manage Hotels</h5>
                            <IconField iconPosition="left" class="block mt-2 md:mt-0">
                                <InputIcon class="pi pi-search" />
                                <InputText class="w-full sm:w-auto" v-model="filters['global'].value" placeholder="Search..." />
                            </IconField>
                        </div>
                    </template>

                    <Column selectionMode="multiple" headerStyle="width: 3rem"></Column>
                    <Column field="name" header="Name" :sortable="true" headerStyle="width:14%; min-width:10rem;">
                        <template #body="slotProps">
                            <span class="p-column-title">Name</span>
                            {{ slotProps.data.name }}
                        </template>
                    </Column>
                    <Column header="Image" headerStyle="width:14%; min-width:10rem;">
                        <template #body="slotProps">
                            <span class="p-column-title">Image</span>
                            <img :src="slotProps.data.image" :alt="slotProps.data.image" class="shadow-2" width="100" />
                        </template>
                    </Column>
                    <Column field="address" header="Address" :sortable="true" headerStyle="width:14%; min-width:10rem;">
                        <template #body="slotProps">
                            <span class="p-column-title">Address</span>
                            {{ slotProps.data.address }}
                        </template>
                    </Column>
                    <Column field="stars" header="Stars" :sortable="true" headerStyle="width:14%; min-width:10rem;">
                        <template #body="slotProps">
                            <span class="p-column-title">Stars</span>
                            <Rating :modelValue="slotProps.data.stars" :readonly="true" :cancel="false" />
                        </template>
                    </Column>
                    <Column field="phone" header="Phone" :sortable="true" headerStyle="width:14%; min-width:10rem;">
                        <template #body="slotProps">
                            <span class="p-column-title">Phone</span>
                            {{ slotProps.data.phone }}
                        </template>
                    </Column>
                    <Column field="email" header="Email" :sortable="true" headerStyle="width:14%; min-width:10rem;">
                        <template #body="slotProps">
                            <span class="p-column-title">Email</span>
                            {{ slotProps.data.email }}
                        </template>
                    </Column>
                    <Column field="website" header="Website" :sortable="true" headerStyle="width:14%; min-width:10rem;">
                        <template #body="slotProps">
                            <span class="p-column-title">Website</span>
                            <a :href="slotProps.data.website">{{ slotProps.data.website }}</a>
                        </template>
                    </Column>
                    <Column headerStyle="min-width:10rem;">
                        <template #body="slotProps">
                            <Button icon="pi pi-pencil" class="mr-2" severity="success" rounded @click="editProduct(slotProps.data)" />
                            <RouterLink :to="`/items/hotel/${slotProps.data.id}/services`"><Button icon="pi pi-table" class="mr-2" rounded/></RouterLink>
                            <Button icon="pi pi-trash" class="mt-2" severity="warning" rounded @click="confirmDeleteProduct(slotProps.data)" />
                        </template>
                    </Column>
                </DataTable>

                <Dialog v-model:visible="productDialog" :style="{ width: '450px' }" header="Product Details" :modal="true" class="p-fluid">
                    <img :src="product.image" :alt="product.image" v-if="product.image" width="150" class="mt-0 mx-auto mb-5 block shadow-2" />
                    <div class="field">
                        <label for="name">Name</label>
                        <InputText id="name" v-model.trim="product.name" required="true" autofocus :invalid="submitted && !product.name" />
                        <small class="p-invalid" v-if="submitted && !product.name">Name is required.</small>
                    </div>
                    <div class="field">
                        <label for="address">Address</label>
                        <InputText id="address" v-model.trim="product.address" required="true" autofocus :invalid="submitted && !product.address" />
                        <small class="p-invalid" v-if="submitted && !product.address">Address is required.</small>
                    </div>
                    <div class="field">
                        <label for="stars">Stars</label>
                        <InputText id="stars" v-model.trim="product.stars" required="true" autofocus :invalid="submitted && !product.stars" />
                        <small class="p-invalid" v-if="submitted && !product.stars">Stars is required.</small>
                    </div>
                    <div class="field">
                        <label for="phone">Phone</label>
                        <InputText id="phone" v-model.trim="product.phone" required="true" autofocus :invalid="submitted && !product.phone" />
                        <small class="p-invalid" v-if="submitted && !product.phone">Phone is required.</small>
                    </div>
                    <div class="field">
                        <label for="email">Email</label>
                        <InputText id="email" v-model.trim="product.email" required="true" autofocus :invalid="submitted && !product.email" />
                        <small class="p-invalid" v-if="submitted && !product.email">Email is required.</small>
                    </div>
                    <div class="field">
                        <label for="website">Website</label>
                        <InputText id="website" v-model.trim="product.website" required="true" autofocus :invalid="submitted && !product.website" />
                        <small class="p-invalid" v-if="submitted && !product.website">Website is required.</small>
                    </div>
                    <div class="field">
                        <label for="image">Image</label>
                        <InputText id="image" v-model.trim="product.image" required="false" autofocus :invalid="submitted && !product.image"/>
                        <small class="p-invalid" v-if="submitted && !product.image">Image is required.</small>
                    </div>
                    <template #footer>
                        <Button label="Cancel" icon="pi pi-times" text="" @click="hideDialog" />
                        <Button label="Save" icon="pi pi-check" text="" @click="saveProduct" />
                    </template>
                </Dialog>


                <Dialog v-model:visible="deleteProductDialog" :style="{ width: '450px' }" header="Confirm" :modal="true">
                    <div class="flex align-items-center justify-content-center">
                        <i class="pi pi-exclamation-triangle mr-3" style="font-size: 2rem" />
                        <span v-if="product"
                            >Are you sure you want to delete <b>{{ product.name }}</b
                            >?</span
                        >
                    </div>
                    <template #footer>
                        <Button label="No" icon="pi pi-times" text @click="deleteProductDialog = false" />
                        <Button label="Yes" icon="pi pi-check" text @click="deleteProduct" />
                    </template>
                </Dialog>

                <Dialog v-model:visible="deleteProductsDialog" :style="{ width: '450px' }" header="Confirm" :modal="true">
                    <div class="flex align-items-center justify-content-center">
                        <i class="pi pi-exclamation-triangle mr-3" style="font-size: 2rem" />
                        <span>Are you sure you want to delete the selected products?</span>
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
