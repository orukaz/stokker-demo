<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSavedFilterRequest;
use App\Http\Requests\UpdateSavedFilterRequest;
use App\Models\SavedFilter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class SavedFilterController extends Controller
{
    public function index(): InertiaResponse
    {
        return Inertia::render('demos/SavedFilters', [
            'savedFilters' => SavedFilter::query()
                ->where('view', 'orders')
                ->orderByDesc('is_default')
                ->latest('updated_at')
                ->get()
                ->map($this->serialize(...)),
        ]);
    }

    public function store(StoreSavedFilterRequest $request): JsonResponse
    {
        $savedFilter = SavedFilter::query()->create($request->validated());

        if ($savedFilter->is_default) {
            $savedFilter->makeDefault();
        }

        $savedFilter->refresh();

        return response()->json([
            'savedFilter' => $this->serialize($savedFilter),
        ], Response::HTTP_CREATED);
    }

    public function update(UpdateSavedFilterRequest $request, SavedFilter $savedFilter): JsonResponse
    {
        $savedFilter->update($request->validated());
        $savedFilter->refresh();

        return response()->json([
            'savedFilter' => $this->serialize($savedFilter),
        ]);
    }

    public function makeDefault(SavedFilter $savedFilter): JsonResponse
    {
        $savedFilter->makeDefault();
        $savedFilter->refresh();

        return response()->json([
            'savedFilter' => $this->serialize($savedFilter),
        ]);
    }

    public function destroy(SavedFilter $savedFilter): Response
    {
        $savedFilter->delete();

        return response()->noContent();
    }

    /**
     * @return array{
     *     id: int,
     *     view: string,
     *     name: string,
     *     filters: array<string, mixed>,
     *     isDefault: bool,
     *     updatedAt: string
     * }
     */
    private function serialize(SavedFilter $savedFilter): array
    {
        return [
            'id' => $savedFilter->id,
            'view' => $savedFilter->view,
            'name' => $savedFilter->name,
            'filters' => $savedFilter->filters,
            'isDefault' => $savedFilter->is_default,
            'updatedAt' => $savedFilter->updated_at->toIso8601String(),
        ];
    }
}
