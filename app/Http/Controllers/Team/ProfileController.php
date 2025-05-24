<?php

namespace App\Http\Controllers\Team;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    use ApiResponseTrait;

    /* ------------------------------------------------------------
     |  Common helper – returns the user, DB dbColumn & disk folder
     * ---------------------------------------------------------- */
    private function meta(string $type): array
    {
        $userId = Auth::guard('team')->id();
        // folder endings keyed by the route segment
        $map = [
            'avatar'        => ['dbColumn' => 'avatar',        'folder' => "teams/$userId/avatars"],
        ];

        abort_unless(isset($map[$type]), 422, 'Unsupported file type');

        return [
            'dbColumn' => $map[$type]['dbColumn'],                                 // DB dbColumn has same name
            'folder' => $map[$type]['folder'],     // disk folder
        ];
    }

    /* ============================================================
     *  PUT /team/profile/file/{type}
     * ========================================================== */
    public function store(Request $request, string $type)
    {
        $meta = $this->meta($type);

        $request->validate(['file' => 'required|image|mimes:jpeg,png,webp|max:2048']);

        // delete previous file (if any)
        if ($old = Auth::guard('team')->user()->getRawOriginal($meta['dbColumn'])) {
            Storage::disk('public')->delete("{$meta['folder']}/{$old}");
        }

        $file = $request->file('file')->store($meta['folder'], 'public');
        Auth::guard('team')->user()->update([$meta['dbColumn'] => basename($file)]);

        return $this->successResponse("{$type} uploaded", ['url' => asset("storage/$file")], 201);
    }

    /* ============================================================
     *  DELETE /team/profile/file/{type}
     * ========================================================== */
    public function destroy(string $type)
    {
        $meta = $this->meta($type);

        if ($name = Auth::guard('team')->user()->getRawOriginal($meta['dbColumn'])) {
            Storage::disk('public')->delete("{$meta['folder']}/{$name}");
            Auth::guard('team')->user()->update([$meta['dbColumn'] => null]);
        }

        return $this->successResponse("{$type} deleted");
    }

    public function profile()
    {
        return view('team.profile')
            ->with('team', Auth::guard('team')->user());
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'name'              => 'required|string|max:255',
        ]);

        Auth::guard('team')->user()->update($data);

        return back()->with('success', 'Profile updated successfully!');
    }
}
