<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\CV;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class CVController extends Controller
{
    /**
     * Get the current uploaded CV (latest).
     */
    public function index()
    {
        $cv = CV::latest()->first();

        if (!$cv) {
            return response()->json(['data' => null]);
        }

        return response()->json([
            'data' => [
                'id'        => $cv->id,
                'file_name' => $cv->file_name,
                'file_size' => $cv->file_size,
                'url'       => Storage::url($cv->file_path),
                'created_at'=> $cv->created_at->format('Y-m-d H:i'),
            ]
        ]);
    }

    /**
     * Upload a new CV (PDF only, max 2MB).
     * Deletes any previously stored CV before saving the new one.
     */
    public function upload(Request $request)
    {
        $request->validate([
            'cv' => 'required|file|mimes:pdf|max:2048',
        ], [
            'cv.required' => 'Please select a PDF file.',
            'cv.mimes'    => 'Only PDF files are allowed.',
            'cv.max'      => 'File size must not exceed 2MB.',
        ]);

        // Remove any existing CV file
        $existing = CV::latest()->first();
        if ($existing) {
            Storage::disk('public')->delete($existing->file_path);
            $existing->delete();
        }

        $file     = $request->file('cv');
        $path     = $file->store('cv', 'public');
        $fileName = $file->getClientOriginalName();
        $fileSize = $file->getSize();

        $cv = CV::create([
            'file_name' => $fileName,
            'file_path' => $path,
            'file_size' => $fileSize,
        ]);

        return response()->json([
            'message' => 'CV uploaded successfully.',
            'data'    => [
                'id'        => $cv->id,
                'file_name' => $cv->file_name,
                'file_size' => $cv->file_size,
                'url'       => Storage::url($cv->file_path),
                'created_at'=> $cv->created_at->format('Y-m-d H:i'),
            ]
        ], Response::HTTP_CREATED);
    }

    /**
     * Delete the CV by ID.
     */
    public function destroy(string $id)
    {
        $cv = CV::findOrFail($id);

        Storage::disk('public')->delete($cv->file_path);
        $cv->delete();

        return response()->json(['message' => 'CV deleted successfully.']);
    }
}
