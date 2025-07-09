<template>
    <header class="fixed top-0 left-0 right-0 bg-background flex items-center justify-center border-b p-1 sm:p-1">
        <div class="flex items-center justify-between w-full max-w-4xl">
            <span class="font-bold">StartGov - Cadastro de Fornecedores</span>
            <Button variant="ghost" size="icon" @click.prevent="toggleTheme">
                <component :is="themeIcon" class="w-5 h-5" />
            </Button>
        </div>
    </header>
</template>

<script lang="ts" setup>
import { Button } from '@/components/ui/button';
import { Sun, Moon } from 'lucide-vue-next';
import { computed, onMounted, ref } from 'vue';

type Theme = 'light' | 'dark';

const themeIcon = computed(() => (theme.value === 'light' ? Sun : Moon))

const theme = ref<Theme>(
    localStorage.getItem('theme') as Theme || 'light'
);

const applyTheme = () => {
    document.documentElement.classList.toggle('dark', theme.value === 'dark')
}

const toggleTheme = () => {
    theme.value = theme.value === 'light' ? 'dark' : 'light';
    localStorage.setItem('theme', theme.value);
    applyTheme();
};

onMounted(applyTheme);
</script>