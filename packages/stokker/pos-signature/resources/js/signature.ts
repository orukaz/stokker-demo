export type SignatureOutputFormat = 'png' | 'svg';

export type SignaturePadProps = {
    outputFormat?: SignatureOutputFormat;
    outputWidth?: number;
    outputHeight?: number;
    signatureLabel?: string;
};

export type SignatureResult = {
    blob: Blob;
    format: SignatureOutputFormat;
    mimeType: 'image/png' | 'image/svg+xml';
    width: number;
    height: number;
};
