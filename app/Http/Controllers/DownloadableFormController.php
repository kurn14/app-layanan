<?php

namespace App\Http\Controllers;

use App\Models\DownloadableForm;
use App\Services\DocumentTemplateService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class DownloadableFormController extends Controller
{
    /**
     * Download the specified form file.
     */
    public function download(DownloadableForm $form): Response
    {
        $path = $form->file_path;

        // Ensure file exists on public disk, otherwise generate it
        if (! $path || ! Storage::disk('public')->exists($path)) {
            $path = 'forms/formulir_'.$form->id.'_'.Str::slug($form->name).'.pdf';
            $pdf = DocumentTemplateService::createFormPdf($form->name, $form->version ?? '1.0');
            Storage::disk('public')->put($path, $pdf);

            $form->update(['file_path' => $path]);
        }

        $extension = pathinfo($path, PATHINFO_EXTENSION) ?: 'pdf';
        $downloadFileName = Str::slug($form->name).'.'.$extension;

        return Storage::disk('public')->download($path, $downloadFileName);
    }
}
