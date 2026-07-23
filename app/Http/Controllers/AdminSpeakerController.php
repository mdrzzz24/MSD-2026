<?php

namespace App\Http\Controllers;

use App\Models\Speaker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminSpeakerController extends Controller
{
    public function index()
    {
        $speakers = Speaker::orderBy('name')->get();
        return view('admin.speakers.index', compact('speakers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'    => ['required', 'string', 'max:255'],
            'title'   => ['nullable', 'string', 'max:255'],
            'company' => ['nullable', 'string', 'max:255'],
            'photo'   => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'bio'     => ['nullable', 'string', 'max:2000'],
        ]);

        // Handle photo upload
        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('speakers', 'public');
            $this->fillTransparentWithWhite($path);
            $validated['photo'] = $path;
        }

        Speaker::create($validated + ['is_active' => true]);

        return redirect()->route('admin.speakers.index')
            ->with('success', 'Speaker <strong>' . e($validated['name']) . '</strong> created.');
    }

    public function update(Request $request, Speaker $speaker)
    {
        $validated = $request->validate([
            'name'    => ['required', 'string', 'max:255'],
            'title'   => ['nullable', 'string', 'max:255'],
            'company' => ['nullable', 'string', 'max:255'],
            'photo'   => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'bio'     => ['nullable', 'string', 'max:2000'],
        ]);

        // Handle photo upload
        if ($request->hasFile('photo')) {
            // Delete old photo
            if ($speaker->photo && Storage::disk('public')->exists($speaker->photo)) {
                Storage::disk('public')->delete($speaker->photo);
            }
            $path = $request->file('photo')->store('speakers', 'public');
            $this->fillTransparentWithWhite($path);
            $validated['photo'] = $path;
        } elseif ($request->input('remove_photo')) {
            // Remove photo
            if ($speaker->photo && Storage::disk('public')->exists($speaker->photo)) {
                Storage::disk('public')->delete($speaker->photo);
            }
            $validated['photo'] = null;
        }

        $speaker->update($validated);

        return redirect()->route('admin.speakers.index')
            ->with('success', 'Speaker <strong>' . e($speaker->name) . '</strong> updated.');
    }

    public function destroy(Speaker $speaker)
    {
        $name = $speaker->name;
        // Delete photo file
        if ($speaker->photo && Storage::disk('public')->exists($speaker->photo)) {
            Storage::disk('public')->delete($speaker->photo);
        }
        $speaker->agendaItems()->detach();
        $speaker->delete();

        return redirect()->route('admin.speakers.index')
            ->with('success', 'Speaker <strong>' . e($name) . '</strong> deleted.');
    }

    public function toggle(Speaker $speaker)
    {
        $speaker->update(['is_active' => !$speaker->is_active]);
        return back()->with('success', 'Speaker status toggled.');
    }

    /**
     * Replace transparent pixels with white background in an uploaded image.
     * Useful for PNG headshots with transparent backgrounds.
     */
    private function fillTransparentWithWhite(string $path): void
    {
        $fullPath = storage_path('app/public/' . $path);

        if (!file_exists($fullPath)) {
            return;
        }

        $info = getimagesize($fullPath);
        if (!$info) {
            return;
        }

        // Only process if the image has transparency (PNG or WebP)
        $mime = $info['mime'];
        if (!in_array($mime, ['image/png', 'image/webp'])) {
            return;
        }

        // Create image from file
        switch ($mime) {
            case 'image/png':
                $src = @imagecreatefrompng($fullPath);
                break;
            case 'image/webp':
                $src = @imagecreatefromwebp($fullPath);
                break;
            default:
                return;
        }

        if (!$src) {
            return;
        }

        $width = imagesx($src);
        $height = imagesy($src);

        // Create a new truecolor image with white background
        $whiteBg = imagecreatetruecolor($width, $height);
        $white = imagecolorallocate($whiteBg, 255, 255, 255);
        imagefill($whiteBg, 0, 0, $white);

        // Copy the original image onto the white background (preserves alpha blending)
        imagecopy($whiteBg, $src, 0, 0, 0, 0, $width, $height);

        // Save over the original file — preserve format
        switch ($mime) {
            case 'image/png':
                imagepng($whiteBg, $fullPath);
                break;
            case 'image/webp':
                imagewebp($whiteBg, $fullPath);
                break;
        }

        imagedestroy($src);
        imagedestroy($whiteBg);
    }
}
