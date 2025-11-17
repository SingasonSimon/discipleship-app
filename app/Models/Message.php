<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Message extends Model
{
    use HasFactory;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'message_type',
        'template',
        'channel',
        'scheduled_at',
        'status',
        'payload',
        'sent_at',
        'metadata',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'scheduled_at' => 'datetime',
        'sent_at' => 'datetime',
        'payload' => 'array',
        'metadata' => 'array',
    ];

    /**
     * Get all logs for this message
     */
    public function logs(): HasMany
    {
        return $this->hasMany(MessageLog::class);
    }

    /**
     * Check if message is scheduled
     */
    public function isScheduled(): bool
    {
        return $this->status === 'scheduled';
    }

    /**
     * Check if message is sent
     */
    public function isSent(): bool
    {
        return $this->status === 'sent';
    }

    /**
     * Check if message is draft
     */
    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    /**
     * Check if message failed
     */
    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    /**
     * Check if message is due to be sent
     */
    public function isDue(): bool
    {
        return $this->isScheduled() && $this->scheduled_at <= now();
    }

    /**
     * Get success rate for this message
     */
    public function getSuccessRate(): float
    {
        $totalLogs = $this->logs()->count();
        if ($totalLogs === 0) {
            return 0.0;
        }

        $successfulLogs = $this->logs()
            ->where('result', 'success')
            ->count();

        return round(($successfulLogs / $totalLogs) * 100, 2);
    }

    /**
     * Process template with variables
     */
    public function processTemplate(array $variables = []): string
    {
        $template = $this->template;

        foreach ($variables as $key => $value) {
            $template = str_replace("{{{$key}}}", $value, $template);
        }

        return $template;
    }
}
