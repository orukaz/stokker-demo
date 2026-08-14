<script setup lang="ts">
import { Check, Redo2, RotateCcw, Undo2 } from '@lucide/vue';
import { computed, onBeforeUnmount, onMounted, ref } from 'vue';

import {
    AlertDialog,
    AlertDialogAction,
    AlertDialogCancel,
    AlertDialogContent,
    AlertDialogDescription,
    AlertDialogFooter,
    AlertDialogHeader,
    AlertDialogTitle,
    AlertDialogTrigger,
} from './components/ui/alert-dialog';
import { Button } from './components/ui/button';
import {
    Tooltip,
    TooltipContent,
    TooltipProvider,
    TooltipTrigger,
} from './components/ui/tooltip';
import type {
    SignatureOutputFormat,
    SignaturePadProps,
    SignatureResult,
} from './signature';

type Point = {
    x: number;
    y: number;
};

type Stroke = Point[];

type Size = {
    width: number;
    height: number;
};

type Bounds = {
    minX: number;
    minY: number;
    maxX: number;
    maxY: number;
};

type StrokeLayout = {
    points: Point[][];
    strokeWidth: number;
};

const props = withDefaults(defineProps<SignaturePadProps>(), {
    outputFormat: 'png',
    outputWidth: 1024,
    outputHeight: 384,
    signatureLabel: 'Signature',
});

const emit = defineEmits<{
    confirmed: [signature: SignatureResult];
}>();

const canvasElement = ref<HTMLCanvasElement | null>(null);
const strokes = ref<Stroke[]>([]);
const redoStrokes = ref<Stroke[]>([]);
const referenceSize = ref<Size | null>(null);
const hasSignature = computed(() => strokes.value.length > 0);
const canUndo = computed(() => strokes.value.length > 0);
const canRedo = computed(() => redoStrokes.value.length > 0);
const canRestart = computed(() => canUndo.value || canRedo.value);

let activePointerId: number | null = null;
let context: CanvasRenderingContext2D | null = null;
let resizeObserver: ResizeObserver | null = null;
let currentView = { scale: 1, offsetX: 0, offsetY: 0 };

function clamp(value: number, minimum: number, maximum: number): number {
    return Math.max(minimum, Math.min(maximum, value));
}

function currentSize(): Size {
    const rect = canvasElement.value?.getBoundingClientRect();

    return {
        width: rect?.width ?? 0,
        height: rect?.height ?? 0,
    };
}

function calculateViewTransform(size: Size): {
    scale: number;
    offsetX: number;
    offsetY: number;
} {
    const reference = referenceSize.value;

    if (!reference) {
        return { scale: 1, offsetX: 0, offsetY: 0 };
    }

    if (!strokes.value.length) {
        const scale = Math.min(
            size.width / Math.max(reference.width, 1),
            size.height / Math.max(reference.height, 1),
        );

        return {
            scale,
            offsetX: (size.width - reference.width * scale) / 2,
            offsetY: (size.height - reference.height * scale) / 2,
        };
    }

    const bounds = signatureBounds();
    const contentHeight = Math.max(bounds.maxY - bounds.minY, 1);
    const horizontalScale = size.width / Math.max(reference.width, 1);
    const verticalScale = Math.max(size.height - 24, 1) / contentHeight;
    const scale = Math.min(horizontalScale, verticalScale);

    return {
        scale,
        offsetX: (size.width - reference.width * scale) / 2,
        offsetY:
            (size.height - contentHeight * scale) / 2 - bounds.minY * scale,
    };
}

function pointFromEvent(event: PointerEvent): Point {
    const rect = canvasElement.value?.getBoundingClientRect();
    const reference = referenceSize.value;

    if (!rect || !reference) {
        return { x: 0, y: 0 };
    }

    return {
        x: clamp(
            (event.clientX - rect.left - currentView.offsetX) /
                currentView.scale,
            0,
            reference.width,
        ),
        y: clamp(
            (event.clientY - rect.top - currentView.offsetY) /
                currentView.scale,
            0,
            reference.height,
        ),
    };
}

function transformedPoints(
    stroke: Stroke,
    scale: number,
    offsetX: number,
    offsetY: number,
): Point[] {
    return stroke.map((point) => ({
        x: point.x * scale + offsetX,
        y: point.y * scale + offsetY,
    }));
}

