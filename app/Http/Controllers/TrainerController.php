<?php

namespace App\Http\Controllers;

use App\Models\CallRequest;
use App\Models\ClientChat;
use App\Models\ClientChatMessage;
use App\Models\AdminTrainerMessage;
use App\Models\Membership;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class TrainerController extends Controller
{
    public function dashboard(): View
    {
        $memberships = Membership::with(['member.attendances', 'package'])
            ->where('trainer_id', Auth::id())
            ->paidActive()
            ->latest()
            ->get();

        return view('trainer.dashboard', [
            'memberships' => $memberships,
            'chatCount' => ClientChat::where('type', 'trainer_direct')->where('trainer_id', Auth::id())->count(),
            'pendingCalls' => CallRequest::with('caller')
                ->where('trainer_id', Auth::id())
                ->where('status', 'ringing')
                ->whereHas('membership', fn ($query) => $query->paidActive())
                ->latest()
                ->get(),
            'adminMessages' => AdminTrainerMessage::with('admin')
                ->where('trainer_id', Auth::id())
                ->latest()
                ->take(5)
                ->get(),
        ]);
    }

    public function chat(Request $request): View
    {
        $chats = ClientChat::with(['member', 'messages.sender'])
            ->where('type', 'trainer_direct')
            ->where('trainer_id', Auth::id())
            ->whereHas('member.memberships', function ($query) {
                $query->paidActive()->where('trainer_id', Auth::id());
            })
            ->latest()
            ->get();

        $activeChat = $request->integer('chat')
            ? $chats->firstWhere('id', $request->integer('chat'))
            : $chats->first();

        return view('trainer.chat', [
            'chats' => $chats,
            'activeChat' => $activeChat,
            'pendingCalls' => CallRequest::with('caller')
                ->where('trainer_id', Auth::id())
                ->where('status', 'ringing')
                ->whereHas('membership', fn ($query) => $query->paidActive())
                ->latest()
                ->get(),
        ]);
    }

    public function sendMessage(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'chat_id' => ['required', 'exists:client_chats,id'],
            'body' => ['required', 'string', 'max:1000'],
        ]);

        $chat = ClientChat::where('id', $data['chat_id'])
            ->where('type', 'trainer_direct')
            ->where('trainer_id', Auth::id())
            ->whereHas('member.memberships', function ($query) {
                $query->paidActive()->where('trainer_id', Auth::id());
            })
            ->firstOrFail();

        ClientChatMessage::create([
            'client_chat_id' => $chat->id,
            'sender_id' => Auth::id(),
            'body' => $data['body'],
        ]);

        return redirect()->route('trainer.chat', ['chat' => $chat->id]);
    }
}
