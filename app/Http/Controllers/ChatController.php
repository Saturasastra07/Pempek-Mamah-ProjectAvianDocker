<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Models\Conversation;
use App\Models\Message;
use App\Services\BotService;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function __construct(protected BotService $bot) {}

    public function open(Request $request)
    {
        $user = auth()->user();

        $conversation = Conversation::where('user_id', $user->id)
            ->whereIn('status', ['bot', 'waiting', 'active'])
            ->latest()
            ->first();

        if (!$conversation) {
            $conversation = Conversation::create([
                'user_id'         => $user->id,
                'order_id'        => $request->order_id ?? null,
                'status'          => 'bot',
                'last_message_at' => now(),
            ]);

            $this->bot->getWelcomeMessage($conversation);
        }

        $messages = $conversation->messages()->with('sender')->get();

        return response()->json([
            'conversation' => $conversation,
            'messages'     => $messages,
        ]);
    }

    public function refreshWelcome(Conversation $conversation)
    {
        $questions = $this->bot->getRandomQuestions();

        return response()->json([
            'bot_reply' => [
                'metadata' => [
                    'questions' => $questions
                ]
            ]
        ]);
    }

    public function delete(Request $request)
    {
        Message::where('id', $request->id)->delete();

        return response()->json(['success' => true]);
    }

    public function react(Request $request, Conversation $conversation, Message $message)
    {
        $emoji = $request->emoji;

        $metadata = $message->metadata ?? [];

        if (isset($metadata['reaction']) && $metadata['reaction'] === $emoji) {
            unset($metadata['reaction']);
        } else {
            $metadata['reaction'] = $emoji;
        }

        $message->metadata = $metadata;
        $message->save();

        return response()->json([
            'reaction' => $metadata['reaction'] ?? null
        ]);
    }

    public function unsend($conversationId, $messageId)
    {
        $message = Message::where('id', $messageId)
            ->where('conversation_id', $conversationId)
            ->firstOrFail();

        if ($message->sender_type !== 'user') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $message->delete();

        return response()->json(['success' => true]);
    }

    public function send(Request $request, Conversation $conversation)
    {
        $request->validate([
            'content' => 'nullable|string|max:1000',
            'image'   => 'nullable|image|max:5120',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('chat-images', 'public');
        }

        $content = trim($request->content ?? '');

        if (!$content && !$imagePath) {
            return response()->json(['error' => 'Pesan kosong'], 422);
        }

        $userMessage = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id'       => auth()->id(),
            'sender_type'     => 'user',
            'content'         => $content,
            'message_type'    => $imagePath ? 'image' : 'text',
            'image_path'      => $imagePath,
        ]);

        $conversation->update(['last_message_at' => now()]);
        broadcast(new MessageSent($userMessage))->toOthers();

        if ($conversation->isBot() && $content) {
            $botReply = $this->bot->reply($conversation, $content);
            return response()->json([
                'user_message' => $userMessage,
                'bot_reply'    => $botReply,
            ]);
        }

        return response()->json(['user_message' => $userMessage]);
    }

    public function quickReply(Request $request, Conversation $conversation)
    {
        $option = $request->option;

        if (in_array($option, ['Hubungi Admin', 'Pertanyaan Lain', 'Lainnya'])) {
            $conversation->update(['status' => 'waiting']);

            Message::create([
                'conversation_id' => $conversation->id,
                'sender_id'       => null,
                'sender_type'     => 'bot',
                'content'         => 'Menghubungkan kamu dengan tim kami... Mohon tunggu sebentar 🙏',
                'message_type'    => 'system',
            ]);

            return response()->json(['escalated' => true]);
        }

        return $this->send(new Request(['content' => $option]), $conversation);
    }

    public function resolve(Conversation $conversation)
    {
        $conversation->update(['status' => 'resolved']);

        Message::create([
            'conversation_id' => $conversation->id,
            'sender_id'       => null,
            'sender_type'     => 'bot',
            'content'         => 'Percakapan telah diselesaikan. Terima kasih sudah menghubungi kami! 😊',
            'message_type'    => 'system',
        ]);

        return response()->json(['success' => true]);
    }

    public function index()
    {
        $user = auth()->user();
        
        $conversation = Conversation::where('user_id', $user->id)
            ->whereIn('status', ['bot', 'waiting', 'active'])
            ->latest()
            ->first();

        if (!$conversation) {
            $conversation = Conversation::create([
                'user_id'         => $user->id,
                'status'          => 'bot',
                'last_message_at' => now(),
            ]);
            $this->bot->getWelcomeMessage($conversation);
        }

        if ($conversation->messages()->count() === 0) {
            $this->bot->getWelcomeMessage($conversation);
        }

        $messages = $conversation->messages()->get();

        return view('pusat-bantuan', compact('conversation', 'messages'));
    }
}