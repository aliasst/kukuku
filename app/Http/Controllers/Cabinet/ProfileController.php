<?php

namespace App\Http\Controllers\Cabinet;

use App\Http\Controllers\Controller;
use App\Models\UserDocument;
use App\Models\UserFile;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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


    public function uploadAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048'
        ]);

        $user = auth()->user();
        $profile = $user->getOrCreateProfile();

        // Удаляем старый файл, если он есть
        if ($profile->avatar && Storage::disk('public')->exists($profile->avatar)) {
            Storage::disk('public')->delete($profile->avatar);
        }

        // Сохраняем новый файл
        $file = $request->file('avatar');
        $filename = 'avatars/' . Str::random(40) . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('avatars', basename($filename), 'public'); // storeAs создаст в папке avatars

        // Более простой вариант:
        // $path = $request->file('avatar')->store('avatars', 'public');

        $profile->avatar = $path;
        $profile->save();

        return response()->json([
            'success' => true,
            'avatar_url' => Storage::url($path)
        ]);
    }

    public function uploadDocuments(Request $request)
    {
        $request->validate([
            'documents.*' => 'required|file|max:10240|mimes:pdf,doc,docx,jpg,jpeg,png,zip,rar,txt'
        ]);

        $user = auth()->user();
        $uploadedFiles = [];

        foreach ($request->file('documents') as $file) {
            $originalName = $file->getClientOriginalName();
            $safeName = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $originalName);
            $path = $file->storeAs('documents/' . $user->id, $safeName, 'public');

            $doc = UserDocument::create([
                'user_id' => $user->id,
                'title' => pathinfo($originalName, PATHINFO_FILENAME),
                'file_path' => $path,
                'file_name' => $originalName,
                'file_type' => $file->getMimeType(),
                'file_size' => $file->getSize(),
                'category' => 'document',
                'description' => null
            ]);

            $uploadedFiles[] = [
                'id' => $doc->id,
                'name' => $originalName,
                'url' => $doc->getDownloadUrlAttribute(),
            ];
        }

        return response()->json([
            'success' => true,
            'files' => $uploadedFiles
        ]);
    }

    public function uploadFiles(Request $request)
    {
        $request->validate([
            'files.*' => 'required|file|max:10240' // 10 MB
        ]);

        $user = auth()->user();
        $uploadedFiles = [];

        foreach ($request->file('files') as $file) {
            $fileName = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('files/' . $user->id, $fileName, 'public');

            $userFile = UserFile::create([
                'user_id' => $user->id,
                'title' => '', // можно оставить пустым или вытащить из имени
                'file_path' => $path,
                'file_name' => $file->getClientOriginalName(),
                'file_type' => $file->getMimeType(),
                'file_size' => $file->getSize(),
                'category' => '',
                'description' => ''
            ]);

            $uploadedFiles[] = [
                'id'   => $userFile->id,
                'name' => $userFile->file_name,
                'url'  => Storage::url($path)
            ];
        }

        return response()->json([
            'success' => true,
            'files' => $uploadedFiles
        ]);
    }


    public function deleteFile(Request $request, $id)
    {
        $user = auth()->user();
        $type = $request->input('type'); // 'document' или 'file'

        $file = null;
        $storagePath = null;

        if ($type === 'document') {
            $file = UserDocument::where('id', $id)->where('user_id', $user->id)->first();
            if ($file) {
                $storagePath = $file->file_path;
            }
        } elseif ($type === 'file') {
            $file = UserFile::where('id', $id)->where('user_id', $user->id)->first();
            if ($file) {
                $storagePath = $file->file_path;
            }
        }

        if (!$file) {
            return response()->json(['success' => false, 'message' => 'Файл не найден'], 404);
        }

        // Удаляем физический файл
        if ($storagePath && Storage::disk('public')->exists($storagePath)) {
            Storage::disk('public')->delete($storagePath);
        }

        // Удаляем запись из БД
        $file->delete();

        return response()->json(['success' => true, 'message' => 'Файл удалён']);
    }

    public function updateField(Request $request)
    {
        $request->validate([
            'field' => 'required|in:full_name,birth_date,gender,phone,telegram',
            'value' => 'nullable|string|max:255',
        ]);

        $user = auth()->user();
        $profile = $user->getOrCreateProfile();
        $field = $request->field;
        $value = $request->value;

        // Валидация в зависимости от поля
        if ($field === 'birth_date') {
            if (!empty($value)) {
                try {
                    $date = Carbon::createFromFormat('d.m.Y', $value);
                    $value = $date->format('Y-m-d');
                } catch (\Exception $e) {
                    return response()->json(['success' => false, 'message' => 'Неверный формат даты'], 422);
                }
            } else {
                $value = null;
            }
        }

        if ($field === 'phone') {
            $value = preg_replace('/[^0-9+]/', '', $value);
        }

        if ($field === 'gender' && !in_array($value, ['male', 'female', null])) {
            return response()->json(['success' => false, 'message' => 'Неверное значение'], 422);
        }

        $profile->$field = $value;
        $profile->save();

        // Формируем отображаемое значение для возврата
        $displayValue = $value;
        if ($field === 'birth_date' && $value) {
            $displayValue = Carbon::parse($value)->format('d.m.Y');
        }
        if ($field === 'phone' && $value) {
            $displayValue = $value; // модно отформатировать, например, +7 999 123 45 67
        }

        return response()->json([
            'success' => true,
            'message' => 'Сохранено',
            'display_value' => $displayValue
        ]);
    }



}
