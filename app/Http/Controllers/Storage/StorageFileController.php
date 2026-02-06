<?php

namespace App\Http\Controllers\Storage;

use App\Http\Controllers\Controller;
use App\Models\Storage\StorageFile;
use App\Models\Storage\StorageFolder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage as StorageFacade;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class StorageFileController extends Controller
{
    public function store(Request $request, StorageFolder $folder): RedirectResponse
    {
        $this->authorize('upload', $folder);

        $data = $request->validate([
            'files' => ['required', 'array', 'min:1'],
            'files.*' => ['file', 'max:51200'], // 50 MB per file
        ]);

        $disk = 'local';
        $baseDir = "storage-files/{$folder->id}";

        if (!StorageFacade::disk($disk)->exists($baseDir)) {
            StorageFacade::disk($disk)->makeDirectory($baseDir);
        }

        foreach ($data['files'] as $uploadedFile) {
            $uuid = (string) Str::uuid();
            $ext = $uploadedFile->getClientOriginalExtension();
            $storedName = $uuid . ($ext ? "." . $ext : "");

            $path = StorageFacade::disk($disk)->putFileAs($baseDir, $uploadedFile, $storedName);

            StorageFile::create([
                'storage_folder_id' => $folder->id,
                'original_name' => $uploadedFile->getClientOriginalName(),
                'stored_name' => $storedName,
                'disk' => $disk,
                'path' => $path,
                'mime_type' => $uploadedFile->getClientMimeType(),
                'size' => (int) $uploadedFile->getSize(),
                'checksum' => hash_file('sha256', $uploadedFile->getRealPath()),
                'uploaded_by' => $request->user()?->id,
                'is_active' => true,
            ]);
        }

        return back()->with('success', 'File(s) uploaded.');
    }

    public function download(Request $request, StorageFile $file): StreamedResponse
    {
        $this->authorize('download', $file);

        return StorageFacade::disk($file->disk)->download($file->path, $file->download_name);
    }

    public function update(Request $request, StorageFile $file): RedirectResponse
    {
        $this->authorize('update', $file);

        $data = $request->validate([
            'original_name' => ['required', 'string', 'max:255'],
            'replace_file' => ['nullable', 'file', 'max:51200'],
        ]);

        $file->original_name = $data['original_name'];

        // Optional replace content
        if ($request->hasFile('replace_file')) {
            $uploaded = $request->file('replace_file');

            $disk = $file->disk ?: 'local';
            $baseDir = "storage-files/{$file->storage_folder_id}";

            $uuid = (string) Str::uuid();
            $ext = $uploaded->getClientOriginalExtension();
            $storedName = $uuid . ($ext ? "." . $ext : "");

            $newPath = StorageFacade::disk($disk)->putFileAs($baseDir, $uploaded, $storedName);

            // delete old
            try {
                StorageFacade::disk($disk)->delete($file->path);
            } catch (\Throwable $e) {}

            $file->stored_name = $storedName;
            $file->path = $newPath;
            $file->mime_type = $uploaded->getClientMimeType();
            $file->size = (int) $uploaded->getSize();
            $file->checksum = hash_file('sha256', $uploaded->getRealPath());
        }

        $file->save();

        return back()->with('success', 'File updated.');
    }

    public function destroy(Request $request, StorageFile $file): RedirectResponse
    {
        $this->authorize('delete', $file);

        $folderId = $file->storage_folder_id;

        try {
            StorageFacade::disk($file->disk)->delete($file->path);
        } catch (\Throwable $e) {}

        $file->delete();

        return redirect()->route('storage.folders.show', $folderId)->with('success', 'File deleted.');
    }
}
