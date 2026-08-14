<script setup lang="ts">
import { onBeforeUnmount, onMounted, ref } from 'vue';

type Point = {
    x: number;
    y: number;
};

type Stroke = Point[];

const emit = defineEmits<{
    confirmed: [signature: Blob];
}>();

const canvasElement = ref<HTMLCanvasElement | null>(null);
const hasSignature = ref(false);
const strokes: Stroke[] = [];

let activePointerId: number | null = null;
let context: CanvasRenderingContext2D | null = null;
let resizeObserver: ResizeObserver | null = null;

function clamp(value: number): number {
    return Math.max(0, Math.min(1, value));
}

function currentSize(): { width: number; height: number } {
    const rect = canvasElement.value?.getBoundingClientRect();

    return {
        width: rect?.width ?? 0,
        height: rect?.height ?? 0,
    };
}

function pointFromEvent(event: PointerEvent): Point {
    const rect = canvasElement.value?.getBoundingClientRect();

    if (!rect) {
        return { x: 0, y: 0 };
    }

    return {
        x: clamp((event.clientX - rect.left) / rect.width),
        y: clamp((event.clientY - rect.top) / rect.height),
    };
}

function pixelPoints(
    stroke: Stroke,
    width: number,
    height: number,
    offsetX = 0,
    offsetY = 0,
): Point[] {
    return stroke.map((point) => ({
        x: point.x * width + offsetX,
        y: point.y * height + offsetY,
    }));
}

function drawStroke(
    targetContext: CanvasRenderingContext2D,
    points: Point[],
): void {
    if (!points.length) {
        return;
    }

    targetContext.strokeStyle = '#111827';
    targetContext.fillStyle = '#111827';
    targetContext.lineWidth = 2.7;
    targetContext.lineCap = 'round';
    targetContext.lineJoin = 'round';

    if (points.length === 1) {
        targetContext.beginPath();
        targetContext.arc(points[0].x, points[0].y, 1.35, 0, Math.PI * 2);
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

    const { width, height } = currentSize();

    context.clearRect(0, 0, width, height);

    for (const stroke of strokes) {
        drawStroke(context, pixelPoints(stroke, width, height));
    }
}

function resizeCanvas(): void {
    const canvas = canvasElement.value;

    if (!canvas || !context) {
        return;
    }

    const { width, height } = currentSize();

    if (width < 1 || height < 1) {
        return;
    }

    const ratio = Math.max(window.devicePixelRatio || 1, 1);
    const nextWidth = Math.round(width * ratio);
    const nextHeight = Math.round(height * ratio);

    if (canvas.width !== nextWidth || canvas.height !== nextHeight) {
        canvas.width = nextWidth;
        canvas.height = nextHeight;
        context.setTransform(ratio, 0, 0, ratio, 0, 0);
    }

    redraw();
}

function startDrawing(event: PointerEvent): void {
    const canvas = canvasElement.value;

    if (!canvas || activePointerId !== null) {
        return;
    }

    event.preventDefault();
    activePointerId = event.pointerId;
    canvas.setPointerCapture(event.pointerId);
    strokes.push([pointFromEvent(event)]);
    hasSignature.value = true;
    redraw();
}

function continueDrawing(event: PointerEvent): void {
    if (event.pointerId !== activePointerId) {
        return;
    }

    event.preventDefault();

    const stroke = strokes.at(-1);
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

function signatureBounds(width: number, height: number) {
    const points = strokes.flatMap((stroke) =>
        pixelPoints(stroke, width, height),
    );
    const xs = points.map((point) => point.x);
    const ys = points.map((point) => point.y);

    return {
        minX: Math.min(...xs),
        minY: Math.min(...ys),
        maxX: Math.max(...xs),
        maxY: Math.max(...ys),
    };
}

function createTrimmedCanvas(): HTMLCanvasElement | null {
    if (!strokes.length) {
        return null;
    }

    const { width, height } = currentSize();
    const bounds = signatureBounds(width, height);
    const padding = 14;
    const exportWidth = Math.max(
        1,
        Math.ceil(bounds.maxX - bounds.minX + padding * 2),
    );
    const exportHeight = Math.max(
        1,
        Math.ceil(bounds.maxY - bounds.minY + padding * 2),
    );
    const scale = 2;
    const exportCanvas = document.createElement('canvas');
    const exportContext = exportCanvas.getContext('2d');

    if (!exportContext) {
        return null;
    }

    exportCanvas.width = exportWidth * scale;
    exportCanvas.height = exportHeight * scale;
    exportContext.scale(scale, scale);

    for (const stroke of strokes) {
        drawStroke(
            exportContext,
            pixelPoints(
                stroke,
                width,
                height,
                padding - bounds.minX,
                padding - bounds.minY,
            ),
        );
    }

    return exportCanvas;
}

function confirmSignature(): void {
    const exportCanvas = createTrimmedCanvas();

    exportCanvas?.toBlob((blob) => {
        if (blob) {
            emit('confirmed', blob);
        }
    }, 'image/png');
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
</script>

<template>
    <main
        class="fixed inset-0 flex h-dvh w-screen flex-col overflow-hidden bg-white"
    >
        <div class="relative min-h-0 flex-1 bg-white">
            <canvas
                ref="canvasElement"
                class="block size-full cursor-crosshair touch-none bg-white select-none"
                tabindex="0"
                aria-label="Allkirja joonistamise ala"
            />
            <div
                class="pointer-events-none absolute inset-x-6 bottom-8 border-t border-slate-300"
            />
        </div>

        <div class="shrink-0 bg-white pb-[env(safe-area-inset-bottom)]">
            <button
                type="button"
                class="h-16 w-full bg-[#e30613] text-lg font-bold text-white transition-colors disabled:cursor-not-allowed disabled:bg-slate-300 landscape:h-14"
                :disabled="!hasSignature"
                @click="confirmSignature"
            >
                Kinnita
            </button>
        </div>
    </main>
</template>
