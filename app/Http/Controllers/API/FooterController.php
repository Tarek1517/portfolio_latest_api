<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\FooterRequest;
use App\Http\Resources\Admin\FooterResource;
use App\Models\Footer;
use App\Models\Page;
use Symfony\Component\HttpFoundation\Response;

class FooterController extends Controller
{
    public function index()
    {
        $columns = Footer::query()->orderBy('order_number')->get();

        foreach ($columns as $column) {
            $pageIds = json_decode($column->pages);
            $pages = Page::query()->whereIn('id', $pageIds)->get();

            $column['pages'] = $pages;
        }

        return FooterResource::make($columns);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(FooterRequest $request)
    {
        $data = $request->validated();
        if ($data['pages']) {
            $data['pages'] = json_encode($data['pages']);
        }
        $footer = Footer::create($data);

        return FooterResource::make($footer);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(FooterRequest $request, string $id)
    {
        $data = $request->validated();
        $footer = Footer::find($id);
        if ($data['pages']) {
            $data['pages'] = json_encode($data['pages']);
        }
        $footer->update($data);
        return FooterResource::make($footer);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $footer = Footer::find($id);
        if ($footer) {
            $footer->delete();

            return Response::HTTP_OK;
        }
    }

    public function footer()
    {
        //footer columns
        $footerColumns = Footer::query()->orderBy('order_number')->get();
        foreach ($footerColumns as $column) {
            $footerPageIds = json_decode($column->pages, true); // make sure it's an array
            if (is_array($footerPageIds) && !empty($footerPageIds)) {
                $footerPages = Page::query()
                    ->whereIn('id', $footerPageIds)
                    ->select('slug', 'title')
                    ->get();
                $column['pages'] = $footerPages;
            } else {
                $column['pages'] = []; // fallback if pages are empty or invalid
            }
        }

        return FooterResource::collection($footerColumns);
    }
}
