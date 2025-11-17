<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Services\MessageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MessageController extends Controller
{
    protected MessageService $messageService;

    public function __construct(MessageService $messageService)
    {
        $this->middleware('auth');
        $this->messageService = $messageService;
    }

    /**
     * Display a listing of messages.
     */
    public function index(): View
    {
        $messages = Message::orderBy('created_at', 'desc')
            ->paginate(20);

        return view('messages.index', compact('messages'));
    }

    /**
     * Show the form for creating a new message.
     */
    public function create(): View
    {
        return view('messages.create');
    }

    /**
     * Store a newly created message.
     */
    public function store(Request $request): RedirectResponse
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

        return redirect()->route('messages.index')
            ->with('success', 'Message created successfully.');
    }

    /**
     * Display the specified message.
     */
    public function show(Message $message): View
    {
        $message->load('logs');
        
        return view('messages.show', compact('message'));
    }

    /**
     * Show the form for editing the specified message.
     */
    public function edit(Message $message): View
    {
        return view('messages.edit', compact('message'));
    }

    /**
     * Update the specified message.
     */
    public function update(Request $request, Message $message): RedirectResponse
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

        return redirect()->route('messages.index')
            ->with('success', 'Message updated successfully.');
    }

    /**
     * Send a draft message immediately
     */
    public function sendNow(Message $message): RedirectResponse
    {
        if ($message->status !== 'draft') {
            return redirect()->back()
                ->with('error', 'Only draft messages can be sent immediately.');
        }

        $results = $this->messageService->sendMessage($message);

        if ($results['success'] > 0) {
            return redirect()->back()
                ->with('success', "Message sent successfully to {$results['success']} recipient(s).");
        } else {
            return redirect()->back()
                ->with('error', 'Failed to send message. ' . implode(', ', $results['errors']));
        }
    }

    /**
     * Send all scheduled messages that are due
     */
    public function sendScheduled(Request $request): RedirectResponse
    {
        $messages = Message::where('status', 'scheduled')
            ->whereNotNull('scheduled_at')
            ->where('scheduled_at', '<=', now())
            ->get();

        $totalSent = 0;
        $totalFailed = 0;
        $errors = [];

        foreach ($messages as $message) {
            try {
                $results = $this->messageService->sendMessage($message);
                $totalSent += $results['success'];
                $totalFailed += $results['failed'];
                $errors = array_merge($errors, $results['errors']);
            } catch (\Exception $e) {
                $totalFailed++;
                $errors[] = "Error processing message ID {$message->id}: {$e->getMessage()}";
            }
        }

        if ($totalSent > 0) {
            $message = "Sent {$totalSent} scheduled message(s) successfully.";
            if ($totalFailed > 0) {
                $message .= " {$totalFailed} failed.";
            }
            return redirect()->back()->with('success', $message);
        } else {
            return redirect()->back()
                ->with('error', 'No scheduled messages were sent. ' . implode(', ', array_slice($errors, 0, 5)));
        }
    }

    /**
     * Remove the specified message.
     */
    public function destroy(Message $message): RedirectResponse
    {
        $message->delete();

        return redirect()->route('messages.index')
            ->with('success', 'Message deleted successfully.');
    }
}