function drawStroke(
    targetContext: CanvasRenderingContext2D,
    points: Point[],
    strokeWidth: number,
): void {
    if (!points.length) {
        return;
    }

    targetContext.strokeStyle = '#111827';
    targetContext.fillStyle = '#111827';
    targetContext.lineWidth = strokeWidth;
    targetContext.lineCap = 'round';
    targetContext.lineJoin = 'round';

    if (points.length === 1) {
        targetContext.beginPath();
        targetContext.arc(
            points[0].x,
            points[0].y,
            strokeWidth / 2,
            0,
            Math.PI * 2,
        );
        targetContext.fill();

        return;
    }

    targetContext.beginPath();
    targetContext.moveTo(points[0].x, points[0].y);

    for (let index = 1; index < points.length - 1; index += 1) {
        const current = points[index];
        const next = points[index + 1];

        targetContext.quadraticCurveTo(
            current.x,
            current.y,
            (current.x + next.x) / 2,
            (current.y + next.y) / 2,
        );
    }

    const last = points[points.length - 1];

    targetContext.lineTo(last.x, last.y);
    targetContext.stroke();
}

function redraw(): void {
    if (!context) {
        return;
    }

    const size = currentSize();

    context.clearRect(0, 0, size.width, size.height);

    if (!referenceSize.value) {
        return;
    }

    for (const stroke of strokes.value) {
        drawStroke(
            context,
            transformedPoints(
                stroke,
                currentView.scale,
                currentView.offsetX,
                currentView.offsetY,
            ),
            Math.max(1.5, 2.7 * currentView.scale),
        );
    }
}

function resizeCanvas(): void {
    const canvas = canvasElement.value;

    if (!canvas || !context) {
        return;
    }

    const size = currentSize();

    if (size.width < 1 || size.height < 1) {
        return;
    }

    const ratio = Math.max(window.devicePixelRatio || 1, 1);
    const nextWidth = Math.round(size.width * ratio);
    const nextHeight = Math.round(size.height * ratio);

    if (canvas.width !== nextWidth || canvas.height !== nextHeight) {
        canvas.width = nextWidth;
        canvas.height = nextHeight;
        context.setTransform(ratio, 0, 0, ratio, 0, 0);
    }

    currentView = calculateViewTransform(size);
    redraw();
}

function startDrawing(event: PointerEvent): void {
    const canvas = canvasElement.value;

    if (!canvas || activePointerId !== null) {
        return;
    }

    event.preventDefault();

    if (!referenceSize.value) {
        referenceSize.value = currentSize();
        currentView = { scale: 1, offsetX: 0, offsetY: 0 };
    }

    activePointerId = event.pointerId;
    canvas.setPointerCapture(event.pointerId);
    redoStrokes.value = [];
    strokes.value.push([pointFromEvent(event)]);
    redraw();
}

function continueDrawing(event: PointerEvent): void {
    if (event.pointerId !== activePointerId) {
        return;
    }

    event.preventDefault();

    const stroke = strokes.value.at(-1);
    const coalescedEvents = event.getCoalescedEvents?.() ?? [];
    const events = coalescedEvents.length ? coalescedEvents : [event];

    for (const coalescedEvent of events) {
        stroke?.push(pointFromEvent(coalescedEvent));
    }

    redraw();
}

function stopDrawing(event: PointerEvent): void {
    if (event.pointerId === activePointerId) {
        activePointerId = null;
    }
}

function restart(): void {
    strokes.value = [];
    redoStrokes.value = [];
    referenceSize.value = null;
    activePointerId = null;
    currentView = { scale: 1, offsetX: 0, offsetY: 0 };
    redraw();
}

function undo(): void {
    const stroke = strokes.value.pop();

    if (stroke) {
        redoStrokes.value.push(stroke);
        redraw();
    }
}

function redo(): void {
    const stroke = redoStrokes.value.pop();

    if (stroke) {
        strokes.value.push(stroke);
        redraw();
    }
}

function signatureBounds(): Bounds {
    const points = strokes.value.flat();
    const xs = points.map((point) => point.x);
    const ys = points.map((point) => point.y);

    return {
        minX: Math.min(...xs),
        minY: Math.min(...ys),
        maxX: Math.max(...xs),
        maxY: Math.max(...ys),
    };
}

function outputSize(): Size {
    return {
        width: clamp(
            Number.isFinite(props.outputWidth)
                ? Math.round(props.outputWidth)
                : 1024,
            64,
            4096,
        ),
        height: clamp(
            Number.isFinite(props.outputHeight)
                ? Math.round(props.outputHeight)
                : 384,
            64,
            4096,
        ),
    };
}

