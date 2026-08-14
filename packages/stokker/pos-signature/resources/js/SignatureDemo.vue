<script setup lang="ts">
import { ref } from 'vue';

import { Button } from './components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogTitle,
    DialogTrigger,
} from './components/ui/dialog';
import type { SignatureResult } from './signature';
import SignaturePad from './SignaturePad.vue';

const emit = defineEmits<{
    confirmed: [signature: SignatureResult];
}>();

const open = ref(false);
const signaturePadKey = ref(0);

function updateOpen(value: boolean): void {
    if (value && !open.value) {
        signaturePadKey.value += 1;
    }

    open.value = value;
}

function confirmSignature(signature: SignatureResult): void {
    emit('confirmed', signature);
    open.value = false;
}
</script>

<template>
    <main class="grid min-h-dvh place-items-center bg-muted/35 p-6">
        <Dialog :open="open" @update:open="updateOpen">
            <DialogTrigger as-child>
                <Button type="button" size="lg">Sign</Button>
            </DialogTrigger>

            <DialogContent>
                <DialogTitle class="sr-only">Add signature</DialogTitle>
                <DialogDescription class="sr-only">
                    Draw a signature and confirm the transparent PNG file.
                </DialogDescription>

                <SignaturePad
                    :key="signaturePadKey"
                    output-format="png"
                    :output-width="1024"
                    :output-height="384"
                    signature-label="Signature"
                    @confirmed="confirmSignature"
                />
            </DialogContent>
        </Dialog>
    </main>
</template>
