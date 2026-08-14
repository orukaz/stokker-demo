export type SignatureOutputFormat = 'png' | 'svg';

export const signatureStrokeColors = [
    { label: 'Black', value: '#111827' },
    { label: 'Stokker Blue', value: '#0075ba' },
    { label: 'Red', value: '#e30613' },
] as const;

export type SignatureStrokeColor =
    (typeof signatureStrokeColors)[number]['value'];

export type SignaturePadProps = {
    outputFormat?: SignatureOutputFormat;
    outputWidth?: number;
    outputHeight?: number;
    signatureLabel?: string;
    initialStrokeColor?: SignatureStrokeColor;
};

export type SignatureResult = {
    blob: Blob;
    format: SignatureOutputFormat;
    mimeType: 'image/png' | 'image/svg+xml';
    width: number;
    height: number;
    strokeColor: SignatureStrokeColor;
};
