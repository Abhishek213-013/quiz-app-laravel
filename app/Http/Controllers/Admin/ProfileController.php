<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function index()
    {
        $admin = [
            'firstName' => 'Administrator',
            'lastName' => 'User',
            'email' => Session::get('admin_email', 'admin@quiz.com'),
            'phone' => '+1 (555) 123-4567',
            'bio' => 'Administrator of the Quiz Application platform. Responsible for managing users, quizzes, and system settings.',
            'avatar' => null,
            'createdAt' => 'Jan 15, 2024',
            'lastLogin' => '2 hours ago',
        ];

        return Inertia::render('Admin/AdminProfile', [
            'profile' => $admin
        ]);
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'firstName' => 'required|string|max:255',
            'lastName' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'nullable|string|max:20',
            'bio' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Update session email if changed
        if ($request->email !== Session::get('admin_email')) {
            Session::put('admin_email', $request->email);
        }

        return back()->with([
            'message' => 'Profile updated successfully!',
            'type' => 'success'
        ]);
    }

    public function updateAvatar(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store('admin/avatars', 'public');
            // Store the path in session or database as needed
            Session::put('admin_avatar', $path);
        }

        return back()->with([
            'message' => 'Avatar updated successfully!',
            'type' => 'success'
        ]);
    }
}