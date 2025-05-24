<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponseTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    use ApiResponseTrait;

     /* ------------------------------------------------------------
     |  Common helper – returns the user, DB dbColumn & disk folder
     * ---------------------------------------------------------- */
    private function meta(string $type): array
    {
        $adminId = Auth::guard('admin')->id();
        // folder endings keyed by the route segment
        $map = [
            'avatar'        => ['dbColumn' => 'avatar',        'folder' => "admin/$adminId/avatars"],
            // add more here
        ];

        abort_unless(isset($map[$type]), 422, 'Unsupported file type');

        return [
            'dbColumn' => $map[$type]['dbColumn'],                                 // DB dbColumn has same name
            'folder' => $map[$type]['folder'],     // disk folder
        ];
    }

    /* ============================================================
     *  PUT /admin/profile/file/{type}
     * ========================================================== */
    public function store(Request $request, string $type)
    {
        $meta = $this->meta($type);

        $request->validate(['file' => 'required|image|mimes:jpeg,png,webp|max:2048']);

        // delete previous file (if any)
        if ($old = Auth::guard('admin')->user()->getRawOriginal($meta['dbColumn'])) {
            Storage::disk('public')->delete("{$meta['folder']}/{$old}");
        }

        $file = $request->file('file')->store($meta['folder'], 'public');
        Auth::guard('admin')->user()->update([$meta['dbColumn'] => basename($file)]);

        return $this->successResponse("{$type} uploaded", ['url' => asset("storage/$file")], 201);
    }

    /* ============================================================
     *  DELETE /admin/profile/file/{type}
     * ========================================================== */
    public function destroy(string $type)
    {
        $meta = $this->meta($type);

        if ($name = Auth::guard('admin')->user()->getRawOriginal($meta['dbColumn'])) {
            Storage::disk('public')->delete("{$meta['folder']}/{$name}");
            Auth::guard('admin')->user()->update([$meta['dbColumn'] => null]);
        }

        return $this->successResponse("{$type} deleted");
    }

    public function profile()
    {
        return view('admin.profile')->with('admin', Auth::guard('admin')->user());
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'name'              => 'required|string|max:255',
            'phone'             => 'nullable|phone', 
            'email'             => 'required|string|max:255',
        ]);
        Auth::guard('admin')->user()->update($validated);
        return redirect()->back()->with('success', 'Profile updated successfully!');
    }

}
