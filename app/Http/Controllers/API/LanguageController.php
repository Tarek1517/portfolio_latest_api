<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\LanguageRequest;
use App\Http\Resources\Admin\LanguageResource;
use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LanguageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = (string) $request->query('search', '');

        $query = Language::query()->orderBy('title');

        if ($search) {
            $query->where('title', 'like', "%{$search}%");
        }

        $languages = $query->get();

        return LanguageResource::collection($languages);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(LanguageRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('uploads', 'public');
        }

        $language = Language::create($data);

        return LanguageResource::make($language);
    }

    /**
     * Display the specified resource.
     */
    public function show(Language $language)
    {
        return LanguageResource::make($language);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(LanguageRequest $request, Language $language)
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            // Delete old image if it exists
            if ($language->image && Storage::disk('public')->exists($language->image)) {
                Storage::disk('public')->delete($language->image);
            }
            $data['image'] = $request->file('image')->store('uploads', 'public');
        } else {
            // Keep the existing image; do not overwrite with null
            unset($data['image']);
        }

        $language->update($data);

        return LanguageResource::make($language);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Language $language)
    {
        if ($language->image && Storage::disk('public')->exists($language->image)) {
            Storage::disk('public')->delete($language->image);
        }

        $language->delete();

        return LanguageResource::make($language);
    }
}
