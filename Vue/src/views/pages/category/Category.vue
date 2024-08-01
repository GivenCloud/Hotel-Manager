<script lang="ts" setup>
import { FilterMatchMode } from 'primevue/api';
import { ref, onMounted, onBeforeMount } from 'vue';
import { useCategory } from '../../../service/useCategory';
import { useToast } from 'primevue/usetoast';
import { type Category } from '@/types/category';
import axios from 'axios';

const toast = useToast();

const products = ref(null);
const productDialog = ref(false);
const deleteProductDialog = ref(false);
const deleteProductsDialog = ref(false);
const product = ref<Category>({ value: null });
const selectedProducts = ref(null);
const dt = ref(null);
const filters = ref({});
const submitted = ref(false);

const useCategories = new useCategory();

onBeforeMount(() => {
    initFilters();
});
onMounted(() => {
    useCategories.getCategories().then((data) => (products.value = data));
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
    if (!product.value.name) {
        toast.add({ severity: 'error', summary: 'Error', detail: 'All fields are required', life: 3000 });
        return;
    }

    try {
        let response;

        if (product.value.id) {
            // Realiza la petición PUT a la API de Laravel para actualizar el category
            response = await axios.put(`http://hotel-manager.test/api/category/${product.value.id}`, {
                name: product.value.name,
            });

            // Actualiza el category en la lista local
            const index = products.value.findIndex((p:any) => p.id === product.value.id);
            if (index !== -1) {
                products.value[index] = response.data;
            }

            // Muestra una notificación de éxito
            toast.add({ severity: 'success', summary: 'Successful', detail: 'Category Updated', life: 3000 });
        } else {
            // Realiza la petición POST a la API de Laravel para crear un nuevo category
            response = await axios.post('http://hotel-manager.test/api/category', {
                name: product.value.name,
            });

            // Añade el nuevo category a la lista local
            products.value.push(response.data);

            // Muestra una notificación de éxito
            toast.add({ severity: 'success', summary: 'Successful', detail: 'Category Created', life: 3000 });
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
            toast.add({ severity: 'error', summary: 'Error', detail: 'Could not save category', life: 3000 });
        }
    }
};

const editProduct = (editProduct: Category) => {
    product.value = { ...editProduct };
    productDialog.value = true;
};

const confirmDeleteProduct = (editProduct: Category) => {
    product.value = editProduct;
    deleteProductDialog.value = true;
};

const deleteProduct = async () => {
    try {
        // Realiza la petición DELETE a la API de Laravel
        await axios.delete(`http://hotel-manager.test/api/category/${product.value.id}`);

        // Filtra el producto eliminado de la lista local
        products.value = products.value.filter((val: { id: any; }) => val.id !== product.value.id);
        
        // Cierra el diálogo de confirmación
        deleteProductDialog.value = false;
        
        // Limpia el producto seleccionado
        product.value = [];
        
        // Muestra una notificación de éxito
        toast.add({ severity: 'success', summary: 'Successful', detail: 'Category Deleted', life: 3000 });
    } catch (error) {
        // Maneja errores en la petición DELETE
        console.error('Error deleting category:', error);
        toast.add({ severity: 'error', summary: 'Error', detail: 'Could not delete category', life: 3000 });
    }
};

const confirmDeleteSelected = () => {
    deleteProductsDialog.value = true;
};

const deleteSelectedProducts = async () => {
    try {
        // Realiza todas las peticiones DELETE concurrentemente
        await Promise.all(selectedProducts.value.map((category: Category) => 
            axios.delete(`http://hotel-manager.test/api/category/${category.id}`)
        ));

        // Filtra los categories locales eliminando los seleccionados
        products.value = products.value.filter((val:any) => !selectedProducts.value.includes(val));
        
        // Cierra el diálogo de eliminación
        deleteProductsDialog.value = false;
        selectedProducts.value = null;

        // Muestra una notificación de éxito
        toast.add({ severity: 'success', summary: 'Successful', detail: 'Categories Deleted', life: 3000 });
    } catch (error) {
        // Maneja errores en la petición DELETE
        console.error('Error deleting categories:', error);
        toast.add({ severity: 'error', summary: 'Error', detail: 'Could not delete categories', life: 3000 });
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
                            <h5 class="m-0">Manage Categories</h5>
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
                    <Column headerStyle="min-width:10rem;">
                        <template #body="slotProps">
                            <Button icon="pi pi-pencil" class="mr-2" severity="success" rounded @click="editProduct(slotProps.data)" />
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
