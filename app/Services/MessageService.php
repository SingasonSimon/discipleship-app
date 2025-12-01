<?php

namespace App\Services;

use App\Models\DiscipleshipClass;
use App\Models\Member;
use App\Models\Message;
use App\Models\MessageLog;
use App\Models\Mentorship;
use App\Notifications\CustomMessageNotification;
use Illuminate\Support\Facades\Notification;

class MessageService
{

    /**
     * Send a message to its recipients
     */
    public function sendMessage(Message $message): array
    {
        $results = [
            'success' => 0,
            'failed' => 0,
            'errors' => [],
        ];

        $recipients = $message->payload['recipients'] ?? [];
        $subject = $message->payload['subject'] ?? 'Message from Discipleship System';
        $content = $message->template;

        // Get actual recipients based on message type
        $actualRecipients = $this->getRecipients($recipients, $message);

        foreach ($actualRecipients as $recipient) {
            try {
                if ($message->channel === 'email') {
                    $this->sendEmail($message, $recipient, $subject, $content, $results);
                } else {
                    // Unknown channel
                    MessageLog::create([
                        'message_id' => $message->id,
                        'recipient' => $recipient->email ?? 'unknown',
                        'channel' => $message->channel,
                        'result' => 'failed',
                        'response' => ['error' => "Unknown channel: {$message->channel}"],
                        'created_at' => now(),
                    ]);

                    $results['failed']++;
                    $results['errors'][] = "Unknown channel for {$recipient->full_name}";
                }
            } catch (\Exception $e) {
                // Log failure
                MessageLog::create([
                    'message_id' => $message->id,
                    'recipient' => $recipient->email ?? 'unknown',
                    'channel' => $message->channel,
                    'result' => 'failed',
                    'response' => ['error' => $e->getMessage()],
                    'created_at' => now(),
                ]);

                $results['failed']++;
                $results['errors'][] = "Error sending to {$recipient->full_name}: {$e->getMessage()}";
            }
        }

        // Update message status
        $message->update([
            'status' => $results['failed'] === 0 ? 'sent' : ($results['success'] > 0 ? 'sent' : 'failed'),
            'sent_at' => now(),
        ]);

        return $results;
    }

    /**
     * Send email message
     */
    protected function sendEmail(Message $message, Member $recipient, string $subject, string $content, array &$results): void
    {
        // Check if recipient has a user account with email
        $email = $recipient->user?->email ?? $recipient->email;
        
        if (!$email) {
            // Log failure - no email address
            MessageLog::create([
                'message_id' => $message->id,
                'recipient' => $recipient->email ?? 'unknown',
                'channel' => $message->channel,
                'result' => 'failed',
                'response' => ['error' => 'No email address available'],
                'created_at' => now(),
            ]);

            $results['failed']++;
            $results['errors'][] = "No email address for {$recipient->full_name}";
            return;
        }

        try {
            // If recipient has a user account, send via notification
            if ($recipient->user) {
                $recipient->user->notify(
                    new CustomMessageNotification($subject, $content)
                );
            } else {
                // Fallback: send directly via Mail facade if no user account
                \Illuminate\Support\Facades\Mail::raw($content, function ($mail) use ($email, $subject) {
                    $mail->to($email)
                        ->subject($subject ?: 'Message from Discipleship System');
                });
            }

            // Log success
            MessageLog::create([
                'message_id' => $message->id,
                'recipient' => $email,
                'channel' => $message->channel,
                'result' => 'success',
                'response' => ['sent' => true, 'sent_at' => now()->toIso8601String()],
                'created_at' => now(),
            ]);

            $results['success']++;
        } catch (\Exception $e) {
            // Log failure with exception details
            MessageLog::create([
                'message_id' => $message->id,
                'recipient' => $email,
                'channel' => $message->channel,
                'result' => 'failed',
                'response' => ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()],
                'created_at' => now(),
            ]);

            $results['failed']++;
            $results['errors'][] = "Failed to send email to {$recipient->full_name}: {$e->getMessage()}";
        }
    }

    /**
     * Get actual recipients based on message recipient types
     * Includes members with email addresses (either via user account or direct email)
     */
    protected function getRecipients(array $recipientTypes, Message $message): \Illuminate\Support\Collection
    {
        $recipients = collect();

        foreach ($recipientTypes as $type) {
            switch ($type) {
                case 'all_members':
                    // Get all members who have an email (either via user account or direct email field)
                    $recipients = $recipients->merge(
                        Member::where(function ($query) {
                            $query->whereHas('user')
                                ->orWhereNotNull('email')
                                ->where('email', '!=', '');
                        })
                        ->with('user')
                        ->get()
                    );
                    break;

                case 'class_members':
                    // Get members from all active classes who have email
                    $classIds = DiscipleshipClass::where('is_active', true)->pluck('id');
                    $recipients = $recipients->merge(
                        Member::whereHas('enrollments', function ($query) use ($classIds) {
                            $query->whereIn('class_id', $classIds)
                                ->where('status', 'approved');
                        })
                        ->where(function ($query) {
                            $query->whereHas('user')
                                ->orWhereNotNull('email')
                                ->where('email', '!=', '');
                        })
                        ->with('user')
                        ->get()
                    );
                    break;

                case 'mentorship_pairs':
                    // Get members in active mentorships who have email
                    $memberIds = Mentorship::where('status', 'active')
                        ->pluck('member_id')
                        ->unique();
                    $recipients = $recipients->merge(
                        Member::whereIn('id', $memberIds)
                            ->where(function ($query) {
                                $query->whereHas('user')
                                    ->orWhereNotNull('email')
                                    ->where('email', '!=', '');
                            })
                            ->with('user')
                            ->get()
                    );
                    break;
            }
        }

        // Remove duplicates
        return $recipients->unique('id');
    }
}

