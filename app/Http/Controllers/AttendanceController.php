<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\User;
use App\Services\OcrService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AttendanceController extends Controller
{
    public function scan(): View
    {
        return view('trainer.attendance-scan', [
            'todayAttendances' => Attendance::with('member')
                ->whereDate('attendance_date', now()->toDateString())
                ->latest()
                ->take(20)
                ->get(),
            'importResults' => session('importResults', []),
            'ocrStatus' => app(OcrService::class)->statusMessage(),
        ]);
    }

    public function mark(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'identifier' => ['required', 'string', 'max:255'],
        ]);

        $identifier = $this->normalizeIdentifier($data['identifier']);
        $member = User::where('role', 'member')
            ->where(function ($query) use ($identifier) {
                $query->where('member_number', $identifier)
                    ->orWhere('qr_token', $identifier);
            })
            ->first();

        if (! $member) {
            return back()->with('warning', 'No member found for that member number or QR token.');
        }

        if (! $member->memberships()->paidActive()->exists()) {
            return back()->with('warning', $member->name.' does not have an active paid membership.');
        }

        $attendance = Attendance::firstOrCreate(
            ['member_id' => $member->id, 'attendance_date' => now()->toDateString()],
            [
                'marked_by' => Auth::id(),
                'check_in_at' => now()->format('H:i:s'),
                'status' => 'present',
                'qr_code' => $member->qr_token,
            ]
        );

        if (! $attendance->wasRecentlyCreated && ! $attendance->check_out_at) {
            $attendance->update([
                'marked_by' => Auth::id(),
                'check_out_at' => now()->format('H:i:s'),
            ]);

            return back()->with('status', $member->name.' checked out successfully.');
        }

        if (! $attendance->wasRecentlyCreated) {
            return back()->with('status', $member->name.' is already checked out for today.');
        }

        return back()->with('status', $member->name.' checked in successfully.');
    }

    public function importPaperwork(Request $request, OcrService $ocr): RedirectResponse
    {
        $data = $request->validate([
            'attendance_date' => ['required', 'date'],
            'scan_text' => ['nullable', 'string', 'max:20000'],
            'sheet_file' => ['nullable', 'file', 'mimes:txt,csv,jpg,jpeg,png,webp,bmp,tif,tiff,pdf', 'max:10240'],
        ]);

        $text = trim((string) ($data['scan_text'] ?? ''));

        if ($request->hasFile('sheet_file')) {
            $file = $request->file('sheet_file');

            if ($ocr->isOcrFile($file)) {
                try {
                    $text .= "\n".$ocr->extractText($file);
                } catch (\RuntimeException $exception) {
                    return back()->with('warning', $exception->getMessage());
                }
            } else {
                $text .= "\n".$file->get();
            }
        }

        if (trim($text) === '') {
            return back()->with('warning', 'Paste scanned paperwork text or upload a TXT/CSV/image/PDF sheet first.');
        }

        $date = Carbon::parse($data['attendance_date'])->toDateString();
        $results = [];
        $imported = 0;

        foreach ($this->paperworkLines($text) as $line) {
            $match = $this->matchPaperworkLine($line);

            if (! $match['member']) {
                $results[] = [
                    'line' => $line,
                    'status' => 'Skipped',
                    'message' => 'No matching member number or member name found.',
                ];
                continue;
            }

            if ($match['times'] === []) {
                $results[] = [
                    'line' => $line,
                    'status' => 'Skipped',
                    'message' => 'Matched '.$match['member']->name.', but no time was found.',
                ];
                continue;
            }

            $attendance = Attendance::updateOrCreate(
                ['member_id' => $match['member']->id, 'attendance_date' => $date],
                [
                    'marked_by' => Auth::id(),
                    'check_in_at' => $match['times'][0],
                    'check_out_at' => $match['times'][1] ?? null,
                    'status' => 'present',
                    'qr_code' => $match['member']->qr_token,
                ]
            );

            $imported++;
            $results[] = [
                'line' => $line,
                'status' => $attendance->wasRecentlyCreated ? 'Created' : 'Updated',
                'message' => $match['member']->member_number.' - '.$match['member']->name.' | In: '.$match['times'][0].' | Out: '.($match['times'][1] ?? '-'),
            ];
        }

        return back()
            ->with('status', $imported.' attendance record'.($imported === 1 ? '' : 's').' imported from paperwork.')
            ->with('importResults', $results);
    }

    private function normalizeIdentifier(string $identifier): string
    {
        $identifier = trim($identifier);

        if (filter_var($identifier, FILTER_VALIDATE_URL)) {
            return basename(parse_url($identifier, PHP_URL_PATH) ?: $identifier);
        }

        return $identifier;
    }

    private function paperworkLines(string $text): array
    {
        return collect(preg_split('/\R+/', $text) ?: [])
            ->map(fn ($line) => trim(preg_replace('/\s+/', ' ', str_replace([',', ';', "\t"], ' ', $line))))
            ->filter(fn ($line) => $line !== '')
            ->values()
            ->all();
    }

    private function matchPaperworkLine(string $line): array
    {
        $member = $this->memberFromLine($line);

        return [
            'member' => $member,
            'times' => $this->timesFromLine($line),
        ];
    }

    private function memberFromLine(string $line): ?User
    {
        if (preg_match('/\bGYM[-\s]?\d+\b/i', $line, $matches)) {
            preg_match('/\d+/', $matches[0], $digits);
            $memberNumber = 'GYM-'.str_pad($digits[0], 4, '0', STR_PAD_LEFT);
            $member = User::where('role', 'member')->where('member_number', $memberNumber)->first();

            if ($member) {
                return $member;
            }
        }

        $normalizedLine = Str::of($line)
            ->lower()
            ->replaceMatches('/\b\d{1,2}[:.]\d{2}\s*(am|pm)?\b/i', ' ')
            ->replaceMatches('/\b\d{1,2}\s*(am|pm)\b/i', ' ')
            ->replaceMatches('/\bgym[-\s]?\d+\b/i', ' ')
            ->replaceMatches('/[^a-z\s]/', ' ')
            ->squish()
            ->toString();

        return User::where('role', 'member')
            ->get()
            ->first(function (User $member) use ($normalizedLine) {
                $name = Str::of($member->name)->lower()->replaceMatches('/[^a-z\s]/', ' ')->squish()->toString();

                return $name !== '' && str_contains($normalizedLine, $name);
            });
    }

    private function timesFromLine(string $line): array
    {
        preg_match_all('/\b(?:[01]?\d|2[0-3])[:.][0-5]\d\s*(?:am|pm)?\b|\b(?:1[0-2]|0?[1-9])\s*(?:am|pm)\b/i', $line, $matches);

        return collect($matches[0] ?? [])
            ->map(fn ($time) => $this->normalizeTime($time))
            ->filter()
            ->unique()
            ->take(2)
            ->values()
            ->all();
    }

    private function normalizeTime(string $time): ?string
    {
        $time = strtolower(trim(str_replace('.', ':', $time)));

        foreach (['g:i a', 'g:ia', 'H:i', 'G:i', 'g a', 'ga'] as $format) {
            try {
                return Carbon::createFromFormat($format, $time)->format('H:i:s');
            } catch (\Throwable) {
                //
            }
        }

        return null;
    }
}
