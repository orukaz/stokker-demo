<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Support\ProductFavoriteOwner;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Pagination\CursorPaginator;
use Inertia\Inertia;
use Inertia\Response;

class ProductController extends Controller
{
    public function index(Request $request): Response
    {
        $perPage = $request->query('cursor') ? 10 : 20;
        $owner = ProductFavoriteOwner::attributes($request);

        return Inertia::render('products/Index', [
            'products' => Inertia::scroll(fn () => $this->paginatedProducts($owner, $perPage)),
        ]);
    }

    /**
     * @param  array{user_id?: int, visitor_id?: string}  $favoriteOwner
     */
    private function paginatedProducts(array $favoriteOwner, int $perPage): CursorPaginator
    {
        $query = Product::query()
            ->select([
                'id',
                'title',
                'link',
                'image_url',
                'brand',
                'quantity',
                'price',
                'currency',
            ]);

        if ($favoriteOwner !== []) {
            $query->withExists([
                'favorites as is_favorited' => function (Builder $query) use ($favoriteOwner): void {
                    $query->where($favoriteOwner);
                },
            ]);
        }

        return $query
            ->orderBy('id')
            ->cursorPaginate($perPage)
            ->through(fn (Product $product): array => [
                'id' => $product->id,
                'title' => $product->title,
                'link' => $product->link,
                'imageUrl' => $product->image_url,
                'brand' => $product->brand,
                'quantity' => $product->quantity === null ? null : (float) $product->quantity,
                'price' => $product->price === null ? null : (float) $product->price,
                'currency' => $product->currency ?: 'EUR',
                'isFavorited' => (bool) $product->getAttribute('is_favorited'),
            ]);
    }
}
