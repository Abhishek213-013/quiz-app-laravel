<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class CustomizeController extends Controller
{
    public function index()
    {
        $customization = [
            'colorScheme' => Cache::get('customization.color_scheme', 'light'),
            'primaryColor' => Cache::get('customization.primary_color', 'blue'),
            'layout' => Cache::get('customization.layout', 'sidebar'),
            'customCSS' => Cache::get('customization.css', ''),
            'logo' => Cache::get('customization.logo'),
            'favicon' => Cache::get('customization.favicon'),
            'appName' => Cache::get('customization.app_name', config('app.name')),
            'welcomeMessage' => Cache::get('customization.welcome_message', 'Welcome to our Quiz Platform!'),
        ];

        return Inertia::render('Admin/AdminCustomize', [
            'customization' => $customization
        ]);
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'colorScheme' => 'required|in:light,dark,auto,blue',
            'primaryColor' => 'required|in:blue,green,purple,red,orange',
            'layout' => 'required|in:sidebar,topbar',
            'customCSS' => 'nullable|string',
            'appName' => 'required|string|max:255',
            'welcomeMessage' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        Cache::put('customization.color_scheme', $request->colorScheme);
        Cache::put('customization.primary_color', $request->primaryColor);
        Cache::put('customization.layout', $request->layout);
        Cache::put('customization.css', $request->customCSS);
        Cache::put('customization.app_name', $request->appName);
        Cache::put('customization.welcome_message', $request->welcomeMessage);

        return back()->with([
            'message' => 'Customization updated successfully!',
            'type' => 'success'
        ]);
    }

    public function uploadLogo(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'logo' => 'required|image|mimes:jpeg,png,jpg,svg|max:1024',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('branding', 'public');
            $logoUrl = asset('storage/' . $path);
            Cache::put('customization.logo', $logoUrl);
        }

        return back()->with([
            'message' => 'Logo uploaded successfully!',
            'type' => 'success'
        ]);
    }

    public function uploadFavicon(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'favicon' => 'required|image|mimes:ico,png|max:512',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        if ($request->hasFile('favicon')) {
            $path = $request->file('favicon')->store('branding', 'public');
            $faviconUrl = asset('storage/' . $path);
            Cache::put('customization.favicon', $faviconUrl);
        }

        return back()->with([
            'message' => 'Favicon uploaded successfully!',
            'type' => 'success'
        ]);
    }

    public function reset()
    {
        Cache::forget('customization.color_scheme');
        Cache::forget('customization.primary_color');
        Cache::forget('customization.layout');
        Cache::forget('customization.css');
        Cache::forget('customization.logo');
        Cache::forget('customization.favicon');
        Cache::forget('customization.app_name');
        Cache::forget('customization.welcome_message');

        return back()->with([
            'message' => 'Customization reset to default successfully!',
            'type' => 'success'
        ]);
    }
}