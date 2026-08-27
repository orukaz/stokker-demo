<?php

namespace App\Support;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class SourceCodeRepository
{
    /**
     * @return array{
     *     sourceSet: string,
     *     title: string,
     *     files: list<array{id: string, label: string, language: string, code: string}>
     * }
     */
    public function find(string $sourceSet): array
    {
        $sourceSets = config('source_code.source_sets', []);
        $configuredSourceSet = is_array($sourceSets)
            ? ($sourceSets[$sourceSet] ?? null)
            : null;

        if (! is_array($configuredSourceSet)) {
            throw new NotFoundHttpException;
        }

        $title = $configuredSourceSet['title'] ?? null;
        $configuredFiles = $configuredSourceSet['files'] ?? null;

        if (! is_string($title) || ! is_array($configuredFiles)) {
            throw new NotFoundHttpException;
        }

        $files = [];

        foreach ($configuredFiles as $id => $configuredFile) {
            if (! is_string($id) || ! is_array($configuredFile)) {
                throw new NotFoundHttpException;
            }

            $files[] = $this->readConfiguredFile($id, $configuredFile);
        }

        return [
            'sourceSet' => $sourceSet,
            'title' => $title,
            'files' => $files,
        ];
    }

    /**
     * @param  array<string, mixed>  $configuredFile
     * @return array{id: string, label: string, language: string, code: string}
     */
    private function readConfiguredFile(string $id, array $configuredFile): array
    {
        $label = $configuredFile['label'] ?? null;
        $language = $configuredFile['language'] ?? null;
        $relativePath = $configuredFile['path'] ?? null;

        if (! is_string($label) || ! is_string($language) || ! is_string($relativePath)) {
            throw new NotFoundHttpException;
        }

        $absolutePath = realpath(base_path($relativePath));

        if (! is_string($absolutePath)
            || ! File::isFile($absolutePath)
            || ! $this->isWithinAllowedRoot($absolutePath)
            || ! $this->hasAllowedExtension($absolutePath)
            || ! $this->hasAllowedLanguage($language)
            || File::size($absolutePath) > config('source_code.max_file_size', 512 * 1024)
        ) {
            throw new NotFoundHttpException;
        }

        return [
            'id' => $id,
            'label' => $label,
            'language' => $language,
            'code' => File::get($absolutePath),
        ];
    }

    private function isWithinAllowedRoot(string $absolutePath): bool
    {
        $allowedRoots = config('source_code.allowed_roots', []);

        if (! is_array($allowedRoots)) {
            return false;
        }

        foreach ($allowedRoots as $allowedRoot) {
            if (! is_string($allowedRoot)) {
                continue;
            }

            $absoluteRoot = realpath(base_path($allowedRoot));

            if (is_string($absoluteRoot)
                && Str::startsWith($absolutePath, $absoluteRoot.DIRECTORY_SEPARATOR)
            ) {
                return true;
            }
        }

        return false;
    }

    private function hasAllowedExtension(string $absolutePath): bool
    {
        $allowedExtensions = config('source_code.allowed_extensions', []);
        $extension = Str::lower(pathinfo($absolutePath, PATHINFO_EXTENSION));

        return is_array($allowedExtensions)
            && in_array($extension, $allowedExtensions, true);
    }

    private function hasAllowedLanguage(string $language): bool
    {
        $allowedLanguages = config('source_code.allowed_languages', []);

        return is_array($allowedLanguages)
            && in_array($language, $allowedLanguages, true);
    }
}
