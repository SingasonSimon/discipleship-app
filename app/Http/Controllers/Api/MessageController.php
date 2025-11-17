<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Services\MessageService;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    protected MessageService $messageService;

    public function __construct(MessageService $messageService)
    {
        $this->messageService = $messageService;
    }

    /**
     * Display a listing of messages
     */
    public function index(Request $request)
    {
        $messages = Message::orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $messages,
        ]);
    }

    /**
     * Store a newly created message
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'message_type' => ['required', 'string', 'in:welcome,class_reminder,mentorship_assigned,general'],
            'channel' => ['required', 'string', 'in:email'],
            'subject' => ['nullable', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'recipients' => ['required', 'array', 'min:1'],
            'recipients.*' => ['required', 'string'],
            'schedule_type' => ['required', 'string', 'in:immediate,scheduled'],
            'scheduled_at' => ['nullable', 'required_if:schedule_type,scheduled', 'date', 'after:now'],
        ]);

        $message = Message::create([
            'message_type' => $validated['message_type'],
            'channel' => $validated['channel'],
            'template' => $validated['content'],
            'status' => $validated['schedule_type'] === 'immediate' ? 'draft' : 'scheduled',
            'scheduled_at' => $validated['schedule_type'] === 'scheduled' && isset($validated['scheduled_at']) 
                ? $validated['scheduled_at'] 
                : null,
            'payload' => [
                'subject' => $validated['subject'] ?? null,
                'recipients' => $validated['recipients'],
            ],
            'metadata' => [
                'created_by' => auth()->id(),
            ],
        ]);

        return response()->json([
            'success' => true,
            'data' => $message,
        ], 201);
    }

    /**
     * Display the specified message
     */
    public function show(Message $message)
    {
        $message->load('logs');

        return response()->json([
            'success' => true,
            'data' => $message,
        ]);
    }

    /**
     * Update the specified message
     */
    public function update(Request $request, Message $message)
    {
        $validated = $request->validate([
            'message_type' => ['required', 'string', 'in:welcome,class_reminder,mentorship_assigned,general,custom'],
            'channel' => ['required', 'string', 'in:email'],
            'subject' => ['nullable', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'recipients' => ['required', 'array', 'min:1'],
            'recipients.*' => ['required', 'string'],
            'schedule_type' => ['required', 'string', 'in:immediate,scheduled'],
            'scheduled_at' => ['nullable', 'required_if:schedule_type,scheduled', 'date', 'after:now'],
        ]);

        $message->update([
            'message_type' => $validated['message_type'],
            'channel' => $validated['channel'],
            'template' => $validated['content'],
            'status' => $validated['schedule_type'] === 'immediate' ? 'draft' : 'scheduled',
            'scheduled_at' => $validated['schedule_type'] === 'scheduled' && isset($validated['scheduled_at']) 
                ? $validated['scheduled_at'] 
                : null,
            'payload' => [
                'subject' => $validated['subject'] ?? null,
                'recipients' => $validated['recipients'],
            ],
        ]);

        return response()->json([
            'success' => true,
            'data' => $message,
        ]);
    }

    /**
     * Remove the specified message
     */
    public function destroy(Message $message)
    {
        $message->delete();

        return response()->json([
            'success' => true,
            'message' => 'Message deleted successfully',
        ]);
    }

    /**
     * Send a draft message immediately
     */
    public function sendNow(Message $message)
    {
        if ($message->status !== 'draft') {
            return response()->json([
                'success' => false,
                'message' => 'Only draft messages can be sent immediately.',
            ], 400);
        }

        $results = $this->messageService->sendMessage($message);

        return response()->json([
            'success' => $results['success'] > 0,
            'data' => $results,
        ]);
    }
}

