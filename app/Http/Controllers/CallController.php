<?php

namespace App\Http\Controllers;

use App\Models\CallRequest;
use App\Models\ClientChat;
use App\Models\ClientChatMessage;
use App\Models\Membership;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CallController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $membership = $request->user()->memberships()->with(['package', 'trainer'])->paidActive()->latest()->first();

        if (! $membership) {
            return redirect()
                ->route('client.packages')
                ->with('warning', 'Your package days are over. Please recharge to unlock calling again.');
        }

        abort_unless($membership->trainer_id, 422, 'Choose a trainer before starting a call.');

        $chat = ClientChat::firstOrCreate(
            ['type' => 'trainer_direct', 'member_id' => Auth::id(), 'trainer_id' => $membership->trainer_id],
            ['title' => 'Chat with '.$membership->trainer->name]
        );

        $call = CallRequest::create([
            'caller_id' => Auth::id(),
            'trainer_id' => $membership->trainer_id,
            'membership_id' => $membership->id,
            'client_chat_id' => $chat->id,
            'status' => 'ringing',
            'caller_ice' => [],
            'trainer_ice' => [],
        ]);

        ClientChatMessage::create([
            'client_chat_id' => $chat->id,
            'sender_id' => Auth::id(),
            'body' => 'Started a voice call request. Open call #'.$call->id.' to join.',
        ]);

        return redirect()->route('calls.show', $call);
    }

    public function show(CallRequest $call): View
    {
        $this->authorizeCall($call);

        return view('calls.show', [
            'call' => $call->load(['caller', 'trainer', 'chat']),
            'isCaller' => $call->caller_id === Auth::id(),
        ]);
    }

    public function accept(CallRequest $call): RedirectResponse
    {
        abort_unless($call->trainer_id === Auth::id(), 403);
        abort_unless($call->membership_id && Membership::whereKey($call->membership_id)->paidActive()->exists(), 403);

        $call->update([
            'status' => 'accepted',
            'accepted_at' => now(),
        ]);

        return redirect()->route('calls.show', $call);
    }

    public function decline(CallRequest $call): RedirectResponse
    {
        abort_unless($call->trainer_id === Auth::id(), 403);

        $call->update(['status' => 'declined', 'ended_at' => now()]);

        return back();
    }

    public function end(CallRequest $call): RedirectResponse
    {
        $this->authorizeCall($call);

        $call->update(['status' => 'ended', 'ended_at' => now()]);

        return match (Auth::user()->role) {
            'trainer' => redirect()->route('trainer.chat', ['chat' => $call->client_chat_id]),
            'admin' => redirect()->route('admin.chats', ['chat' => $call->client_chat_id]),
            default => redirect()->route('client.chat', ['room' => 'trainer']),
        };
    }

    public function signal(Request $request, CallRequest $call): JsonResponse
    {
        $this->authorizeCall($call);

        if ($request->isMethod('post')) {
            $data = $request->validate([
                'offer_sdp' => ['nullable', 'string'],
                'answer_sdp' => ['nullable', 'string'],
                'ice_candidate' => ['nullable', 'array'],
            ]);

            $updates = [];
            $iceColumn = $call->caller_id === Auth::id() ? 'caller_ice' : 'trainer_ice';

            if (! empty($data['offer_sdp']) && $call->caller_id === Auth::id()) {
                $updates['offer_sdp'] = $data['offer_sdp'];
            }

            if (! empty($data['answer_sdp']) && $call->trainer_id === Auth::id()) {
                $updates['answer_sdp'] = $data['answer_sdp'];
            }

            if (! empty($data['ice_candidate'])) {
                $updates[$iceColumn] = array_values(array_merge($call->{$iceColumn} ?? [], [$data['ice_candidate']]));
            }

            if ($updates !== []) {
                $call->update($updates);
            }
        }

        $call->refresh();

        return response()->json([
            'status' => $call->status,
            'offer_sdp' => $call->offer_sdp,
            'answer_sdp' => $call->answer_sdp,
            'caller_ice' => $call->caller_ice ?? [],
            'trainer_ice' => $call->trainer_ice ?? [],
        ]);
    }

    private function authorizeCall(CallRequest $call): void
    {
        if (Auth::user()->role === 'member' && $call->caller_id === Auth::id()) {
            abort_unless(
                $call->membership_id && Membership::whereKey($call->membership_id)->paidActive()->exists(),
                403
            );
        }

        abort_unless(
            Auth::user()->role === 'admin'
            || in_array(Auth::id(), [$call->caller_id, $call->trainer_id], true),
            403
        );
    }
}
