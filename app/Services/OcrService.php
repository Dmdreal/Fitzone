<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use RuntimeException;
use Symfony\Component\Process\Process;

class OcrService
{
    public function isAvailable(): bool
    {
        return (bool) config('ocr.enabled')
            && $this->binaryWorks((string) config('ocr.tesseract_binary'))
            && $this->binaryWorks((string) config('ocr.pdftoppm_binary'));
    }

    public function statusMessage(): string
    {
        if (! config('ocr.enabled')) {
            return 'OCR is disabled by OCR_ENABLED=false.';
        }

        $missing = [];

        if (! $this->binaryWorks((string) config('ocr.tesseract_binary'))) {
            $missing[] = 'tesseract';
        }

        if (! $this->binaryWorks((string) config('ocr.pdftoppm_binary'))) {
            $missing[] = 'pdftoppm/poppler';
        }

        return $missing === []
            ? 'OCR ready for images and scanned PDFs.'
            : 'OCR unavailable. Missing: '.implode(', ', $missing).'.';
    }

    public function extractText(UploadedFile $file): string
    {
        if (! $this->isAvailable()) {
            throw new RuntimeException($this->statusMessage());
        }

        return $this->isPdf($file)
            ? $this->extractPdfText($file)
            : $this->extractImageText($file->getRealPath() ?: $file->path());
    }

    public function isOcrFile(UploadedFile $file): bool
    {
        return $this->isPdf($file) || str_starts_with((string) $file->getMimeType(), 'image/');
    }

    private function extractImageText(string $path): string
    {
        $process = new Process([
            (string) config('ocr.tesseract_binary'),
            $path,
            'stdout',
            '-l',
            (string) config('ocr.language'),
        ]);
        $process->setTimeout((int) config('ocr.timeout'));
        $process->run();

        if (! $process->isSuccessful()) {
            throw new RuntimeException(trim($process->getErrorOutput()) ?: 'Tesseract OCR failed.');
        }

        return trim($process->getOutput());
    }

    private function extractPdfText(UploadedFile $file): string
    {
        $workDir = storage_path('app/ocr/'.Str::uuid());
        File::ensureDirectoryExists($workDir);

        try {
            $prefix = $workDir.'/page';
            $convert = new Process([
                (string) config('ocr.pdftoppm_binary'),
                '-r',
                (string) config('ocr.pdf_dpi'),
                '-png',
                '-f',
                '1',
                '-l',
                (string) config('ocr.pdf_max_pages'),
                $file->getRealPath() ?: $file->path(),
                $prefix,
            ]);
            $convert->setTimeout((int) config('ocr.timeout'));
            $convert->run();

            if (! $convert->isSuccessful()) {
                throw new RuntimeException(trim($convert->getErrorOutput()) ?: 'PDF conversion failed.');
            }

            $text = collect(glob($prefix.'-*.png') ?: [])
                ->sort()
                ->map(fn (string $imagePath) => $this->extractImageText($imagePath))
                ->filter()
                ->implode("\n");

            if (trim($text) === '') {
                throw new RuntimeException('No text could be extracted from the scanned PDF.');
            }

            return $text;
        } finally {
            File::deleteDirectory($workDir);
        }
    }

    private function isPdf(UploadedFile $file): bool
    {
        return strtolower($file->getClientOriginalExtension()) === 'pdf'
            || $file->getMimeType() === 'application/pdf';
    }

    private function binaryWorks(string $binary): bool
    {
        try {
            $process = new Process([$binary, '--version']);
            $process->setTimeout(5);
            $process->run();

            return $process->isSuccessful();
        } catch (\Throwable) {
            return false;
        }
    }
}
