<template>
    <div class="home">
        <Header />
        <div class="flex items-center justify-center">
            <div class="pt-16 flex items-center w-full justify-between max-w-4xl">
                <div></div>
                <Button aria-label="Registrar novo fornecedor" variant="ghost">
                    <Plus class="w-4 h-4" />
                    <span>Registrar Novo</span>
                </Button>
            </div>
        </div>
        <Footer />
    </div>
</template>

<script lang="ts" setup>
import { getSuppliers } from '@/services/suppliers';
import Header from '@/components/ui/header/Header.vue';
import Footer from '@/components/ui/footer/Footer.vue';
import { ref, onMounted } from 'vue';
import { Button } from '@/components/ui/button';
import { Plus } from 'lucide-vue-next';
import type { SupplierList } from '@/types/Supplier';

const suppliers = ref<SupplierList>([]);



onMounted(async () => {
    const response = await getSuppliers();
    suppliers.value = response.data;
});
</script>