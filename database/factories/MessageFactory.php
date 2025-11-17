<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Message>
 */
class MessageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'message_type' => $this->faker->randomElement(['welcome', 'class_reminder', 'mentorship_assigned', 'general', 'custom']),
            'channel' => $this->faker->randomElement(['email', 'sms']),
            'template' => $this->faker->sentence(),
            'scheduled_at' => null,
            'status' => $this->faker->randomElement(['draft', 'scheduled', 'sent', 'failed']),
            'payload' => [
                'subject' => $this->faker->sentence(),
                'recipients' => ['all_members'],
            ],
            'sent_at' => null,
            'metadata' => [
                'created_by' => 1,
            ],
        ];
    }
}
