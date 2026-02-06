<?php

namespace App\Http\Controllers\Storage;

use App\Http\Controllers\Controller;
use App\Models\Storage\StorageFolder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StorageBrowserController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $folders = StorageFolder::query()
            ->whereNull('parent_id')
            ->viewableBy($user)
            ->orderBy('name')
            ->get();

        return view('storage.index', [
            'folders' => $folders,
        ]);
    }

    public function show(Request $request, StorageFolder $folder): View
    {
        $this->authorize('view', $folder);

        $user = $request->user();

        $subfolders = StorageFolder::query()
            ->where('parent_id', $folder->id)
            ->viewableBy($user)
            ->orderBy('name')
            ->get();

        $files = $folder->files()->orderBy('original_name')->get();

        return view('storage.folders.show', [
            'folder' => $folder,
            'subfolders' => $subfolders,
            'files' => $files,
            'access' => $folder->accessForUser($user->id),
        ]);
    }
}
