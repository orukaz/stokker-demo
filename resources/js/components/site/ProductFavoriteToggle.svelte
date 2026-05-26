<script lang="ts">
    import Heart from 'lucide-svelte/icons/heart';
    import {
        destroy,
        store,
    } from '@/actions/App/Http/Controllers/ProductFavoriteController';
    import { cn } from '@/lib/utils';

    type FavoriteResponse = {
        isFavorited: boolean;
        favoritesCount: number;
    };

    let {
        productId,
        initialFavorited = false,
    }: {
        productId: number;
        initialFavorited?: boolean;
    } = $props();

    let isFavorited = $state(false);
    let isPending = $state(false);

    $effect(() => {
        isFavorited = initialFavorited;
    });

    function emitFavoriteDelta(delta: number): void {
        window.dispatchEvent(
            new CustomEvent('stokker:favorites-count-change', {
                detail: { delta },
            }),
        );
    }

    function emitFavoritesCount(count: number): void {
        window.dispatchEvent(
            new CustomEvent('stokker:favorites-count-change', {
                detail: { count },
            }),
        );
    }

    function csrfToken(): string {
        return (
            document.querySelector<HTMLMetaElement>('meta[name="csrf-token"]')
                ?.content ?? ''
        );
    }

    async function toggleFavorite(event: MouseEvent): Promise<void> {
        event.preventDefault();
        event.stopPropagation();

        if (isPending) {
            return;
        }

        const previousState = isFavorited;
        const nextState = !isFavorited;
        const delta = nextState ? 1 : -1;

        isFavorited = nextState;
        isPending = true;
        emitFavoriteDelta(delta);

        try {
            const route = nextState ? store(productId) : destroy(productId);
            const response = await fetch(route.url, {
                method: route.method.toUpperCase(),
                credentials: 'same-origin',
                headers: {
                    Accept: 'application/json',
                    'X-CSRF-TOKEN': csrfToken(),
                    'X-Requested-With': 'XMLHttpRequest',
                },
            });

            if (!response.ok) {
                throw new Error('Unable to update favorite.');
            }

            const payload = (await response.json()) as FavoriteResponse;

            isFavorited = payload.isFavorited;
            emitFavoritesCount(payload.favoritesCount);
        } catch {
            isFavorited = previousState;
            emitFavoriteDelta(delta * -1);
        } finally {
            isPending = false;
        }
    }
</script>

<button
    type="button"
    class={cn(
        'absolute top-3 right-3 z-2 inline-flex size-9 items-center justify-center rounded-full bg-white text-stokker-primary shadow-sm ring-1 ring-gray-200 transition-colors hover:bg-stokker-light-green disabled:opacity-70',
        isFavorited && 'bg-stokker-light-green text-stokker-green',
    )}
    aria-label={isFavorited ? 'Eemalda lemmikutest' : 'Lisa lemmikutesse'}
    aria-pressed={isFavorited}
    disabled={isPending}
    onclick={toggleFavorite}
>
    <Heart
        class={cn('size-5', isFavorited && 'fill-current')}
        strokeWidth={1.8}
    />
</button>
