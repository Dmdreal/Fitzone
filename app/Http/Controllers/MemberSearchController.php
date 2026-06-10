<?php

namespace App\Http\Controllers;

use App\Models\ClientChat;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class MemberSearchController extends Controller
{
    public function index(Request $request): View
    {
        $query = trim((string) $request->query('q'));

        $members = collect();

        if ($query !== '') {
            $members = User::query()
                ->where('role', 'member')
                ->where(function ($builder) use ($query) {
                    $builder->where('name', 'like', '%'.$query.'%')
                        ->orWhere('email', 'like', '%'.$query.'%')
                        ->orWhere('member_number', 'like', '%'.$query.'%')
                        ->orWhere('phone', 'like', '%'.$query.'%')
                        ->orWhere('headline', 'like', '%'.$query.'%');
                })
                ->with(['memberships.package', 'wallet'])
                ->orderBy('name')
                ->limit(30)
                ->get();
        }

        return view('members.search', [
            'query' => $query,
            'members' => $members,
        ]);
    }

    public function startChat(Request $request, User $member): RedirectResponse
    {
        abort_unless($member->role === 'member', 404);
        abort_if($member->id === Auth::id(), 403);

        $chat = $this->directChatFor(Auth::id(), $member->id);

        return redirect()->route('member-chats.show', $chat);
    }

    public function showChat(ClientChat $chat): View
    {
        $this->authorizeDirectChat($chat);

        return view('members.direct-chat', [
            'chat' => $chat->load(['member', 'trainer', 'messages.sender']),
            'otherUser' => $chat->member_id === Auth::id() ? $chat->trainer : $chat->member,
        ]);
    }

    public function sendMessage(Request $request, ClientChat $chat): RedirectResponse
    {
        $this->authorizeDirectChat($chat);

        $data = $request->validate([
            'body' => ['required', 'string', 'max:1000'],
        ]);

        ClientChatMessage::create([
            'client_chat_id' => $chat->id,
            'sender_id' => Auth::id(),
            'body' => $data['body'],
        ]);

        return redirect()->route('member-chats.show', $chat);
    }

    private function directChatFor(int $firstUserId, int $secondUserId): ClientChat
    {
        $userIds = [min($firstUserId, $secondUserId), max($firstUserId, $secondUserId)];
        $firstUser = User::findOrFail($userIds[0]);
        $secondUser = User::findOrFail($userIds[1]);

        return ClientChat::firstOrCreate(
            ['type' => 'member_direct', 'member_id' => $userIds[0], 'trainer_id' => $userIds[1]],
            ['title' => $firstUser->name.' and '.$secondUser->name]
        );
    }

    private function authorizeDirectChat(ClientChat $chat): void
    {
        abort_unless($chat->type === 'member_direct', 404);
        abort_unless(in_array(Auth::id(), [$chat->member_id, $chat->trainer_id], true), 403);
    }
}
