@extends('layouts.app')

@section('title', 'Ticket #' . $ticket->id)

@section('content')
<div style="margin-bottom: 2rem;">
    <a href="{{ route('tickets.index') }}" style="color: var(--text-muted); text-decoration: none; font-size: 0.875rem;">&larr; Back to Tickets</a>
    <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 0.5rem;">
        <div>
            <h2 style="margin: 0 0 0.25rem;">{{ $ticket->subject }}</h2>
            <div style="display: flex; gap: 1rem; color: var(--text-muted); font-size: 0.9rem;">
                <span>Category: {{ $ticket->category }}</span>
                <span>&bull;</span>
                <span>Server: {{ $ticket->server ? $ticket->server->name : 'None' }}</span>
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
                <button class="btn btn-danger" style="padding: 0.5rem 1rem; width: auto;">Close Ticket</button>
            </form>
            @endif
        </div>
    </div>
</div>

<div class="ticket-chat-container">
    @foreach($ticket->messages as $msg)
        @php
            // Identify if the message is from an admin, or the ticket owner.
            $isAdminMessage = $msg->user->isAdmin();
            $isOwnMessage = $msg->user_id === auth()->id();
        @endphp

        <div class="chat-message {{ $isOwnMessage ? 'chat-right' : 'chat-left' }}" style="display: flex; gap: 1rem; margin-bottom: 1.5rem; {{ $isOwnMessage ? 'flex-direction: row-reverse;' : '' }}">
            <!-- Avatar -->
            <div style="width: 40px; height: 40px; border-radius: 50%; flex-shrink: 0; display: flex; align-items: center; justify-content: center; font-weight: 600; color: #fff; background: {{ $isAdminMessage ? 'var(--primary)' : 'var(--bg-modifier-hover)' }};">
                {{ strtoupper(substr($msg->user->username, 0, 1)) }}
            </div>
            
            <!-- Message Bubble -->
            <div style="max-width: 80%; display: flex; flex-direction: column; {{ $isOwnMessage ? 'align-items: flex-end;' : 'align-items: flex-start;' }}">
                <div style="font-size: 0.8rem; color: var(--text-muted); margin-bottom: 0.25rem;">
                    {{ $msg->user->username }} @if($isAdminMessage) <span class="badge" style="background: var(--primary); color: #fff; font-size: 0.6rem; padding: 0.1rem 0.4rem; margin-left: 0.25rem;">ADMIN</span> @endif &bull; {{ $msg->created_at->format('M d, H:i') }}
                </div>
                
                <div class="chat-bubble" style="padding: 1rem; border-radius: 12px; font-size: 0.95rem; line-height: 1.5; white-space: pre-wrap; {{ $isOwnMessage ? 'background: var(--primary); color: #fff; border-top-right-radius: 2px;' : 'background: var(--glass-bg); border: 1px solid var(--border); border-top-left-radius: 2px;' }}">
{{ $msg->message }}
                </div>
            </div>
        </div>
    @endforeach
</div>

@if($ticket->status !== 'Closed')
<div class="glass-panel" style="margin-top: 2rem;">
    <form action="{{ route('tickets.reply', $ticket) }}" method="POST">
        @csrf
        <div class="form-group">
            <textarea name="message" class="form-input" rows="4" placeholder="Type your reply here..." required style="resize: vertical;"></textarea>
        </div>
        <div style="display: flex; justify-content: flex-end;">
            <button type="submit" class="btn btn-primary" style="padding: 0.75rem 2rem;">
                <i data-feather="send" style="width: 16px; height: 16px; margin-right: 0.5rem;"></i> Send Reply
            </button>
        </div>
    </form>
</div>
@endif
@endsection
