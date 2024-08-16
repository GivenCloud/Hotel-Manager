<script lang="ts" setup>
import { FilterMatchMode } from 'primevue/api';
import { ref, onMounted, onBeforeMount } from 'vue';
import { useType } from '../../../service/useType';
import { useToast } from 'primevue/usetoast';
import { type Type } from '@/types/type';
import apiClient from 'axios';

const toast = useToast();

const products = ref<Type[]>([]);
const productDialog = ref(false);
const deleteProductDialog = ref(false);
const deleteProductsDialog = ref(false);
const product = ref<Type>({ id: null, name: '', price: 0, capacity: 0 });
const selectedProducts = ref<Type[]>([]);
const dt = ref(null);
const filters = ref({});
const submitted = ref(false);

const useTypes = new useType();

onBeforeMount(() => {
    initFilters();
});

onMounted(async () => {
    try {
        products.value = await useTypes.getTypes();
    } catch (error) {
        console.error('Error loading types:', error);
        products.value = [];
    }
});

const openNew = () => {
    product.value = { id: null, name: '', price: 0, capacity: 0 };
    submitted.value = false;
    productDialog.value = true;
};

const hideDialog = () => {
    productDialog.value = false;
    submitted.value = false;
};

const formatCurrency = (value: number): string => {
    return value.toLocaleString('en-US', { style: 'currency', currency: 'EUR' });
};

const saveProduct = async () => {
    submitted.value = true;

    if (!product.value.name || isNaN(product.value.price) || isNaN(product.value.capacity)) {
        toast.add({ severity: 'error', summary: 'Error', detail: 'All fields are required', life: 3000 });
        return;
    }

    try {
        let response;

        if (product.value.id) {
            response = await apiClient.put(`http://hotel-manager.test/api/type/${product.value.id}`, {
                name: product.value.name,
                price: Number(product.value.price),
                capacity: Number(product.value.capacity),
            }, {
                headers: {
                    Authorization: `Bearer ${localStorage.getItem('token')}` // Include the token in the header
                }
            });

            const index = products.value.findIndex(p => p.id === product.value.id);
            if (index !== -1) {
                products.value[index] = response.data;
            }

            toast.add({ severity: 'success', summary: 'Successful', detail: 'Type Updated', life: 3000 });
        } else {
            response = await apiClient.post('http://hotel-manager.test/api/type', {
                name: product.value.name,
                price: Number(product.value.price),
                capacity: Number(product.value.capacity),
            }, {
                headers: {
                    Authorization: `Bearer ${localStorage.getItem('token')}` // Include the token in the header
                }
            });

            products.value.push(response.data);
            toast.add({ severity: 'success', summary: 'Successful', detail: 'Type Created', life: 3000 });
        }

        productDialog.value = false;
        product.value = { id: null, name: '', price: 0, capacity: 0 };

    } catch (error: unknown) {
        handleSaveProductError(error);
    }
};

const handleSaveProductError = (error: unknown) => {
    if ((error as any).response?.data?.errors) {
        const errors: Record<string, string[]> = (error as any).response.data.errors;
        let errorMessage = 'Validation errors:';

        for (const [field, messages] of Object.entries(errors)) {
            messages.forEach((message) => {
                errorMessage += `\n- ${message}`;
            });
        }

        toast.add({ severity: 'error', summary: 'Validation Error', detail: errorMessage, life: 5000 });
    } else {
        toast.add({ severity: 'error', summary: 'Error', detail: 'Could not save type', life: 3000 });
    }
};

const editProduct = (editProduct: Type) => {
    product.value = { ...editProduct };
    productDialog.value = true;
};

const confirmDeleteProduct = (editProduct: Type) => {
    product.value = editProduct;
    deleteProductDialog.value = true;
};

const deleteProduct = async () => {
    try {
        await apiClient.delete(`http://hotel-manager.test/api/type/${product.value.id}`, {
            headers: {
                Authorization: `Bearer ${localStorage.getItem('token')}` // Include the token in the header
            }
        });
        products.value = products.value.filter(p => p.id !== product.value.id);
        deleteProductDialog.value = false;
        product.value = { id: null, name: '', price: 0, capacity: 0 };
        toast.add({ severity: 'success', summary: 'Successful', detail: 'Type Deleted', life: 3000 });
    } catch (error) {
        toast.add({ severity: 'error', summary: 'Error', detail: 'Could not delete type', life: 3000 });
    }
};

const confirmDeleteSelected = () => {
    deleteProductsDialog.value = true;
};

