<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting;
use App\Models\Admin;
use App\Models\Page;
use App\Models\Project;
use App\Models\Footer;
use App\Models\Experience;
use function App\Http\Helpers\getSetting;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Hash;

class SettingController extends Controller
{
    public function getGlobalSetting()
    {

        $projectIds = json_decode(getSetting('home_projects'));
        $projects = [];
        if ($projectIds !== null && count($projectIds) > 0) {
            $projects = Project::whereIn('id', $projectIds ?? [])->with(['category', 'languages', 'images'])->get();
        }

        $experienceIds = json_decode(getSetting('home_experience'));
        $experiences = [];
        if ($experienceIds !== null && count($experienceIds) > 0) {
            $experiences = Experience::whereIn('id', $experienceIds ?? [])->get();
        }
        //footer columns
        $footerColumns = Footer::query()->orderBy('order_number')->get();
        foreach ($footerColumns as $column) {
            $footerPageIds = json_decode($column->pages);
            $footerPages = Page::query()->whereIn('id', $footerPageIds)->select('slug', 'title')->get();
            $column['pages'] = $footerPages;
        }

        $settings = [
            'home_projects' => $projects,
            'home_experience' => $experiences,
            'name' => getSetting('name'),
            'currency' => getSetting('currency'),
            'currency_symbol' => getSetting('currency_symbol'),
            'app_name' => getSetting('app_name'),
            'app_url' => getSetting('app_url'),
            'footer_columns' => $footerColumns,
            'logo_light' => getSetting('logo_light'),
            'logo_dark' => getSetting('logo_dark'),
            'home_image' => getSetting('home_image'),
            'email' => getSetting('email'),
            'termsandcondition_link' => getSetting('termsandcondition_link'),
            'privacyPolicy_link' => getSetting('privacyPolicy_link'),
            'cookiePolicy_link' => getSetting('cookiePolicy_link'),
            'phone_number' => getSetting('phone_number'),
            'whatsapp_number' => getSetting('whatsapp_number'),
            'hotline_number' => getSetting('hotline_number'),
            'facebook_link' => getSetting('facebook_link'),
            'github_link' => getSetting('github_link'),
            'instagram_link' => getSetting('instagram_link'),
            'linkedin_link' => getSetting('linkedin_link'),
            'twitter_link' => getSetting('twitter_link'),
            'address' => getSetting('address'),
        ];
        return response()->json($settings);
    }

    public function getAllSetting()
    {
        $settings = [
            'home_projects' => json_decode(getSetting('home_projects')),
            'home_experience' => json_decode(getSetting('home_experience')),
            'name' => getSetting('name'),
            'currency' => getSetting('currency'),
            'currency_symbol' => getSetting('currency_symbol'),
            'app_name' => getSetting('app_name'),
            'app_url' => getSetting('app_url'),
            'logo_light' => getSetting('logo_light'),
            'logo_dark' => getSetting('logo_dark'),
            'home_image' => getSetting('home_image'),
            'email' => getSetting('email'),
            'termsandcondition_link' => getSetting('termsandcondition_link'),
            'privacyPolicy_link' => getSetting('privacyPolicy_link'),
            'cookiePolicy_link' => getSetting('cookiePolicy_link'),
            'phone_number' => getSetting('phone_number'),
            'whatsapp_number' => getSetting('whatsapp_number'),
            'hotline_number' => getSetting('hotline_number'),
            'facebook_link' => getSetting('facebook_link'),
            'github_link' => getSetting('github_link'),
            'instagram_link' => getSetting('instagram_link'),
            'linkedin_link' => getSetting('linkedin_link'),
            'twitter_link' => getSetting('twitter_link'),
            'address' => getSetting('address'),
        ];


        $settings['logo_light_url'] = $settings['logo_light']
            ? asset($settings['logo_light'])
            : null;

        $settings['logo_dark_url'] = $settings['logo_dark']
            ? asset($settings['logo_dark'])
            : null;

        return response()->json($settings);
    }

    public function saveSetting(Request $request)
    {
        $data = $request->all();
        $keysToTransform = [
            'home_projects',
            'home_experience',
        ];

        foreach ($data as $type => $value) {

            if ($value === null) {
                continue;
            }

            $settings = Setting::where('key', $type)->first();

            if (!$settings) {
                $settings = new Setting();
                $settings->key = $type;
            }

            if (in_array($type, $keysToTransform)) {
                if (is_array($value)) {
                    if ($type === 'notices') {
                        $value = array_map(function ($item) {
                            return is_string($item) ? $item : strval($item);
                        }, $value);
                    } else {
                        $value = array_map('intval', $value);
                    }
                    $settings->value = json_encode($value);
                } else {

                    if ($type === 'notices') {

                        $decoded = json_decode($value, true);
                        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                            $settings->value = json_encode($decoded);
                        } else {

                            $settings->value = json_encode([$value]);
                        }
                    } else {
                        $settings->value = $value;
                    }
                }
            } else {
                $settings->value = $value;
            }
            $settings->save();
        }


        if ($request->hasFile('logo_light')) {
            $settings = Setting::where('key', 'logo_light')->first();
            if (!$settings) {
                $settings = new Setting();
                $settings->key = 'logo_light';
            }

            if ($settings->value) {
                Storage::disk('public')->delete(str_replace('storage/', '', $settings->value));
            }

            $value = $request->file('logo_light')->store('uploads', 'public');
            $settings->value = 'storage/' . $value;
            $settings->save();
        }

        if ($request->hasFile('logo_dark')) {
            $settings = Setting::where('key', 'logo_dark')->first();
            if (!$settings) {
                $settings = new Setting();
                $settings->key = 'logo_dark';
            }

            if ($settings->value) {
                Storage::disk('public')->delete(str_replace('storage/', '', $settings->value));
            }

            $value = $request->file('logo_dark')->store('uploads', 'public');
            $settings->value = 'storage/' . $value;
            $settings->save();
        }

        return response()->json(['status' => 'success'], Response::HTTP_OK);
    }
}
