<?php

namespace App\Console\Commands;

use App\Models\DownloadableForm;
use App\Services\DocumentTemplateService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class EnsureDownloadableFormsExist extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'forms:generate-files';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate valid physical PDF files for all DownloadableForm records if not already existing.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $forms = DownloadableForm::all();
        $this->info("Checking {$forms->count()} downloadable forms...");

        foreach ($forms as $form) {
            $path = $form->file_path ?: 'forms/formulir_'.$form->id.'.pdf';

            if (! Storage::disk('public')->exists($path)) {
                $pdf = DocumentTemplateService::createFormPdf($form->name, $form->version ?? '1.0');
                Storage::disk('public')->put($path, $pdf);
                $this->info("Generated: {$path} for [{$form->name}]");
            } else {
                $this->line("Exists: {$path}");
            }

            if ($form->file_path !== $path) {
                $form->update(['file_path' => $path]);
            }
        }

        $this->info('All downloadable forms are ready on public storage.');

        return Command::SUCCESS;
    }
}
