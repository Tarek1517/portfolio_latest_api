<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\PageRequest;
use App\Http\Resources\PageResource;
use App\Models\Page;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class PageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $Page = Page::latest()->search(['title'], request()->search);
        return PageResource::collection($Page);
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
    public function store(PageRequest $request)
    {
        $data = $request->validated();
        $data['slug'] = str::slug($data['title']);
        $page = Page::create($data);
        return PageResource::make($page);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $Page = Page::where('id', $id)->first();
        return PageResource::make($Page);
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
    public function update(PageRequest $request, string $id)
    {
        $Page = Page::findOrFail($id);
        $validatedData = $request->validated();
        $validatedData['slug'] = Str::slug($validatedData['title']);
        $Page->update($validatedData);
        return new PageResource($Page);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $page = Page::findOrFail($id);

        if($page){
            $page->delete();
            return Response::HTTP_OK;
        }
    }
    
}
