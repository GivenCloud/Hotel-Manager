<script lang="ts" setup>
import { FilterMatchMode } from 'primevue/api';
import { ref, onMounted, onBeforeMount } from 'vue';
import { useService } from '../../../service/useService';
import { useCategory } from '@/service/useCategory';
import { useToast } from 'primevue/usetoast';
import { type Service } from '@/types/service';
import axios from 'axios';

const toast = useToast();

const products = ref(null);
const productDialog = ref(false);
const deleteProductDialog = ref(false);
const deleteProductsDialog = ref(false);
const product = ref<Service>({ value: null });
const selectedProducts = ref(null);
const dt = ref(null);
const filters = ref({});
const submitted = ref(false);

const categoriesId = ref(null);

const useServices = new useService();
const useCategories = new useCategory();

onBeforeMount(() => {
    initFilters();
});
onMounted(() => {
    useServices.getServices().then((data) => (products.value = data));

    useCategories.getCategories().then((data) => {
        categoriesId.value = data.map((category) => ({ label: category.name, value: category.id }));
    }).catch(error => {
        console.error('Error loading categories:', error);
        categoriesId.value = [];
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
    if (!product.value.name || !product.value.description || !product.value.category_id) {
        toast.add({ severity: 'error', summary: 'Error', detail: 'All fields are required', life: 3000 });
        return;
    }

    try {
        let response;

        if (product.value.id) {
            let categoryId;
            let hotelId;
            if (typeof product.value.category_id === 'object') {
                categoryId = product.value.category_id.value;
            } else {
                categoryId = product.value.category_id;
            }

            // Realiza la petición PUT a la API de Laravel para actualizar el service
            response = await axios.put(`http://hotel-manager.test/api/service/${product.value.id}`, {
                name: product.value.name,
                description: product.value.description,
                category_id: categoryId,
            });

            // Actualiza el service en la lista local
            const index = products.value.findIndex((p:any) => p.id === product.value.id);
            if (index !== -1) {
                products.value[index] = response.data;
            }

            // Muestra una notificación de éxito
            toast.add({ severity: 'success', summary: 'Successful', detail: 'Service Updated', life: 3000 });
        } else {
            // Realiza la petición POST a la API de Laravel para crear un nuevo service
            response = await axios.post('http://hotel-manager.test/api/service', {
                name: product.value.name,
                description: product.value.description,
                category_id: product.value.category_id.value,
            });

            // Añade el nuevo service a la lista local
            products.value.push(response.data);

            // Muestra una notificación de éxito
            toast.add({ severity: 'success', summary: 'Successful', detail: 'Service Created', life: 3000 });
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
            toast.add({ severity: 'error', summary: 'Error', detail: 'Could not save service', life: 3000 });
        }
    }
};

const editProduct = (editProduct: Service) => {
    if (editProduct) {
        product.value = { ...editProduct };
        productDialog.value = true;
    } else {
        console.error('No product data available for editing.');
    }
};

const confirmDeleteProduct = (editProduct: Service) => {
    product.value = editProduct;
    deleteProductDialog.value = true;
};

const deleteProduct = async () => {
    try {
        // Realiza la petición DELETE a la API de Laravel
        await axios.delete(`http://hotel-manager.test/api/service/${product.value.id}`);

        // Filtra el producto eliminado de la lista local
        products.value = products.value.filter((val: { id: any; }) => val.id !== product.value.id);
        
        // Cierra el diálogo de confirmación
        deleteProductDialog.value = false;
        
        // Limpia el producto seleccionado
        product.value = {};
        
        // Muestra una notificación de éxito
        toast.add({ severity: 'success', summary: 'Successful', detail: 'Service Deleted', life: 3000 });
    } catch (error) {
        // Maneja errores en la petición DELETE
        console.error('Error deleting service:', error);
        toast.add({ severity: 'error', summary: 'Error', detail: 'Could not delete service', life: 3000 });
    }
};

const confirmDeleteSelected = () => {
    deleteProductsDialog.value = true;
};

const deleteSelectedProducts = async () => {
    try {
        // Realiza todas las peticiones DELETE concurrentemente
        await Promise.all(selectedProducts.value.map((service: Service) => 
            axios.delete(`http://hotel-manager.test/api/service/${service.id}`)
        ));

        // Filtra los servicees locales eliminando los seleccionados
        products.value = products.value.filter((val:any) => !selectedProducts.value.includes(val));
        
        // Cierra el diálogo de eliminación
        deleteProductsDialog.value = false;
        selectedProducts.value = null;

        // Muestra una notificación de éxito
        toast.add({ severity: 'success', summary: 'Successful', detail: 'Services Deleted', life: 3000 });
    } catch (error) {
        // Maneja errores en la petición DELETE
        console.error('Error deleting services:', error);
        toast.add({ severity: 'error', summary: 'Error', detail: 'Could not delete services', life: 3000 });
    }
};

const initFilters = () => {
    filters.value = {
        global: { value: null, matchMode: FilterMatchMode.CONTAINS }
    };
};

const getCategoryName = (categoryId: number) => {
    if (categoriesId.value) {
        const category = categoriesId.value.find((category: { value: number; }) => category.value === categoryId);
        return category ? category.label : 'Unknown Category';
    }
    return 'Unknown Category';
    
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
                            <h5 class="m-0">Manage Services</h5>
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
                    <Column field="description" header="Description" :sortable="true" headerStyle="width:14%; min-width:10rem;">
                        <template #body="slotProps">
                            <span class="p-column-title">Description</span>
                            {{ slotProps.data.description }}
                        </template>
                    </Column>
                    <Column field="category_id" header="Category" :sortable="true" headerStyle="width:14%; min-width:10rem;">
                        <template #body="slotProps">
                            <span class="p-column-title">Category</span>
                            <Tag>{{ getCategoryName(slotProps.data.category_id) }}</Tag>
                        </template>
                    </Column>
                    <Column headerStyle="min-width:10rem;">
                        <template #body="slotProps">
                            <Button icon="pi pi-pencil" class="mr-2" severity="success" rounded @click="editProduct(slotProps.data)" />
                            <RouterLink :to="`/items/service/${slotProps.data.id}/hotels`"><Button icon="pi pi-building" class="mr-2" rounded/></RouterLink>
                            <RouterLink :to="`/items/service/${slotProps.data.id}/guests`"><Button icon="pi pi-users" class="mr-2" rounded/></RouterLink>
                            <Button icon="pi pi-trash" class="mt-2" severity="warning" rounded @click="confirmDeleteProduct(slotProps.data)" />
                        </template>
                    </Column>
                </DataTable>

                <Dialog v-model:visible="productDialog" :style="{ width: '450px' }" header="Product Details" :modal="true" class="p-fluid">
                    <img :src="'/demo/images/product/' + product.image" :alt="product.image" v-if="product.image" width="150" class="mt-0 mx-auto mb-5 block shadow-2" />
                    <div class="field">
                        <label for="name">Name</label>
                        <InputText id="name" v-model.trim="product.name" required="true" autofocus :invalid="submitted && !product.name" />
                        <small class="p-invalid" v-if="submitted && !product.name">Name is required.</small>
                    </div>
                    
                    <div class="field">
                        <label for="description">Description</label>
                        <Textarea id="description" v-model.trim="product.description" required="true" autofocus :invalid="submitted && !product.description" placeholder="Your Message" :autoResize="true" rows="3" cols="30" />
                        <small class="p-invalid" v-if="submitted && !product.description">Description is required.</small>
                    </div>

                    <div class="field">
                        <label for="category_id" class="mb-3">Category</label>
                        <Dropdown id="category_id" v-model="product.category_id" :options="categoriesId" optionLabel="label" placeholder="Select a Category">
                            <template #value="slotProps">
                                <div v-if="slotProps.value && slotProps.value.value">
                                    <span :class="'product-badge'">{{ slotProps.value.label }}</span>
                                </div>
                                <div v-else-if="slotProps.value && !slotProps.value.value">
                                    <span :class="'product-badge'">{{ getCategoryName(slotProps.value) }}</span>
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
