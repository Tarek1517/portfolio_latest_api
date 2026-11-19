<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SliderRequest;
use App\Http\Resources\Admin\SliderResource;
use Illuminate\Http\Request;
use App\Models\Slider;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;
use function App\Http\Helpers\uploadFile;

class SliderController extends Controller
{
    public function index()
    {
        $sliders = Slider::query()
            ->orderBy('order_number')
            ->paginate();
        return SliderResource::collection($sliders);
    }

    public function show($id)
    {
        $slider = Slider::find($id);

        return SliderResource::make($slider);
    }
    public function store(SliderRequest $request)
    {
        $data = $request->validated();
        $data['image'] = $request->file('image')->store('uploads', 'public');
        $slider = Slider::create($data);
        return SliderResource::make($slider);
    }


    public function update(Request $request, string $id)
    {
        $slider = Slider::findOrFail($id);
        $data = $request->validate([
            'image' => 'nullable|mimes:png,jpg,jpeg,webp',
            'url' => 'required|string',
            'order_number' => 'required|integer',
        ]);
        $data['image'] = $request->hasFile('image')
            ? $request->file('image')->store('uploads', 'public')
            : $slider->getRawOriginal('image');
        $slider->update($data);

        return SliderResource::make($slider);
    }
    public function destroy(string $id)
    {
        $slider = Slider::findOrFail($id);

        if ($slider) {
            if (File::exists(public_path($slider->image))) {
                File::delete(public_path($slider->image));
            }
            $slider->delete();
            return Response::HTTP_OK;
        }
    }
}