function outputLayout(size: Size): StrokeLayout {
    const bounds = signatureBounds();
    const padding = Math.max(
        8,
        Math.round(Math.min(size.width, size.height) * 0.08),
    );
    const contentWidth = Math.max(bounds.maxX - bounds.minX, 1);
    const contentHeight = Math.max(bounds.maxY - bounds.minY, 1);
    const scale = Math.min(
        Math.max(size.width - padding * 2, 1) / contentWidth,
        Math.max(size.height - padding * 2, 1) / contentHeight,
    );
    const offsetX =
        (size.width - contentWidth * scale) / 2 - bounds.minX * scale;
    const offsetY =
        (size.height - contentHeight * scale) / 2 - bounds.minY * scale;

    return {
        points: strokes.value.map((stroke) =>
            transformedPoints(stroke, scale, offsetX, offsetY),
        ),
        strokeWidth: clamp(2.7 * scale, 2, 12),
    };
}

function formatNumber(value: number): string {
    return Number(value.toFixed(2)).toString();
}

function svgPath(points: Point[]): string {
    const first = points[0];
    let path = `M ${formatNumber(first.x)} ${formatNumber(first.y)}`;

    for (let index = 1; index < points.length - 1; index += 1) {
        const current = points[index];
        const next = points[index + 1];

        path += ` Q ${formatNumber(current.x)} ${formatNumber(current.y)} ${formatNumber((current.x + next.x) / 2)} ${formatNumber((current.y + next.y) / 2)}`;
    }

    const last = points[points.length - 1];

    return `${path} L ${formatNumber(last.x)} ${formatNumber(last.y)}`;
}

function createSvg(size: Size, layout: StrokeLayout): Blob {
    const elements = layout.points
        .map((points) => {
            if (points.length === 1) {
                return `<circle cx="${formatNumber(points[0].x)}" cy="${formatNumber(points[0].y)}" r="${formatNumber(layout.strokeWidth / 2)}" fill="#111827"/>`;
            }

            return `<path d="${svgPath(points)}" fill="none" stroke="#111827" stroke-width="${formatNumber(layout.strokeWidth)}" stroke-linecap="round" stroke-linejoin="round"/>`;
        })
        .join('');
    const svg = `<svg xmlns="http://www.w3.org/2000/svg" width="${size.width}" height="${size.height}" viewBox="0 0 ${size.width} ${size.height}">${elements}</svg>`;

    return new Blob([svg], { type: 'image/svg+xml' });
}

function emitResult(
    blob: Blob,
    format: SignatureOutputFormat,
    size: Size,
): void {
    emit('confirmed', {
        blob,
        format,
        mimeType: format === 'svg' ? 'image/svg+xml' : 'image/png',
        width: size.width,
        height: size.height,
    });
}

function createPng(size: Size, layout: StrokeLayout): void {
    const outputCanvas = document.createElement('canvas');
    const outputContext = outputCanvas.getContext('2d');

    if (!outputContext) {
        return;
    }

    outputCanvas.width = size.width;
    outputCanvas.height = size.height;

    for (const points of layout.points) {
        drawStroke(outputContext, points, layout.strokeWidth);
    }

    outputCanvas.toBlob((blob) => {
        if (blob) {
            emitResult(blob, 'png', size);
        }
    }, 'image/png');
}

function confirmSignature(): void {
    if (!hasSignature.value) {
        return;
    }

    const size = outputSize();
    const layout = outputLayout(size);

    if (props.outputFormat === 'svg') {
        emitResult(createSvg(size, layout), 'svg', size);

        return;
    }

    createPng(size, layout);
}

onMounted(() => {
    const canvas = canvasElement.value;

    if (!canvas) {
        return;
    }

    context = canvas.getContext('2d');

    if (!context) {
        return;
    }

    canvas.addEventListener('pointerdown', startDrawing);
    canvas.addEventListener('pointermove', continueDrawing);
    canvas.addEventListener('pointerup', stopDrawing);
    canvas.addEventListener('pointercancel', stopDrawing);
    canvas.addEventListener('lostpointercapture', stopDrawing);

    resizeObserver = new ResizeObserver(resizeCanvas);
    resizeObserver.observe(canvas.parentElement ?? canvas);
    resizeCanvas();
});

