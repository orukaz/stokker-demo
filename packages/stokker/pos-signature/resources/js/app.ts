import { createApp } from 'vue';
import '../css/app.css';
import type { SignatureResult } from './signature';
import SignatureDemo from './SignatureDemo.vue';

function downloadSignature(signature: SignatureResult): void {
    const url = URL.createObjectURL(signature.blob);
    const link = document.createElement('a');

    link.href = url;
    link.download = `pos-allkiri.${signature.format}`;
    document.body.appendChild(link);
    link.click();
    link.remove();

    window.setTimeout(() => URL.revokeObjectURL(url), 1000);
}

const root = document.getElementById('pos-signature-app');

if (root) {
    createApp(SignatureDemo, {
        onConfirmed: downloadSignature,
    }).mount(root);
}
