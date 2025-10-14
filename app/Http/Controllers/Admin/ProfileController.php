<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ProfileController extends BaseAdminController
{
    public function index()
    {
        if ($redirect = $this->checkAdminAuth()) {
            return $redirect;
        }

        return Inertia::render('Admin/AdminProfile', [
            'profile' => $this->getAdminProfileData(),
            'theme' => $this->getThemeData()
        ]);
    }

    public function update(Request $request)
    {
        if (!Session::get('admin_authenticated')) {
            return redirect()->route('admin.login');
        }

        $adminEmail = Session::get('admin_email');
        $adminUser = DB::table('users')->where('email', $adminEmail)->first();

        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'username' => 'required|string|max:255|alpha_dash|unique:users,username,' . ($adminUser->id ?? 0),
            'email' => 'required|email|max:255|unique:users,email,' . ($adminUser->id ?? 0),
            'phone' => 'nullable|string|max:20',
            'bio' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        if ($adminUser) {
            // Update existing admin user
            DB::table('users')->where('id', $adminUser->id)->update([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'username' => $request->username,
                'email' => $request->email,
                'phone' => $request->phone,
                'bio' => $request->bio,
                'updated_at' => now(),
            ]);

            // Update session email if email changed
            if ($request->email !== $adminEmail) {
                Session::put('admin_email', $request->email);
            }
            
            // Get updated user data
            $updatedUser = DB::table('users')->where('id', $adminUser->id)->first();
        } else {
            // Create admin user if doesn't exist
            $userId = DB::table('users')->insertGetId([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'username' => $request->username,
                'email' => $request->email,
                'phone' => $request->phone,
                'bio' => $request->bio,
                'role' => 'admin',
                'password' => Hash::make('temp_password'),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            Session::put('admin_email', $request->email);
            $updatedUser = DB::table('users')->where('id', $userId)->first();
        }

        return back()->with([
            'message' => 'Profile updated successfully!',
            'type' => 'success',
            'updatedProfile' => [
                'firstName' => $updatedUser->first_name ?? '',
                'lastName' => $updatedUser->last_name ?? '',
                'username' => $updatedUser->username ?? '',
                'email' => $updatedUser->email ?? '',
                'avatar' => $updatedUser->avatar ? Storage::url($updatedUser->avatar) : null,
            ]
        ]);
    }

    public function updateAvatar(Request $request)
    {
        if (!Session::get('admin_authenticated')) {
            return redirect()->route('admin.login');
        }

        $validator = Validator::make($request->all(), [
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        $adminEmail = Session::get('admin_email');
        $adminUser = DB::table('users')->where('email', $adminEmail)->first();

        if (!$adminUser) {
            return back()->withErrors(['message' => 'Admin user not found.']);
        }

        // Delete old avatar if exists
        if ($adminUser->avatar) {
            Storage::disk('public')->delete($adminUser->avatar);
        }

        // Store new avatar
        $path = $request->file('avatar')->store('avatars', 'public');

        // Update user record
        DB::table('users')->where('id', $adminUser->id)->update([
            'avatar' => $path,
            'updated_at' => now(),
        ]);

        // Get updated user data
        $updatedUser = DB::table('users')->where('id', $adminUser->id)->first();

        return back()->with([
            'message' => 'Avatar updated successfully!',
            'type' => 'success',
            'updatedProfile' => [
                'avatar' => $updatedUser->avatar ? Storage::url($updatedUser->avatar) : null,
                'firstName' => $updatedUser->first_name,
                'lastName' => $updatedUser->last_name,
            ]
        ]);
    }

    public function removeAvatar(Request $request)
    {
        if (!Session::get('admin_authenticated')) {
            return redirect()->route('admin.login');
        }

        $adminEmail = Session::get('admin_email');
        $adminUser = DB::table('users')->where('email', $adminEmail)->first();

        if ($adminUser && $adminUser->avatar) {
            Storage::disk('public')->delete($adminUser->avatar);
            DB::table('users')->where('id', $adminUser->id)->update([
                'avatar' => null,
                'updated_at' => now(),
            ]);
        }

        return back()->with([
            'message' => 'Avatar removed successfully!',
            'type' => 'success'
        ]);
    }

    public function updatePassword(Request $request)
    {
        if (!Session::get('admin_authenticated')) {
            return redirect()->route('admin.login');
        }

        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        $adminEmail = Session::get('admin_email');
        $adminUser = DB::table('users')->where('email', $adminEmail)->first();

        if (!$adminUser) {
            return back()->withErrors(['message' => 'Admin user not found in database.']);
        }

        if (!Hash::check($request->current_password, $adminUser->password)) {
            return back()->withErrors(['current_password' => 'The current password is incorrect.']);
        }

        DB::table('users')->where('id', $adminUser->id)->update([
            'password' => Hash::make($request->password),
            'updated_at' => now(),
        ]);

        return back()->with([
            'message' => 'Password updated successfully!',
            'type' => 'success'
        ]);
    }

    public function destroy(Request $request)
    {
        if (!Session::get('admin_authenticated')) {
            return redirect()->route('admin.login');
        }

        $validator = Validator::make($request->all(), [
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        $adminEmail = Session::get('admin_email');
        $adminUser = DB::table('users')->where('email', $adminEmail)->first();

        if ($adminUser) {
            if (!Hash::check($request->password, $adminUser->password)) {
                return back()->withErrors(['password' => 'The password is incorrect.']);
            }

            if ($adminUser->avatar) {
                Storage::disk('public')->delete($adminUser->avatar);
            }

            DB::table('users')->where('id', $adminUser->id)->delete();
        }

        Session::forget('admin_authenticated');
        Session::forget('admin_email');
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login')->with([
            'message' => 'Your account has been deleted successfully.',
            'type' => 'success'
        ]);
    }
}