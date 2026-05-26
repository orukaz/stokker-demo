<script lang="ts">
    import ProductFavoriteToggle from '@/components/site/ProductFavoriteToggle.svelte';
    import { cn } from '@/lib/utils';
    import type { ProductSummary } from '@/types';

    let {
        product,
    }: {
        product: ProductSummary;
    } = $props();

    const price = $derived(
        product.price === null
            ? 'Küsi pakkumist'
            : new Intl.NumberFormat('et-EE', {
                  style: 'currency',
                  currency: product.currency || 'EUR',
              }).format(product.price),
    );

    const quantity = $derived(product.quantity ?? 0);
    const isAvailable = $derived(quantity > 0);
    const availabilityLabel = $derived.by(() => {
        if (quantity > 20) {
            return 'Saadaval 20+';
        }

        if (quantity > 0) {
            return `Saadaval ${Math.floor(quantity)}`;
        }

        return 'Saadaval tellimisel';
    });
</script>

<div class="relative border-b border-gray-300">
    <div class="absolute top-6 -right-px bottom-6 z-2 w-px bg-gray-300"></div>
    <div class="relative h-full min-w-0 overflow-hidden bg-white text-start">
        <ProductFavoriteToggle
            productId={product.id}
            initialFavorited={product.isFavorited}
        />
        <a
            class="flex h-full flex-col justify-between gap-2 rounded-md p-4"
            href={product.link ?? '#'}
            target={product.link ? '_blank' : undefined}
            rel={product.link ? 'external noopener noreferrer' : undefined}
        >
            {#if product.imageUrl}
                <img
                    src={product.imageUrl}
                    alt={product.title}
                    class="aspect-3/2 object-contain"
                    loading="lazy"
                />
            {:else}
                <div
                    class="flex aspect-3/2 w-full items-center justify-center rounded bg-gray-50 text-sm text-gray-400"
                >
                    Pilt puudub
                </div>
            {/if}

            <div class="flex grow flex-col">
                {#if product.brand}
                    <small class="underline">{product.brand}</small>
                {/if}
                <span class="text-lg font-medium">{product.title}</span>
            </div>

            <small>Mootorsaed</small>

            <div class="flex flex-col justify-between">
                <span class="text-base font-medium text-stokker-red"
                    >{price}</span
                >
                <span class="text-xs text-gray-500">&nbsp;</span>
            </div>

            <div class="flex items-center gap-2 text-gray-500">
                <div
                    class={cn(
                        'size-3 rounded-full',
                        isAvailable ? 'bg-stokker-green' : 'bg-stokker-danger',
                    )}
                ></div>
                <span>{availabilityLabel}</span>
            </div>
        </a>
    </div>
</div>