onBeforeUnmount(() => {
    const canvas = canvasElement.value;

    canvas?.removeEventListener('pointerdown', startDrawing);
    canvas?.removeEventListener('pointermove', continueDrawing);
    canvas?.removeEventListener('pointerup', stopDrawing);
    canvas?.removeEventListener('pointercancel', stopDrawing);
    canvas?.removeEventListener('lostpointercapture', stopDrawing);
    resizeObserver?.disconnect();
});

defineExpose({
    confirm: confirmSignature,
    redo,
    restart,
    undo,
});
</script>

<template>
    <TooltipProvider :delay-duration="300">
        <div class="flex h-full min-h-0 flex-col bg-background">
            <div class="relative min-h-0 flex-1 overflow-hidden bg-background">
                <canvas
                    ref="canvasElement"
                    class="block size-full cursor-crosshair touch-none select-none"
                    tabindex="0"
                    aria-label="Signature drawing area"
                />

                <div
                    class="pointer-events-none absolute inset-x-6 bottom-5 text-xs text-muted-foreground"
                >
                    <div
                        class="border-t border-dashed border-foreground/35 pt-1.5"
                    >
                        {{ signatureLabel }}
                    </div>
                </div>
            </div>

            <div
                class="flex shrink-0 items-center gap-2 border-t bg-background p-3 pb-[max(0.75rem,env(safe-area-inset-bottom))]"
            >
                <div class="flex shrink-0 gap-2">
                    <AlertDialog>
                        <Tooltip>
                            <TooltipTrigger as-child>
                                <AlertDialogTrigger as-child>
                                    <Button
                                        type="button"
                                        variant="outline"
                                        size="icon"
                                        class="size-11"
                                        :disabled="!canRestart"
                                        aria-label="Clear"
                                    >
                                        <RotateCcw />
                                    </Button>
                                </AlertDialogTrigger>
                            </TooltipTrigger>
                            <TooltipContent>Clear</TooltipContent>
                        </Tooltip>
                        <AlertDialogContent>
                            <AlertDialogHeader>
                                <AlertDialogTitle>
                                    Clear signature?
                                </AlertDialogTitle>
                                <AlertDialogDescription>
                                    The current signature and its edit history
                                    will be removed.
                                </AlertDialogDescription>
                            </AlertDialogHeader>
                            <AlertDialogFooter>
                                <AlertDialogCancel>Cancel</AlertDialogCancel>
                                <AlertDialogAction
                                    class="bg-destructive text-white hover:bg-destructive/90"
                                    @click="restart"
                                >
                                    Clear
                                </AlertDialogAction>
                            </AlertDialogFooter>
                        </AlertDialogContent>
                    </AlertDialog>
                    <Tooltip>
                        <TooltipTrigger as-child>
                            <Button
                                type="button"
                                variant="outline"
                                size="icon"
                                class="size-11"
                                :disabled="!canUndo"
                                aria-label="Undo"
                                @click="undo"
                            >
                                <Undo2 />
                            </Button>
                        </TooltipTrigger>
                        <TooltipContent>Undo</TooltipContent>
                    </Tooltip>
                    <Tooltip>
                        <TooltipTrigger as-child>
                            <Button
                                type="button"
                                variant="outline"
                                size="icon"
                                class="size-11"
                                :disabled="!canRedo"
                                aria-label="Redo"
                                @click="redo"
                            >
                                <Redo2 />
                            </Button>
                        </TooltipTrigger>
                        <TooltipContent>Redo</TooltipContent>
                    </Tooltip>
                </div>
                <AlertDialog>
                    <AlertDialogTrigger as-child>
                        <Button
                            type="button"
                            variant="default"
                            class="h-11 flex-1"
                            :disabled="!hasSignature"
                        >
                            <Check />
                            Confirm
                        </Button>
                    </AlertDialogTrigger>
                    <AlertDialogContent>
                        <AlertDialogHeader>
                            <AlertDialogTitle
                                >Confirm signature?</AlertDialogTitle
                            >
                            <AlertDialogDescription>
                                The signature will be submitted as a transparent
                                {{ outputFormat.toUpperCase() }} file.
                            </AlertDialogDescription>
                        </AlertDialogHeader>
                        <AlertDialogFooter>
                            <AlertDialogCancel>Cancel</AlertDialogCancel>
                            <AlertDialogAction @click="confirmSignature">
                                <Check />
                                Confirm
                            </AlertDialogAction>
                        </AlertDialogFooter>
                    </AlertDialogContent>
                </AlertDialog>
            </div>
        </div>
    </TooltipProvider>
</template>
