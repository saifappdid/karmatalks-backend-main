@extends('layouts/contentLayoutMaster')

@section('title', 'Instant Consultation Chat History')

@section('page-style')
    <style>
        .chat-container {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.06);
            padding: 20px;
            max-width: 800px;
            margin: 0 auto;
        }

        .chat-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }

        .chat-box {
            max-height: 500px;
            overflow-y: auto;
            padding: 10px;
            border-radius: 10px;
            background-color: #f9f9f9;
        }

        .message {
            margin: 10px 0;
            display: flex;
        }

        .message.user {
            justify-content: flex-end;
        }

        .message .bubble {
            padding: 10px 15px;
            border-radius: 15px;
            max-width: 70%;
            word-wrap: break-word;
        }

        .message.user .bubble {
            background-color: var(--primary);
            color: #fff;
            border-bottom-right-radius: 0;
        }

        .message.astro .bubble {
            background-color: #e5e5e5;
            color: #000;
            border-bottom-left-radius: 0;
        }

        .chat-meta {
            font-size: 12px;
            color: #000;
            margin-top: 3px;
        }
    </style>
@endsection

@section('content')
    <section>
        <div class="chat-container">
            <div class="chat-header">
                <div>
                    <h5>{{ $history->user->name ?? 'User' }} 💬 {{ $history->astro->name ?? 'Astrologer' }}</h5>
                    <small>Consultation ID: #{{ $history->id }}</small>
                </div>
                <span class="badge bg-success">Chat History</span>
            </div>

            <div class="chat-box">
                @forelse($history->chats as $chat)
                    <div class="message {{ $chat->sender_type == 'user' ? 'user' : 'astro' }}">
                        <div class="bubble">
                            {{ $chat->message }}
                            <div class="chat-meta">
                                {{ ucfirst($chat->sender_type) }} • {{ $chat->created_at->format('d M Y, h:i A') }}
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-center text-muted">No chat history found.</p>
                @endforelse
            </div>
        </div>
    </section>
@endsection

@section('page-script')
    <script>
        // Optional: Auto-scroll to the bottom
        document.addEventListener('DOMContentLoaded', function() {
            const chatBox = document.querySelector('.chat-box');
            chatBox.scrollTop = chatBox.scrollHeight;
        });
    </script>
@endsection
