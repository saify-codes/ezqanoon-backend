<?php

namespace App\Http\Controllers\Lawyer;

use App\Http\Controllers\Controller;
use App\Models\Availability;
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
        $userId = Auth::guard('lawyer')->id();
        // folder endings keyed by the route segment
        $map = [
            'avatar'        => ['dbColumn' => 'avatar',        'folder' => "lawyers/$userId/avatars"],
            'selfie'        => ['dbColumn' => 'selfie',        'folder' => "lawyers/$userId/selfies"],
            'licence_front' => ['dbColumn' => 'licence_front', 'folder' => "lawyers/$userId/licences"],
            'licence_back'  => ['dbColumn' => 'licence_back',  'folder' => "lawyers/$userId/licences"],
        ];

        abort_unless(isset($map[$type]), 422, 'Unsupported file type');

        return [
            'dbColumn' => $map[$type]['dbColumn'],                                 // DB dbColumn has same name
            'folder'   => $map[$type]['folder'],     // disk folder
        ];
    }

    /* ============================================================
     *  PUT /lawyer/profile/file/{type}
     * ========================================================== */
    public function store(Request $request, string $type)
    {
        $meta = $this->meta($type);

        $request->validate(['file' => 'required|image|mimes:jpeg,png,webp|max:2048']);

        // delete previous file (if any)
        if ($old = Auth::guard('lawyer')->user()->getRawOriginal($meta['dbColumn'])) {
            Storage::disk('public')->delete("{$meta['folder']}/{$old}");
        }

        $file = $request->file('file')->store($meta['folder'], 'public');
        Auth::guard('lawyer')->user()->update([$meta['dbColumn'] => basename($file)]);

        return $this->successResponse("{$type} uploaded", ['url' => asset("storage/$file")], 201);
    }

    /* ============================================================
     *  DELETE /lawyer/profile/file/{type}
     * ========================================================== */
    public function destroy(string $type)
    {
        $meta = $this->meta($type);

        if ($name = Auth::guard('lawyer')->user()->getRawOriginal($meta['dbColumn'])) {
            Storage::disk('public')->delete("{$meta['folder']}/{$name}");
            Auth::guard('lawyer')->user()->update([$meta['dbColumn'] => null]);
        }

        return $this->successResponse("{$type} deleted");
    }

    public function profile()
    {
        return view('lawyer.profile')
            ->with('lawyer', Auth::guard('lawyer')->user());
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'name'              => 'required|string|max:255',
            'licence_number'    => 'required|string|max:255',
            'cnic'              => 'required|string|max:255',
            'city'              => 'required|string|max:255',
            'country'           => 'nullable|string|max:255',
            'location'          => 'required|string|max:255',
            'specialization'    => 'required|array',
            'qualification'     => 'required|string|max:255',
            'experience'        => 'required|integer|min:0',
            'price'             => 'required|integer|min:0',
            'availability'      => 'nullable|array',
            'availability.*'    => 'nullable|array',
            'availability.*.*'  => 'nullable|date_format:H:i',
            'description'       => 'required|string',
        ]);

        //if time slots given
        if (!empty($request->availability)) {
            
            // remove old records
            Availability::where('lawyer_id', Auth::guard('lawyer')->id())->delete();

            foreach ($request->availability as $day => $times) {
                foreach ($times as $time) {
                    Availability::create([
                        'lawyer_id' => Auth::guard('lawyer')->id(),
                        'day'       => $day,
                        'time'      => $time,
                    ]);
                }
            }

        }

        Auth::guard('lawyer')->user()->update($data + ['is_profile_completed' => true]);

        return back()->with('success', 'Profile updated successfully!');
    }
}
