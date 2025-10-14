<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;

class CustomizeController extends BaseAdminController
{
    public function index()
    {
        if ($redirect = $this->checkAdminAuth()) {
            return $redirect;
        }

        $customization = [
            'colorScheme' => Cache::get('customization.color_scheme', 'light'),
            'primaryColor' => Cache::get('customization.primary_color', 'blue'),
            'layout' => Cache::get('customization.layout', 'sidebar'),
        ];

        return Inertia::render('Admin/AdminCustomize', [
            'profile' => $this->getAdminProfileData(),
            'customization' => $customization
        ]);
    }

    public function update(Request $request)
    {
        if ($redirect = $this->checkAdminAuth()) {
            return $redirect;
        }

        $validator = Validator::make($request->all(), [
            'colorScheme' => 'required|in:light,dark,auto',
            'primaryColor' => 'required|in:blue,green,purple,red,orange',
            'layout' => 'required|in:sidebar,topbar',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Store in cache for persistence
        Cache::put('customization.color_scheme', $request->colorScheme);
        Cache::put('customization.primary_color', $request->primaryColor);
        Cache::put('customization.layout', $request->layout);

        return back()->with([
            'message' => 'Theme updated successfully!',
            'type' => 'success'
        ]);
    }

    public function reset(Request $request)
    {
        if ($redirect = $this->checkAdminAuth()) {
            return $redirect;
        }

        Cache::forget('customization.color_scheme');
        Cache::forget('customization.primary_color');
        Cache::forget('customization.layout');

        return back()->with([
            'message' => 'Theme reset to default successfully!',
            'type' => 'success'
        ]);
    }

    // Optional: If you want to use these methods
    public function getThemeSettings()
    {
        return response()->json([
            'colorScheme' => Cache::get('customization.color_scheme', 'light'),
            'primaryColor' => Cache::get('customization.primary_color', 'blue'),
            'layout' => Cache::get('customization.layout', 'sidebar'),
        ]);
    }

    public function applyTheme(Request $request)
    {
        // Alternative method if you want a separate apply endpoint
        $validator = Validator::make($request->all(), [
            'colorScheme' => 'required|in:light,dark,auto',
            'primaryColor' => 'required|in:blue,green,purple,red,orange',
            'layout' => 'required|in:sidebar,topbar',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        Cache::put('customization.color_scheme', $request->colorScheme);
        Cache::put('customization.primary_color', $request->primaryColor);
        Cache::put('customization.layout', $request->layout);

        return response()->json(['message' => 'Theme applied successfully!']);
    }
}