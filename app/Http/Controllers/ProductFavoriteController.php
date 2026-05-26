<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductFavorite;
use App\Support\ProductFavoriteOwner;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductFavoriteController extends Controller
{
    public function store(Request $request, Product $product): JsonResponse
    {
        $owner = ProductFavoriteOwner::attributes($request, createVisitor: true);

        ProductFavorite::query()->firstOrCreate([
            ...$owner,
            'product_id' => $product->id,
        ]);

        return $this->withVisitorCookie(
            response()->json([
                'isFavorited' => true,
                'favoritesCount' => ProductFavorite::countForOwner($owner),
            ]),
            $owner,
        );
    }

    public function destroy(Request $request, Product $product): JsonResponse
    {
        $owner = ProductFavoriteOwner::attributes($request);

        if ($owner !== []) {
            ProductFavorite::query()
                ->where($owner)
                ->where('product_id', $product->id)
                ->delete();
        }

        return $this->withVisitorCookie(
            response()->json([
                'isFavorited' => false,
                'favoritesCount' => ProductFavorite::countForOwner($owner),
            ]),
            $owner,
        );
    }

    /**
     * @param  array{user_id?: int, visitor_id?: string}  $owner
     */
    private function withVisitorCookie(JsonResponse $response, array $owner): JsonResponse
    {
        if (! isset($owner['visitor_id'])) {
            return $response;
        }

        return $response->withCookie(ProductFavoriteOwner::visitorCookie($owner['visitor_id']));
    }
}
