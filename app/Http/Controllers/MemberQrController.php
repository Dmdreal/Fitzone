<?php

namespace App\Http\Controllers;

use App\Models\User;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\SvgWriter;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class MemberQrController extends Controller
{
    public function svg(Request $request, User $member): Response
    {
        abort_unless($member->role === 'member', 404);
        abort_unless(Auth::user()->role !== 'member' || Auth::id() === $member->id, 403);

        $member->ensureMemberIdentity();

        $qrCode = new QrCode(
            data: route('members.qr.show', $member->qr_token),
            encoding: new Encoding('UTF-8'),
            errorCorrectionLevel: ErrorCorrectionLevel::High,
            size: 340,
            margin: 14,
            roundBlockSizeMode: RoundBlockSizeMode::Margin,
            foregroundColor: new Color(15, 23, 42),
            backgroundColor: new Color(255, 255, 255),
        );

        $result = (new SvgWriter())->write($qrCode);

        $headers = [
            'Content-Type' => $result->getMimeType(),
            'Cache-Control' => 'private, max-age=3600',
        ];

        if ($request->boolean('download')) {
            $headers['Content-Disposition'] = 'attachment; filename="'.$member->member_number.'-fitzone-qr.svg"';
        }

        return response($result->getString(), 200, $headers);
    }

    public function show(string $token)
    {
        $member = User::where('role', 'member')
            ->where('qr_token', $token)
            ->with([
                'memberships' => fn ($query) => $query->with(['package', 'trainer'])->latest(),
                'attendances' => fn ($query) => $query->latest('attendance_date')->take(5),
            ])
            ->firstOrFail();

        $member->ensureMemberIdentity();

        return view('members.qr-profile', [
            'member' => $member,
            'membership' => $member->memberships->first(),
            'activeMembership' => $member->memberships->first(fn ($membership) => $membership->status === 'active' && $membership->ends_at->greaterThanOrEqualTo(now()->startOfDay())),
            'attendances' => $member->attendances,
        ]);
    }
}
