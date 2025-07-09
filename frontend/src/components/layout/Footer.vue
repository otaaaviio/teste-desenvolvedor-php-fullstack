<template>
    <footer class="flex items-center justify-center p-4 border-t fixed bottom-0 left-0 right-0 bg-background">
        <div class="flex items-center justify-between w-full max-w-4xl">
            <div class="text-sm text-muted-foreground">
                &copy; {{ new Date().getFullYear() }} - <span>Otávio G.</span>
            </div>
            <div class="flex items-center gap-1">
                <Button v-for="sm in socialMedias" :key="sm.name" variant="ghost" size="icon"
                    @click="sm.handleClick">
                    <component :is="sm.icon" class="w-5 h-5" />
                </Button>
            </div>
        </div>
        <Toaster position="top-center"/>
    </footer>
</template>

<script lang="ts" setup>
import { ref } from 'vue';
import { Button } from '@/components/ui/button';
import { Github, Linkedin, Mail } from 'lucide-vue-next';
import { Toaster, toast } from 'vue-sonner'

const copyMailToClipboard = () => {
    navigator.clipboard.writeText('otavio18gl@gmail.com');
    const secondary = getComputedStyle(document.documentElement).getPropertyValue('--secondary').trim();

    toast.success('Email copied to clipboard!', {
        style: {
            background: secondary,
            border: `1px solid ${secondary}`,
        },
        duration: 800,
        class: 'bg-secondary text-secondary-foreground',
        descriptionClass: 'my-toast-description'
    });
};

const redirectTo = (url: string) => window.open(url, '_blank');

const socialMedias = ref([
    {
        name: 'Github',
        icon: Github,
        handleClick: () => redirectTo('https://github.com/otaaaviio'),
    },
    {
        name: 'Linkedin',
        icon: Linkedin,
        handleClick: () => redirectTo('https://www.linkedin.com/in/otaaaviio/'),
    },
    {
        name: 'Mail',
        icon: Mail,
        handleClick: copyMailToClipboard,
    },
]);
</script>