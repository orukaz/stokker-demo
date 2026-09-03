<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReorderSavedFiltersRequest;
use App\Http\Requests\StoreSavedFilterRequest;
use App\Http\Requests\UpdateSavedFilterRequest;
use App\Models\SavedFilter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class SavedFilterController extends Controller
{
    public function index(): InertiaResponse
    {
        return Inertia::render('demos/SavedFilters', [
            'savedFilters' => SavedFilter::query()
                ->where('view', 'orders')
                ->orderBy('position')
                ->orderBy('id')
                ->get()
                ->map($this->serialize(...)),
        ]);
    }

    public function store(StoreSavedFilterRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $lastPosition = SavedFilter::query()
            ->where('view', $validated['view'])
            ->max('position');
        $validated['position'] = $lastPosition === null ? 0 : $lastPosition + 1;

        $savedFilter = SavedFilter::query()->create($validated);

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

    public function reorder(ReorderSavedFiltersRequest $request): Response
    {
        $ids = $request->validated()['ids'];

        DB::transaction(function () use ($ids): void {
            foreach ($ids as $position => $id) {
                SavedFilter::query()->whereKey($id)->update(['position' => $position]);
            }
        });

        return response()->noContent();
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
     *     position: int,
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
            'position' => $savedFilter->position,
            'updatedAt' => $savedFilter->updated_at->toIso8601String(),
        ];
    }
}
