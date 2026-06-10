<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('ocr:status', function () {
    $ocr = app(\App\Services\OcrService::class);

    $this->line($ocr->statusMessage());

    return $ocr->isAvailable() ? self::SUCCESS : self::FAILURE;
})->purpose('Check whether Tesseract OCR and Poppler are available');
