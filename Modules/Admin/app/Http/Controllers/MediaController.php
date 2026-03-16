<?php

namespace Modules\Admin\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Modules\Admin\Models\MediaFile;

class MediaController extends Controller
{
    protected function uploadsRoot(): string
    {
        $root = public_path('uploads');
        File::ensureDirectoryExists($root);

        return $root;
    }

    protected function sanitizeRelativePath(?string $path): string
    {
        $path = str_replace('\\', '/', (string) $path);
        $path = trim($path, '/');

        $segments = collect(explode('/', $path))
            ->filter(fn ($segment) => $segment !== '' && $segment !== '.' && $segment !== '..')
            ->values()
            ->all();

        $relative = implode('/', $segments);
        $fullPath = $relative ? $this->uploadsRoot() . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $relative) : $this->uploadsRoot();

        if (! str_starts_with(realpath(dirname($fullPath)) ?: dirname($fullPath), $this->uploadsRoot())) {
            return '';
        }

        return $relative;
    }

    protected function syncMediaRecord(string $relativePath): ?MediaFile
    {
        $relativePath = $this->sanitizeRelativePath($relativePath);
        if ($relativePath === '') {
            return null;
        }

        $fullPath = $this->uploadsRoot() . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $relativePath);
        if (! File::exists($fullPath) || ! File::isFile($fullPath)) {
            return null;
        }

        return MediaFile::firstOrCreate(
            ['file_path' => $relativePath],
            [
                'file_name' => basename($relativePath),
                'file_extension' => Str::lower(pathinfo($relativePath, PATHINFO_EXTENSION)),
                'file_type' => File::mimeType($fullPath),
                'file_size' => File::size($fullPath),
                'folder_path' => trim(str_replace('\\', '/', dirname($relativePath)), '.'),
            ]
        );
    }

    protected function directoryData(string $relativePath = '', string $search = ''): array
    {
        $relativePath = $this->sanitizeRelativePath($relativePath);
        $currentPath = $relativePath
            ? $this->uploadsRoot() . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $relativePath)
            : $this->uploadsRoot();

        if (! File::exists($currentPath) || ! File::isDirectory($currentPath)) {
            $relativePath = '';
            $currentPath = $this->uploadsRoot();
        }

        $search = trim($search);

        $folders = collect(File::directories($currentPath))
            ->map(function ($directory) use ($relativePath) {
                $name = basename($directory);
                $path = trim(($relativePath ? $relativePath . '/' : '') . $name, '/');

                return [
                    'name' => $name,
                    'path' => $path,
                ];
            })
            ->filter(fn ($folder) => $search === '' || Str::contains(Str::lower($folder['name']), Str::lower($search)))
            ->sortBy('name', SORT_NATURAL | SORT_FLAG_CASE)
            ->values()
            ->all();

        $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp', 'gif', 'svg'];

        $files = collect(File::files($currentPath))
            ->filter(fn ($file) => in_array(Str::lower($file->getExtension()), $allowedExtensions, true))
            ->map(function ($file) use ($relativePath) {
                $name = $file->getFilename();
                $path = trim(($relativePath ? $relativePath . '/' : '') . $name, '/');
                $media = $this->syncMediaRecord($path);

                return [
                    'id' => $media?->id,
                    'name' => pathinfo($name, PATHINFO_FILENAME),
                    'file_name' => $name,
                    'path' => $path,
                    'url' => asset('uploads/' . $path),
                    'size' => $file->getSize(),
                    'created_at' => date('Y-m-d H:i', $file->getCTime()),
                    'extension' => Str::lower($file->getExtension()),
                ];
            })
            ->filter(fn ($file) => $search === '' || Str::contains(Str::lower($file['name']), Str::lower($search)) || Str::contains(Str::lower($file['file_name']), Str::lower($search)))
            ->sortBy('file_name', SORT_NATURAL | SORT_FLAG_CASE)
            ->values()
            ->all();

        $breadcrumbs = [];
        $segments = $relativePath ? explode('/', $relativePath) : [];
        $built = '';

        foreach ($segments as $segment) {
            $built = ltrim($built . '/' . $segment, '/');
            $breadcrumbs[] = [
                'name' => $segment,
                'path' => $built,
            ];
        }

        return [
            'current_path' => $relativePath,
            'breadcrumbs' => $breadcrumbs,
            'folders' => $folders,
            'files' => $files,
        ];
    }

    public function index(): View
    {
        return view('admin::media.index');
    }

    public function browser(Request $request): JsonResponse
    {
        return response()->json($this->directoryData(
            $request->string('path')->toString(),
            $request->string('search')->toString()
        ));
    }

    public function upload(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'path' => ['nullable', 'string', 'max:255'],
            'files' => ['required', 'array'],
            'files.*' => ['required', 'image', 'mimes:jpg,jpeg,png,webp,gif,svg', 'max:4096'],
        ]);

        $relativePath = $this->sanitizeRelativePath($validated['path'] ?? '');
        $targetDirectory = $relativePath
            ? $this->uploadsRoot() . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $relativePath)
            : $this->uploadsRoot();

        File::ensureDirectoryExists($targetDirectory);

        foreach ($request->file('files', []) as $file) {
            $baseName = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) ?: 'image';
            $fileName = $baseName . '-' . time() . '-' . Str::random(5) . '.' . $file->getClientOriginalExtension();
            $file->move($targetDirectory, $fileName);
            $storedPath = trim(($relativePath ? $relativePath . '/' : '') . $fileName, '/');
            $this->syncMediaRecord($storedPath);
        }

        return response()->json([
            'message' => 'Files uploaded successfully.',
            'data' => $this->directoryData($relativePath),
        ]);
    }

    public function folder(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'path' => ['nullable', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:100'],
        ]);

        $relativePath = $this->sanitizeRelativePath($validated['path'] ?? '');
        $folderName = Str::slug($validated['name']);

        if ($folderName === '') {
            return response()->json(['message' => 'Invalid folder name.'], 422);
        }

        $targetDirectory = $relativePath
            ? $this->uploadsRoot() . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $relativePath)
            : $this->uploadsRoot();

        File::ensureDirectoryExists($targetDirectory . DIRECTORY_SEPARATOR . $folderName);

        return response()->json([
            'message' => 'Folder created successfully.',
            'data' => $this->directoryData($relativePath),
        ]);
    }
}
