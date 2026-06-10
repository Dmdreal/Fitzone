@extends('layouts.app')

@section('title', 'Voice Call - Fitzone')

@section('content')
<h1>Voice Call</h1>

<section class="card">
    <div style="display:flex;justify-content:space-between;gap:12px;align-items:center;flex-wrap:wrap">
        <div>
            <h2>{{ $call->caller->name }} and {{ $call->trainer->name }}</h2>
            <p class="muted">Status: <span id="call-status">{{ ucfirst($call->status) }}</span></p>
        </div>
        <form method="POST" action="{{ route('calls.end', $call) }}">
            @csrf
            <button class="btn ghost" type="submit">End Call</button>
        </form>
    </div>

    @if ($call->status === 'ringing' && ! $isCaller && auth()->id() === $call->trainer_id)
        <div class="actions" style="justify-content:flex-start">
            <form method="POST" action="{{ route('trainer.calls.accept', $call) }}">@csrf<button class="btn" type="submit">Accept Call</button></form>
            <form method="POST" action="{{ route('trainer.calls.decline', $call) }}">@csrf<button class="btn ghost" type="submit">Decline</button></form>
        </div>
    @endif

    <div class="grid two" style="margin-top:16px">
        <article class="card" style="box-shadow:none">
            <h2>Your Audio</h2>
            <p class="muted" id="local-state">Microphone waiting.</p>
            <audio id="local-audio" autoplay muted></audio>
        </article>
        <article class="card" style="box-shadow:none">
            <h2>Other Person</h2>
            <p class="muted" id="remote-state">Connection waiting.</p>
            <audio id="remote-audio" autoplay></audio>
        </article>
    </div>

    <div class="actions" style="justify-content:flex-start">
        <button class="btn" type="button" id="start-call">Start Audio</button>
        <button class="btn ghost" type="button" id="mute-call">Mute</button>
    </div>
    <p class="muted">Allow microphone access when your browser asks. For the database-signaled prototype, both people should keep this page open while the call connects.</p>
</section>

<script>
    const isCaller = @json($isCaller);
    const signalUrl = @json(route('calls.signal', $call));
    const csrfToken = @json(csrf_token());
    const startButton = document.getElementById('start-call');
    const muteButton = document.getElementById('mute-call');
    const localAudio = document.getElementById('local-audio');
    const remoteAudio = document.getElementById('remote-audio');
    const localState = document.getElementById('local-state');
    const remoteState = document.getElementById('remote-state');
    const callStatus = document.getElementById('call-status');

    let peer;
    let localStream;
    let started = false;
    let remoteIceCount = 0;

    async function signal(payload = null) {
        const options = payload
            ? { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken }, body: JSON.stringify(payload) }
            : { method: 'GET' };
        const response = await fetch(signalUrl, options);
        return response.json();
    }

    async function boot() {
        if (started) return;
        started = true;
        localStream = await navigator.mediaDevices.getUserMedia({ audio: true, video: false });
        localAudio.srcObject = localStream;
        localState.textContent = 'Microphone connected.';

        peer = new RTCPeerConnection({ iceServers: [{ urls: 'stun:stun.l.google.com:19302' }] });
        localStream.getTracks().forEach((track) => peer.addTrack(track, localStream));
        peer.ontrack = (event) => {
            remoteAudio.srcObject = event.streams[0];
            remoteState.textContent = 'Audio connected.';
        };
        peer.onicecandidate = (event) => {
            if (event.candidate) {
                signal({ ice_candidate: event.candidate.toJSON() });
            }
        };

        if (isCaller) {
            const offer = await peer.createOffer();
            await peer.setLocalDescription(offer);
            await signal({ offer_sdp: offer.sdp });
        }
    }

    async function poll() {
        const data = await signal();
        callStatus.textContent = data.status.charAt(0).toUpperCase() + data.status.slice(1);

        if (!peer || data.status === 'ended' || data.status === 'declined') return;

        if (!isCaller && data.offer_sdp && !peer.currentRemoteDescription) {
            await peer.setRemoteDescription({ type: 'offer', sdp: data.offer_sdp });
            const answer = await peer.createAnswer();
            await peer.setLocalDescription(answer);
            await signal({ answer_sdp: answer.sdp });
        }

        if (isCaller && data.answer_sdp && !peer.currentRemoteDescription) {
            await peer.setRemoteDescription({ type: 'answer', sdp: data.answer_sdp });
        }

        const remoteIce = isCaller ? data.trainer_ice : data.caller_ice;
        for (const candidate of remoteIce.slice(remoteIceCount)) {
            await peer.addIceCandidate(candidate);
        }
        remoteIceCount = remoteIce.length;
    }

    startButton.addEventListener('click', async () => {
        startButton.disabled = true;
        try {
            await boot();
            await poll();
        } catch (error) {
            localState.textContent = 'Could not start audio: ' + error.message;
            startButton.disabled = false;
        }
    });

    muteButton.addEventListener('click', () => {
        if (!localStream) return;
        const enabled = !localStream.getAudioTracks()[0].enabled;
        localStream.getAudioTracks().forEach((track) => track.enabled = enabled);
        muteButton.textContent = enabled ? 'Mute' : 'Unmute';
    });

    setInterval(() => {
        poll().catch(() => {});
    }, 2500);
</script>
@endsection
