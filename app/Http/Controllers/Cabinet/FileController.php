<?php

namespace App\Http\Controllers\Cabinet;

use App\Http\Controllers\Controller;
use App\Models\UserFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
    // Скачать документ
    public function download(UserFile $file)
    {
        // Проверяем, что документ принадлежит пользователю
        if ($file->user_id !== Auth::id()) {
            abort(403);
        }

        if (!Storage::disk('public')->exists($file->file_path)) {
            return redirect()->back()->with('error', 'Файл не найден');
        }

        return Storage::disk('public')->download($file->file_path, $file->file_name);
    }

    // Удалить документ
    public function destroy(UserFile $file)
    {
        if ($file->user_id !== Auth::id()) {
            abort(403);
        }

        // Удаляем файл
        if (Storage::disk('public')->exists($file->file_path)) {
            Storage::disk('public')->delete($file->file_path);
        }

        $file->delete();

        return redirect()->route('documents.index')->with('success', 'Документ удален!');
    }
}
