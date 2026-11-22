<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\ExperienceRequest;
use App\Http\Resources\Admin\ExperienceResource;
use App\Models\Experience;
use Illuminate\Http\Request;

class ExperienceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $experiences = Experience::latest()->orderBy('start_date', 'asc')->get();
        return ExperienceResource::collection($experiences);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ExperienceRequest $request)
    {
        $data = $request->validated();
        if ($request->hasFile('company_logo')) {
            $data['company_logo'] = $request->file('company_logo')->store('uploads/experiences', 'public');
        }
        $experience = Experience::create($data);
        return ExperienceResource::make($experience);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $experience = Experience::findOrFail($id);
        return ExperienceResource::make($experience);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ExperienceRequest $request, string $id)
    {
        $data = $request->validated();
        $experience = Experience::findOrFail($id);
        $experience->update($data);
        return ExperienceResource::make($experience);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $experience = Experience::findOrFail($id);
        $experience->delete();
        return response()->json(['message' => 'Experience deleted successfully']);
    }

    /**
     * Update the status to inactive.
     */
    public function inActiveStatus($id)
    {
        Experience::where('id', $id)->update(['status' => 0]);
        $experience = Experience::find($id);
        return ExperienceResource::make($experience);
    }

    /**
     * Update the status to active.
     */
    public function activeStatus($id)
    {
        Experience::where('id', $id)->update(['status' => 1]);
        $experience = Experience::find($id);
        return ExperienceResource::make($experience);
    }
}
