<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\ProjectRequest;
use App\Http\Resources\Admin\ProjectResource;
use App\Http\Resources\Admin\LanguageResource;
use App\Models\Project;
use App\Models\ProjectImage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Symfony\Component\HttpFoundation\Response;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = (string) $request->query('search', '');
        $highlight = $request->query('highlight');
        $status = $request->query('status');

        $projects = Project::query()
            ->with('category', 'languages', 'images')
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                        ->orWhereHas('category', function ($q) use ($search) {
                            $q->where('name', 'like', "%{$search}%");
                        });
                });
            })
            ->when($highlight !== null, function ($query) use ($highlight) {
                $query->where('highlight', filter_var($highlight, FILTER_VALIDATE_BOOLEAN));
            })
            ->when($status !== null, function ($query) use ($status) {
                $query->where('status', filter_var($status, FILTER_VALIDATE_BOOLEAN));
            })
            ->orderBy('created_at', 'DESC')
            ->paginate(10);

        return ProjectResource::collection($projects);
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
    public function store(ProjectRequest $request)
    {
        $data = $request->validated();
        $data['slug'] = Str::slug($data['slug'] ?? $data['title']);
        $filePrefix = $data['slug'];
        $quality = 90;

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('uploads/projects', 'public');
        }

        $languageIds = $data['language_ids'] ?? [];
        if (isset($data['language_ids'])) {
            unset($data['language_ids']);
        }

        $galleryImages = [];
        if (isset($data['gallery_images'])) {
            $galleryImages = $data['gallery_images'];
            unset($data['gallery_images']);
        }

        $Project = Project::create($data);

        // sync languages
        if (!empty($languageIds)) {
            $Project->languages()->sync($languageIds);
        }

        //save project images
        if (!empty($galleryImages)) {
            $files = $galleryImages;
            $uploadedFiles = \App\Http\Helpers\multipleFileUpload(
                $files,
                $filePrefix,
                $quality
            );
            $imageData = array_map(function ($filePath) use ($Project) {
                return [
                    'image' => $filePath,
                    'project_id' => $Project->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }, $uploadedFiles);
            if (!empty($imageData)) {
                ProjectImage::insert($imageData);
            }
        }

        return ProjectResource::make($Project->load(['category', 'languages', 'images']));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $slug)
    {
        $project = Project::where('slug', $slug)
            ->with(['images', 'category', 'languages', 'testimonials' => function ($query) {
                $query->where('status', 1);
            }])
            ->first();

        // Fallback for admin route which passes ID instead of slug
        if (!$project) {
            $project = Project::where('id', $slug)
                ->with(['images', 'category', 'languages', 'testimonials' => function ($query) {
                    $query->where('status', 1);
                }])
                ->first();

            if (!$project) {
                abort(404, 'Project not found');
            }
        }

        return ProjectResource::make($project);
    }


    public function relatedProjects(string $slug)
    {
        $project = Project::where('slug', $slug)
            ->first();

        $relatedProjects = Project::where('category_id', $project->category_id)
            ->where('id', '!=', $project->id)
            ->with(['images', 'category', 'languages'])
            ->get();

        return ProjectResource::collection($relatedProjects);
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
    public function update(ProjectRequest $request, string $id)
    {
        $project = Project::findOrFail($id);
        $data = $request->validated();

        if (empty($data['slug'])) {
            if ($data['title'] !== $project->title) {
                $data['slug'] = Str::slug($data['title']);
            } else {
                $data['slug'] = $project->slug;
            }
        } else {
            $data['slug'] = Str::slug($data['slug']);
        }

        $filePrefix = $project->slug;
        $quality = 90;

        // update cover image
        if ($request->hasFile('image')) {
            if ($project->image && File::exists(public_path('storage/' . $project->image))) {
                File::delete(public_path('storage/' . $project->image));
            } elseif ($project->image && File::exists(public_path($project->image))) {
                File::delete(public_path($project->image));
            }
            $data['image'] = $request->file('image')->store('uploads/projects', 'public');
        } else {
            unset($data['image']);
        }

        $languageIds = $data['language_ids'] ?? null;
        if (array_key_exists('language_ids', $data)) {
            unset($data['language_ids']);
        }

        $galleryImages = $data['gallery_images'] ?? [];
        if (array_key_exists('gallery_images', $data)) unset($data['gallery_images']);

        $project->update($data);

        if ($languageIds !== null) {
            $project->languages()->sync($languageIds);
        }

        //save project images
        if (!empty($galleryImages)) {
            $files = $galleryImages;
            $uploadedFiles = \App\Http\Helpers\multipleFileUpload(
                $files,
                $filePrefix,
                $quality
            );
            $nextOrder = $project->images()->max('order') ?? -1;
            $imageData = array_map(function ($filePath) use ($project, &$nextOrder) {
                return [
                    'image' => $filePath,
                    'project_id' => $project->id,
                    'order' => ++$nextOrder,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }, $uploadedFiles);
            if (!empty($imageData)) {
                ProjectImage::insert($imageData);
            }
        }

        return ProjectResource::make($project->load(['category', 'languages', 'images']));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $project = Project::findOrFail($id);
        if ($project) {
            if ($project->image) {
                if (File::exists(public_path('storage/' . $project->image))) {
                    File::delete(public_path('storage/' . $project->image));
                } elseif (File::exists(public_path($project->image))) {
                    File::delete(public_path($project->image));
                }
            }

            if ($project->images) {
                foreach ($project->images as $image) {
                    if (File::exists(public_path($image->image))) {
                        File::delete(public_path($image->image));
                    }
                    $image->delete();
                }
            }

            $project->languages()->detach();
            $project->delete();

            return Response::HTTP_OK;
        }
    }

    public function deleteImage(string $id)
    {
        $image = ProjectImage::findOrFail($id);
        if ($image) {
            if (File::exists(public_path($image->image))) {
                File::delete(public_path($image->image));
            }
            $image->delete();

            return Response::HTTP_OK;
        }
    }
}