const deleteSelectedProducts = async () => {
    try {
        await Promise.all(selectedProducts.value.map(type =>
            apiClient.delete(`http://hotel-manager.test/api/type/${type.id}`, {
                headers: {
                    Authorization: `Bearer ${localStorage.getItem('token')}` // Include the token in the header
                }
            })
        ));

        products.value = products.value.filter(p => !selectedProducts.value.includes(p));
        deleteProductsDialog.value = false;
        selectedProducts.value = [];
        toast.add({ severity: 'success', summary: 'Successful', detail: 'Types Deleted', life: 3000 });
    } catch (error) {
        toast.add({ severity: 'error', summary: 'Error', detail: 'Could not delete types', life: 3000 });
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
                            <Button label="Delete" icon="pi pi-trash" severity="danger" @click="confirmDeleteSelected" :disabled="!selectedProducts.length" />
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
                    currentPageReportTemplate="Showing {first} to {last} of {totalRecords} types"
                >
                    <template #header>
                        <div class="flex flex-column md:flex-row md:justify-content-between md:align-items-center">
                            <h5 class="m-0">Manage Types</h5>
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
                    <Column field="price" header="Price" :sortable="true" headerStyle="width:14%; min-width:8rem;">
                        <template #body="slotProps">
                            <span class="p-column-title">Price</span>
                            {{ formatCurrency(slotProps.data.price) }}
                        </template>
                    </Column>
                    <Column field="capacity" header="Capacity" :sortable="true" headerStyle="width:14%; min-width:10rem;">
                        <template #body="slotProps">
                            <span class="p-column-title">Capacity</span>
                            {{ slotProps.data.capacity }}
                        </template>
                    </Column>
                    <Column headerStyle="min-width:10rem;">
                        <template #body="slotProps">
                            <Button icon="pi pi-pencil" class="mr-2" severity="success" rounded @click="editProduct(slotProps.data)" />
                            <Button icon="pi pi-trash" class="mt-2" severity="warning" rounded @click="confirmDeleteProduct(slotProps.data)" />
                        </template>
                    </Column>
                </DataTable>

                <Dialog v-model:visible="productDialog" :style="{ width: '450px' }" header="Product Details" :modal="true" class="p-fluid">
                    <img v-if="product.image" :src="'/demo/images/product/' + product.image" :alt="product.image" width="150" class="mt-0 mx-auto mb-5 block shadow-2" />
                    <div class="field">
                        <label for="name">Name</label>
                        <InputText id="name" v-model.trim="product.name" required autofocus :invalid="submitted && !product.name" />
                        <small class="p-invalid" v-if="submitted && !product.name">Name is required.</small>
                    </div>
                    <div class="field">
                        <label for="price">Price</label>
                        <InputText id="price" v-model.trim="product.price" type="number" required autofocus :invalid="submitted && !product.price" />
                        <small class="p-invalid" v-if="submitted && !product.price">Price is required.</small>
                    </div>
                    <div class="field">
                        <label for="capacity">Capacity</label>
                        <InputText id="capacity" v-model.trim="product.capacity" type="number" required autofocus :invalid="submitted && !product.capacity" />
                        <small class="p-invalid" v-if="submitted && !product.capacity">Capacity is required.</small>
                    </div>
                    <template #footer>
                        <Button label="Cancel" icon="pi pi-times" text @click="hideDialog" />
                        <Button label="Save" icon="pi pi-check" text @click="saveProduct" />
                    </template>
                </Dialog>

                <Dialog v-model:visible="deleteProductDialog" :style="{ width: '450px' }" header="Confirm" :modal="true">
                    <div class="flex align-items-center justify-content-center">
                        <i class="pi pi-exclamation-triangle mr-3" style="font-size: 2rem" />
                        <span v-if="product">Are you sure you want to delete <b>{{ product.name }}</b>?</span>
                    </div>
                    <template #footer>
                        <Button label="No" icon="pi pi-times" text @click="deleteProductDialog = false" />
                        <Button label="Yes" icon="pi pi-check" text @click="deleteProduct" />
                    </template>
                </Dialog>

                <Dialog v-model:visible="deleteProductsDialog" :style="{ width: '450px' }" header="Confirm" :modal="true">
                    <div class="flex align-items-center justify-content-center">
                        <i class="pi pi-exclamation-triangle mr-3" style="font-size: 2rem" />
                        <span v-if="selectedProducts.length">Are you sure you want to delete the selected types?</span>
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
