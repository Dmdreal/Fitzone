<?php

return [
    'enabled' => env('OCR_ENABLED', true),
    'tesseract_binary' => env('TESSERACT_BINARY', 'tesseract'),
    'pdftoppm_binary' => env('PDFTOPPM_BINARY', 'pdftoppm'),
    'language' => env('OCR_LANGUAGE', 'eng'),
    'pdf_dpi' => (int) env('OCR_PDF_DPI', 220),
    'pdf_max_pages' => (int) env('OCR_PDF_MAX_PAGES', 8),
    'timeout' => (int) env('OCR_TIMEOUT', 90),
];
