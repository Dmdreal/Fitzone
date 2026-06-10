# OCR Setup

The attendance paperwork importer supports TXT, CSV, image uploads, and scanned PDFs. Images and scanned PDFs require Tesseract OCR and Poppler on the web server.

## Ubuntu / Debian

```bash
sudo apt update
sudo apt install -y tesseract-ocr tesseract-ocr-eng poppler-utils
```

## Environment

```env
OCR_ENABLED=true
TESSERACT_BINARY=tesseract
PDFTOPPM_BINARY=pdftoppm
OCR_LANGUAGE=eng
OCR_PDF_DPI=220
OCR_PDF_MAX_PAGES=8
OCR_TIMEOUT=90
```

Use full binary paths if the web user cannot find them, for example:

```env
TESSERACT_BINARY=/usr/bin/tesseract
PDFTOPPM_BINARY=/usr/bin/pdftoppm
```

## Docker

```dockerfile
RUN apt-get update && apt-get install -y tesseract-ocr tesseract-ocr-eng poppler-utils
```

## Verify

```bash
tesseract --version
pdftoppm -v
php artisan config:clear
php artisan ocr:status
```

Expected result:

```text
OCR ready for images and scanned PDFs.
```

## Restart

Restart the app runtime after installing binaries and clearing config. Examples:

```bash
sudo systemctl restart php8.2-fpm
sudo systemctl restart nginx
```

For queue-based deployments, restart workers too:

```bash
php artisan queue:restart
```

## Test In The App

Open the trainer attendance page, upload a phone photo or scanned PDF of an attendance sheet, and confirm the import results show matched member numbers/names with check-in and check-out times.
