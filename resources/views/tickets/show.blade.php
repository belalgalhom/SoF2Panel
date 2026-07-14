@extends('layouts.app')

@section('title', 'Ticket #' . $ticket->id)

@section('content')

<div style="max-width: 900px; margin: 0 auto;">
    {{-- Header --}}
    <div style="margin-bottom: 1.5rem;">
        <a href="{{ route('tickets.index') }}" style="color: var(--text-muted); text-decoration: none; font-size: 0.875rem;">&larr; Back to Tickets</a>
        <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 0.5rem;">
            <div>
                <h2 style="margin: 0 0 0.25rem;">{{ $ticket->subject }}</h2>
                <div style="display: flex; gap: 1rem; color: var(--text-muted); font-size: 0.9rem;">
                    <span>{{ $ticket->category }}</span>
                    @if($ticket->server)
                        <span>&bull;</span>
                        <span>{{ $ticket->server->name }}</span>
                    @endif
                </div>
            </div>
            <div style="display: flex; align-items: center; gap: 1rem;">
                @if($ticket->status === 'Open')
                    <span class="badge" style="background: rgba(16, 185, 129, 0.15); color: #34d399;">Open</span>
                @elseif($ticket->status === 'Answered')
                    <span class="badge" style="background: rgba(59, 130, 246, 0.15); color: #60a5fa;">Answered</span>
                @else
                    <span class="badge" style="background: rgba(239, 68, 68, 0.15); color: #f87171;">Closed</span>
                @endif

                @if($ticket->status !== 'Closed')
                <form action="{{ route('tickets.close', $ticket) }}" method="POST" style="margin: 0;">
                    @csrf
                    <button class="btn" style="width: auto; background: rgba(239, 68, 68, 0.1); border: 1px solid var(--danger); color: var(--danger); padding: 0.5rem 1rem; font-size: 0.9rem;">Close Ticket</button>
                </form>
                @endif
            </div>
        </div>
    </div>

    {{-- Chat Messages --}}
    <div class="glass-panel" style="max-width: 100%; padding: 1.5rem; margin-bottom: 1.5rem; display: flex; flex-direction: column; gap: 1.5rem;">
        @forelse($ticket->messages as $msg)
            @php
                $isAdmin = $msg->user->isAdmin();
                $isOwn   = $msg->user_id === auth()->id();
            @endphp

            <div style="display: flex; gap: 1rem; flex-direction: {{ $isOwn ? 'row-reverse' : 'row' }};">
                {{-- Avatar --}}
                <div style="width: 38px; height: 38px; border-radius: 50%; flex-shrink: 0; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.9rem; color: #fff; background: {{ $isAdmin ? 'var(--primary)' : 'rgba(255,255,255,0.08)' }};">
                    {{ strtoupper(substr($msg->user->username, 0, 1)) }}
                </div>

                {{-- Bubble --}}
                <div style="max-width: 70%; display: flex; flex-direction: column; align-items: {{ $isOwn ? 'flex-end' : 'flex-start' }};">
                    <div style="font-size: 0.78rem; color: var(--text-muted); margin-bottom: 0.3rem;">
                        {{ $msg->user->username }}
                        @if($isAdmin)
                            <span style="background: var(--primary); color: #fff; font-size: 0.65rem; padding: 0.1rem 0.4rem; border-radius: 4px; margin-left: 0.25rem;">ADMIN</span>
                        @endif
                        &bull; {{ $msg->created_at->format('M d, H:i') }}
                    </div>
                    <div style="padding: 0.85rem 1.1rem; border-radius: 14px; font-size: 0.92rem; line-height: 1.6; white-space: pre-wrap; word-break: break-word; text-align: left;
                        {{ $isOwn
                            ? 'background: var(--primary); color: #fff; border-bottom-right-radius: 3px;'
                            : 'background: rgba(255,255,255,0.05); border: 1px solid var(--border); border-bottom-left-radius: 3px;'
                        }}">{{ $msg->message }}</div>
                </div>
            </div>
        @empty
            <p style="color: var(--text-muted); text-align: center; padding: 2rem 0;">No messages yet.</p>
        @endforelse
    </div>

    {{-- Reply Box --}}
    @if($ticket->status !== 'Closed')
    <div class="glass-panel" style="max-width: 100%;">
        <form action="{{ route('tickets.reply', $ticket) }}" method="POST">
            @csrf
            <div class="form-group">
                <textarea name="message" class="form-input" rows="4" placeholder="Type your reply here..." required style="resize: vertical;"></textarea>
            </div>
            <div style="display: flex; justify-content: flex-end; margin-top: 1rem;">
                <button type="submit" class="btn btn-primary" style="width: auto; padding: 0.6rem 1.5rem;">
                    <i data-feather="send" style="width: 15px; height: 15px; margin-right: 0.5rem;"></i> Send Reply
                </button>
            </div>
        </form>
    </div>
    @endif
</div>

@endsection
