<?php

namespace App\Http\Controllers\API\Public;

use App\Http\Controllers\Controller;
use App\Http\Resources\HeroSectionResource;
use App\Http\Resources\Admin\SliderResource;
use App\Http\Resources\Admin\CounterResource;
use App\Models\HeroSection;
use App\Models\Slider;
use App\Models\Counter;
use App\Models\Testimonial;
use App\Models\FAQ;
use App\Models\Page;
use App\Models\CV;
use App\Http\Resources\Admin\TestimonialResource;
use App\Http\Resources\Admin\FAQResource;
use App\Http\Resources\PageResource;
use Illuminate\Support\Facades\Storage;

class FrontendHomeController extends Controller
{
    public function heroSection()
    {
        $heroSections = HeroSection::query()->where('status', 1)->latest()->first();
        return HeroSectionResource::make($heroSections);
    }

    public function slider()
    {
        $sliders = Slider::query()->where('status', 1)->latest()->get();
        return SliderResource::collection($sliders);
    }

    public function counter()
    {
        $counters = Counter::query()->where('status', 1)->get();
        return CounterResource::collection($counters);
    }

    public function testimonial()
    {
        $testimonials = Testimonial::query()->where('status', 1)->get();
        return TestimonialResource::collection($testimonials);
    }

    public function getCustomPage($slug)
    {
        $page = Page::where('slug', $slug)->first();
        return PageResource::make($page);
    }

    /**
     * Return the publicly accessible URL of the latest uploaded CV.
     */
    public function downloadCV()
    {
        $cv = CV::latest()->first();

        if (!$cv) {
            return response()->json(['message' => 'No CV available.'], 404);
        }

        return response()->json([
            'data' => [
                'url'       => Storage::url($cv->file_path),
                'file_name' => $cv->file_name,
            ]
        ]);
    }
}
