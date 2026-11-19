<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\TestimonialRequest;
use App\Http\Resources\Admin\TestimonialResource;
use App\Models\Testimonial;
use illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class TestimonialController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $Review = Testimonial::query()->with('project')->orderBy('created_at', 'DESC')->get();

        return TestimonialResource::collection($Review);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TestimonialRequest $request)
    {
        $data = $request->validated();
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('uploads', 'public');
        }
        $Testimonial = Testimonial::query()->create($data);
        return TestimonialResource::make($Testimonial);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $Testimonial = Testimonial::query()->findOrFail($id);

        return TestimonialResource::make($Testimonial);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TestimonialRequest $request, string $id)
    {
        $Testimonial = Testimonial::findOrFail($id);
        $data = $request->validated();
        $data['image'] = $request->hasFile('image')
            ? $request->file('image')->store('uploads', 'public')
            : $Testimonial->getRawOriginal('image');
        $Testimonial->update($data);

        return TestimonialResource::make($Testimonial);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $Testimonial = Testimonial::findOrFail($id);

        if ($Testimonial->image) {
            $logoPath = ltrim(str_replace('/storage/', '', $Testimonial->image), '/');
            if (Storage::disk('public')->exists($logoPath)) {
                Storage::disk('public')->delete($logoPath);
            }
        }
        $Testimonial->delete();

        return TestimonialResource::make($Testimonial);
    }
    public function inActiveStatus($id)
    {
        Testimonial::where('id', $id)->update(['status' => '0']);
        $testimonial = Testimonial::find($id);
        return TestimonialResource::make($testimonial);
    }

    public function activeStatus($id)
    {
        Testimonial::where('id', $id)->update(['status' => '1']);
        $testimonial = Testimonial::find($id);
        return TestimonialResource::make($testimonial);
    }
}
