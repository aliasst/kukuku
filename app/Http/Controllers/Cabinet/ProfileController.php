<?php

namespace App\Http\Controllers\Cabinet;

use App\Http\Controllers\Controller;
use App\Models\UserDocument;
use App\Models\UserFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{



    /**
     * Показать форму профиля
     */
    public function show()
    {
        $user = Auth::user();
        $profile = $user->getOrCreateProfile();

        $files = $user->files()->orderBy('created_at')->get();
        $documents = $user->documents()->orderBy('created_at')->get();



        return view('cabinet.profile.show', compact('user', 'profile', 'documents', 'files'));
    }

    /**
     * Сохранить или обновить профиль
     */
    public function store(Request $request)
    {


        $request->validate([
            'full_name' => 'nullable|string|max:255',
            'birth_date' => 'nullable|date|before:today',
            'gender' => 'nullable|in:male,female,other',
            'phone' => 'nullable|string|max:20',
            'telegram' => 'nullable|string|max:255',
            'visit_purpose' => 'nullable|string|max:5000',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'documents.*' => 'nullable|file|max:10240',
            'files.*' => 'nullable|file|max:10240',
        ]);

        $user = Auth::user();
        $profile = $user->getOrCreateProfile();

        $profile->update([
            'full_name' => $request->full_name,
            'birth_date' => $request->birth_date,
            'gender' => $request->gender,
            'phone' => $request->phone,
            'telegram' => $request->telegram,
            'visit_purpose' => $request->visit_purpose,
        ]);

        if ($request->hasfile('avatar')) {
            // Удаляем старую аватарку
            if ($profile->avatar && Storage::disk('public')->exists($profile->avatar)) {
                Storage::disk('public')->delete($profile->avatar);
            }

            // Сохраняем новую
            $path = $request->file('avatar')->store('avatars', 'public');
            $profile->avatar = $path;
            $profile->save();
        }
        if ($request->hasfile('documents')) {
            foreach ($request->file('documents') as $file) {
                $user = Auth::user();
                // Генерируем уникальное имя
                $fileName = time() . '_' . $file->getClientOriginalName();
                $path = $file->storeAs('documents/' . $user->id, $fileName, 'public');

                UserDocument::create([
                    'user_id' => $user->id,
                    'title' => '',
                    'file_path' => $path,
                    'file_name' => $file->getClientOriginalName(),
                    'file_type' => $file->getMimeType(),
                    'file_size' => $file->getSize(),
                    'category' => '',
                    'description' => ''
                ]);
            }
        }

        if ($request->hasfile('files')) {
            foreach ($request->file('files') as $file) {
                $user = Auth::user();
                // Генерируем уникальное имя
                $fileName = time() . '_' . $file->getClientOriginalName();
                $path = $file->storeAs('files/' . $user->id, $fileName, 'public');

                UserFile::create([
                    'user_id' => $user->id,
                    'title' => '',
                    'file_path' => $path,
                    'file_name' => $file->getClientOriginalName(),
                    'file_type' => $file->getMimeType(),
                    'file_size' => $file->getSize(),
                    'category' => '',
                    'description' => ''
                ]);
            }
        }


        return redirect()->route('profile.show')
            ->with('success', 'Профиль успешно сохранён!');
    }




}
