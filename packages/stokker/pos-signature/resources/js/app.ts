import { createApp } from 'vue';
import '../css/app.css';
import SignaturePad from './SignaturePad.vue';

function downloadSignature(signature: Blob): void {
    const url = URL.createObjectURL(signature);
    const link = document.createElement('a');

    link.href = url;
    link.download = 'pos-allkiri.png';
    document.body.appendChild(link);
    link.click();
    link.remove();

    window.setTimeout(() => URL.revokeObjectURL(url), 1000);
}

const root = document.getElementById('pos-signature-app');

if (root) {
    createApp(SignaturePad, {
        onConfirmed: downloadSignature,
    }).mount(root);
}
