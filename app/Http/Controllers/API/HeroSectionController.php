<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\HeroSectionRequest;
use App\Http\Resources\HeroSectionResource;
use App\Models\HeroSection;

class HeroSectionController extends Controller
{
    /**
     * Display a listing of all hero sections.
     */
    public function index()
    {
        $heroSections = HeroSection::query()->latest()->get();
        return HeroSectionResource::collection($heroSections);
    }

    /**
     * Store a newly created hero section.
     */
    public function store(HeroSectionRequest $request)
    {
        $data = $request->validated();
        $data['image'] = $request->file('image')->store('uploads', 'public');
        $heroSection = HeroSection::create($data);

        return new HeroSectionResource($heroSection);
    }

    /**
     * Display the specified hero section.
     */
    public function show(string $id)
    {
        $heroSection = HeroSection::findOrFail($id);
        return new HeroSectionResource($heroSection);
    }

    /**
     * Update the specified hero section.
     */
    public function update(HeroSectionRequest $request, string $id)
    {
        $heroSection = HeroSection::findOrFail($id);
        $data = $request->validated();
        $data['image'] = $request->hasFile('image')
            ? $request->file('image')->store('uploads', 'public')
            : $heroSection->getRawOriginal('image');
        $heroSection->update($data);

        return new HeroSectionResource($heroSection);
    }

    /**
     * Remove the specified hero section.
     */
    public function destroy(string $id)
    {
        $heroSection = HeroSection::findOrFail($id);
        $heroSection->delete();

        return response()->json([
            'message' => 'Hero Section deleted successfully.'
        ]);
    }

    public function inActiveStatus($id)
    {
        HeroSection::where('id', $id)->update(['status' => 0]);
        $heroSection = HeroSection::find($id);

        return HeroSectionResource::make($heroSection);
    }

    public function activeStatus($id)
    {
        HeroSection::query()->update(['status' => 0]);
        $heroSection = HeroSection::findOrFail($id);
        $heroSection->update(['status' => 1]);

        return HeroSectionResource::make($heroSection);
    }
}
