<template>
    <div class="home">
        <Header />
        <div class="flex items-center justify-center">
            <div class="pt-16 flex items-center w-full justify-between max-w-4xl">
                <div></div>
                <Button aria-label="Registrar novo fornecedor" variant="ghost" @click.prevent="manageOpen = true">
                    <Plus class="w-4 h-4" />
                    <span>Registrar Novo</span>
                </Button>
            </div>
        </div>
        <Footer />
        <ManageSupplier :open="manageOpen" @update:open="manageOpen = $event" />
    </div>
</template>

<script lang="ts" setup>
import { getSuppliers } from '@/services/suppliers';
import { Header, Footer } from '@/components/layout';
import { ref, onMounted } from 'vue';
import { Button } from '@/components/ui/button';
import { Plus } from 'lucide-vue-next';
import ManageSupplier from '@/components/supplier/ManageSupplier.vue';
import type { SupplierList } from '@/types/Supplier';

const suppliers = ref<SupplierList>([]);
const manageOpen = ref(true);

onMounted(async () => {
    const response = await getSuppliers();
    suppliers.value = response.data;
});
</script>