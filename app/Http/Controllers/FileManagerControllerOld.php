<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Folder;
use App\Models\File;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class FileManagerController extends Controller
{
    public function createFolder(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:folders,name',
        ]);

        echo "ss";
        exit;

        $folder = Folder::create([
            'name' => $request->name,
            'user_id' => Auth::id(),
        ]);

        return response()->json($folder, 201);
    }

    public function createSubfolder(Request $request, Folder $folder)
    {
        $request->validate([
            'name' => 'required|string|unique:folders,name',
        ]);

        $subfolder = Folder::create([
            'name' => $request->name,
            'user_id' => Auth::id(),
            'parent_id' => $folder->id,
        ]);

        return response()->json($subfolder, 201);
    }

    public function uploadFile(Request $request, Folder $folder)
    {
        $request->validate([
            'file' => 'required|file',
        ]);

        $path = $request->file('file')->store('files');

        $file = File::create([
            'name' => $request->file('file')->getClientOriginalName(),
            'path' => $path,
            'folder_id' => $folder->id,
            'user_id' => Auth::id(),
        ]);

        return response()->json($file, 201);
    }

    public function listFiles(Folder $folder)
    {
        $files = $folder->files()->paginate(10);
        return response()->json($files);
    }

    public function updateFile(Request $request, File $file)
    {
        $request->validate([
            'name' => 'sometimes|string',
        ]);

        if ($request->has('name')) {
            $file->name = $request->name;
        }

        $file->save();
        return response()->json($file);
    }

    public function deleteFile(File $file)
    {
        Storage::delete($file->path);
        $file->delete();
        return response()->json(null, 204);
    }

    public function search(Request $request)
    {
        $query = $request->input('query');
        $results = File::where('name', 'LIKE', "%{$query}%")
            ->orWhereHas('folder', function($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%");
            })
            ->paginate(10);

        return response()->json($results);
    }

    public function share(Request $request)
    {
        // Implement sharing logic here
    }
}
