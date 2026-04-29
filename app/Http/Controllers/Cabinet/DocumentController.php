<?php

namespace App\Http\Controllers\Cabinet;

use App\Http\Controllers\Controller;
use App\Models\UserDocument;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    // Скачать документ
    public function download(UserDocument $document)
    {
        // Проверяем, что документ принадлежит пользователю
        if ($document->user_id !== Auth::id()) {
            abort(403);
        }

        if (!Storage::disk('public')->exists($document->file_path)) {
            return redirect()->back()->with('error', 'Файл не найден');
        }

        return Storage::disk('public')->download($document->file_path, $document->file_name);
    }

    // Удалить документ
    public function destroy(UserDocument $document)
    {
        if ($document->user_id !== Auth::id()) {
            abort(403);
        }

        // Удаляем файл
        if (Storage::disk('public')->exists($document->file_path)) {
            Storage::disk('public')->delete($document->file_path);
        }

        $document->delete();

        return redirect()->route('documents.index')->with('success', 'Документ удален!');
    }
}
