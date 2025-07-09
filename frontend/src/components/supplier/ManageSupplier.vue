<template>
    <Dialog :open="open" @update:open="emit('update:open', $event)">
        <DialogContent close-button>
            <DialogHeader class="flex flex-row items-center">
                {{ title }}
            </DialogHeader>
            <form @submit="onSubmit">
            </form>
            <DialogFooter>
                <div class="flex items-center justify-end w-full">

                    <Button variant="ghost" @click.prevent="saveSupplier">
                        Salvar
                    </Button>
                </div>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>

<script setup lang="ts">
import {
    Dialog,
    DialogContent,
    DialogFooter,
    DialogHeader,
} from '@/components/ui/dialog'

import {
    Stepper,
    StepperIndicator,
    StepperItem,
    StepperSeparator,
    StepperTrigger,
} from '@/components/ui/stepper'
import { Button } from '@/components/ui/button'
import { computed } from 'vue'
import { toTypedSchema } from '@vee-validate/zod'
import * as z from 'zod'
import { useForm } from 'vee-validate'
import { User, Map } from 'lucide-vue-next'
import { ref } from 'vue'
import { Check, Circle, Dot } from 'lucide-vue-next'
const emit = defineEmits(['update:open'])
const props = defineProps<{
    supplier_id?: number
    open: boolean
}>()

const title = computed(() => {
    return props.supplier_id ? `Editar Fornecedor` : 'Adicionar Fornecedor'
})

const step = ref(1)
const steps = [
    {
        step: 1,
        title: 'Informações do Fornecedor',
        disabled: false
    },
    {
        step: 2,
        title: 'Localização',
        disabled: true
    },
]

const setStep = (newStep: number) => {
    if (newStep < 1 || newStep > 2) return
    step.value = newStep
}

const formSchema = toTypedSchema(z.object({
    name: z.string().min(2).max(50),
    email: z.string().email().optional(),
    phone: z.string().optional(),
    document: z.string().optional(),
}))

const form = useForm({
    validationSchema: formSchema,
})

const onSubmit = form.handleSubmit((values) => {
    console.log('Form submitted!', values)
})

const saveSupplier = () => {
    console.log('Saving supplier with ID:', props.supplier_id)
    emit('update:open', false)
}
</script>