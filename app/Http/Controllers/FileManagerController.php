<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Folder;
use App\Models\File;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Exception;
use Illuminate\Support\Facades\Validator;

class FileManagerController extends Controller
{
    public function createFolder(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|unique:folders,name',
            ]);

            if($validator->fails())
            {
                return response()->json([
                    'status'=>400,
                    'errors'=>$validator->messages()
                ]);
            }
    

            $folder = Folder::create([
                'name' => $request->name,
                'user_id' => auth::id(),
            ]);
            // dd($folder);
            return response()->json($folder, 201);
        }
        catch (Exception $e) {
            return response()->json(['errors' => $e->getMessage()], 422);
        }
        
    }

    public function createSubfolder(Request $request, Folder $folder)
    {

        try {

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|unique:folders,name',
            ]);

            if($validator->fails())
            {
                return response()->json([
                    'status'=>400,
                    'errors'=>$validator->messages()
                ]);
            }
    

            $subfolder = Folder::create([
                'name' => $request->name,
                'user_id' => Auth::id(),
                'parent_id' => $folder->id,
            ]);
    
            return response()->json($subfolder, 201);
        }
        catch (Exception $e) {
            return response()->json(['errors' => $e->getMessage()], 422);
        }
        
    }

    public function uploadFile(Request $request, Folder $folder)
    {
        try {

            $validator = Validator::make($request->all(), [
                'file' => 'required|file',
            ]);

            if($validator->fails())
            {
                return response()->json([
                    'status'=>400,
                    'errors'=>$validator->messages()
                ]);
            }
    

            $path = $request->file('file')->store('files');

            $file = File::create([
                'name' => $request->file('file')->getClientOriginalName(),
                'path' => $path,
                'folder_id' => $folder->id,
                'user_id' => Auth::id(),
            ]);

            return response()->json($file, 201);
        }
        catch (Exception $e) {
            return response()->json(['errors' => $e->getMessage()], 422);
        }

    }

    public function listFiles(Folder $folder)
    {
        try{
            $files = File::all();
            return response()->json($files);
        }
        catch (Exception $e) {
            return response()->json(['errors' => $e->getMessage()], 422);
        }
    }

    public function updateFile(Request $request, File $file)
    {
        try {

            $validator = Validator::make($request->all(), [
                'name' => 'sometimes|string',
            ]);

            if($validator->fails())
            {
                return response()->json([
                    'status'=>400,
                    'errors'=>$validator->messages()
                ]);
            }
    

            if ($request->has('name')) {
                $file->name = $request->name;
            }

            $file->save();
            return response()->json($file);
        }
        catch (Exception $e) {
            return response()->json(['errors' => $e->getMessage()], 422);
        }

    }

    public function deleteFile(File $file)
    {
        Storage::delete($file->path);
        $file->delete();
        return response()->json(null, 204);
    }

    public function search(Request $request)
    {
        try{
            $results = File::where('name', 'LIKE', "%{$request->name}%")
                ->paginate(10);

            return response()->json($results);
        }
        catch (Exception $e) {
            return response()->json(['errors' => $e->getMessage()], 422);
        }

    }

    public function share(Request $request)
    {
        // Implement sharing logic here
    }
}
