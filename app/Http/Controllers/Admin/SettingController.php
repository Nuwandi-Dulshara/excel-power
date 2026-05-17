<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class SettingController extends Controller
{
    public function index()
    {
        return view('admin.settings.index', [
            'settings' => AppSetting::allAsArray(),
            'timezones' => timezone_identifiers_list(),
            'languages' => [
                'en' => 'English',
                'si' => 'Sinhala',
                'ta' => 'Tamil',
            ],
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'shop_name' => ['required', 'string', 'max:255'],
            'shop_name_second_line' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:1000'],
            'phone_number' => ['nullable', 'string', 'max:100'],
            'email' => ['nullable', 'email', 'max:255'],
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'currency' => ['required', 'string', 'max:20'],
            'invoice_prefix' => ['required', 'string', 'max:20'],
            'print_size' => ['required', Rule::in(['58mm', '80mm'])],
            'footer_message' => ['nullable', 'string', 'max:500'],
            'timezone' => ['required', 'timezone'],
            'language' => ['required', Rule::in(['en', 'si', 'ta'])],
        ]);

        if ($request->hasFile('logo')) {
            $oldLogo = AppSetting::getValue('logo');

            if ($oldLogo) {
                Storage::disk('public')->delete($oldLogo);
            }

            $validated['logo'] = $request->file('logo')->store('settings', 'public');
        } else {
            unset($validated['logo']);
        }

        foreach ($validated as $key => $value) {
            AppSetting::setValue($key, $value);
        }

        return redirect()
            ->route('admin.settings.index')
            ->with('success', 'Settings updated successfully.');
    }

    public function backup(): BinaryFileResponse|RedirectResponse
    {
        if (config('database.default') !== 'sqlite') {
            return redirect()
                ->route('admin.settings.index')
                ->with('error', 'Database backup is currently available only for SQLite databases.');
        }

        $databasePath = database_path('database.sqlite');

        if (! File::exists($databasePath)) {
            return redirect()
                ->route('admin.settings.index')
                ->with('error', 'Database file was not found.');
        }

        return response()->download(
            $databasePath,
            'pos-backup-'.now()->format('Ymd-His').'.sqlite'
        );
    }

    public function restore(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'database_file' => ['required', 'file', 'max:51200'],
        ]);

        if (config('database.default') !== 'sqlite') {
            return redirect()
                ->route('admin.settings.index')
                ->with('error', 'Database restore is currently available only for SQLite databases.');
        }

        $databasePath = database_path('database.sqlite');
        $backupPath = database_path('database-before-restore-'.now()->format('Ymd-His').'.sqlite');
        $extension = strtolower($validated['database_file']->getClientOriginalExtension());

        if (! in_array($extension, ['sqlite', 'db'], true)) {
            return redirect()
                ->route('admin.settings.index')
                ->with('error', 'Please upload a valid .sqlite or .db database file.');
        }

        if (File::exists($databasePath)) {
            File::copy($databasePath, $backupPath);
        }

        File::copy($validated['database_file']->getRealPath(), $databasePath);

        return redirect()
            ->route('admin.settings.index')
            ->with('success', 'Database restored successfully. A backup of the previous database was saved.');
    }
}
