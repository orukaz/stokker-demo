<?php

namespace App\Http\Controllers;

use App\Support\SourceCodeRepository;
use Illuminate\Http\JsonResponse;

class SourceCodeController extends Controller
{
    public function show(string $sourceSet, SourceCodeRepository $sourceCodeRepository): JsonResponse
    {
        return response()->json($sourceCodeRepository->find($sourceSet));
    }
}
