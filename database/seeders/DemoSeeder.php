<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\ClassSession;
use App\Models\DiscipleshipClass;
use App\Models\Member;
use App\Models\Mentorship;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Creating demo users...');

        $defaultPassword = env('DEFAULT_SHARED_PASSWORD', 'password');
        
        // Get or create admin user (may already exist from migration)
        $admin = User::firstOrCreate(
            ['email' => env('DEFAULT_ADMIN_EMAIL', 'admin@discipleship.local')],
            [
                'name' => env('DEFAULT_ADMIN_NAME', 'Admin User'),
                'role' => User::ROLE_ADMIN,
                'email_verified_at' => now(),
                'password' => Hash::make($defaultPassword),
            ]
        );

        // Create pastor user (use firstOrCreate to avoid duplicates)
        $pastor1 = User::firstOrCreate(
            ['email' => env('DEFAULT_PASTOR_EMAIL', 'pastor@discipleship.local')],
            [
                'name' => env('DEFAULT_PASTOR_NAME', 'Pastor John Smith'),
                'role' => User::ROLE_PASTOR,
                'email_verified_at' => now(),
                'password' => Hash::make($defaultPassword),
            ]
        );


        $this->command->info('Creating demo members...');

        // Create members with different conversion dates
        $members = collect();

        // Recent converts (last 3 months)
        for ($i = 0; $i < 10; $i++) {
            $member = Member::create([
                'full_name' => fake()->name(),
                'phone' => '071' . fake()->numberBetween(1000000, 9999999),
                'email' => fake()->optional(0.8)->safeEmail(),
                'date_of_conversion' => fake()->dateTimeBetween('-3 months', '-1 month')->format('Y-m-d'),
                'preferred_contact' => fake()->randomElement(['sms', 'email', 'call']),
                'notes' => fake()->optional(0.7)->randomElement([
                    'New member, needs follow-up',
                    'Active in church activities',
                    'Interested in discipleship program',
                    'Regular attendee, committed member',
                    'Needs prayer support',
                    'Recently converted, enthusiastic',
                    'Looking for mentorship opportunity',
                    'Engaged in Bible study',
                    'Serving in ministry',
                    'Growing in faith',
                ]),
            ]);
            $members->push($member);
        }

        // Established members (6 months to 2 years)
        for ($i = 0; $i < 15; $i++) {
            $member = Member::create([
                'full_name' => fake()->name(),
                'phone' => '072' . fake()->numberBetween(1000000, 9999999),
                'email' => fake()->optional(0.8)->safeEmail(),
                'date_of_conversion' => fake()->dateTimeBetween('-2 years', '-6 months')->format('Y-m-d'),
                'preferred_contact' => fake()->randomElement(['sms', 'email', 'call']),
                'notes' => fake()->optional(0.7)->randomElement([
                    'New member, needs follow-up',
                    'Active in church activities',
                    'Interested in discipleship program',
                    'Regular attendee, committed member',
                    'Needs prayer support',
                    'Recently converted, enthusiastic',
                    'Looking for mentorship opportunity',
                    'Engaged in Bible study',
                    'Serving in ministry',
                    'Growing in faith',
                ]),
            ]);
            $members->push($member);
        }

        // Long-term members (2+ years)
        for ($i = 0; $i < 8; $i++) {
            $member = Member::create([
                'full_name' => fake()->name(),
                'phone' => '073' . fake()->numberBetween(1000000, 9999999),
                'email' => fake()->optional(0.8)->safeEmail(),
                'date_of_conversion' => fake()->dateTimeBetween('-5 years', '-2 years')->format('Y-m-d'),
                'preferred_contact' => fake()->randomElement(['sms', 'email', 'call']),
                'notes' => fake()->optional(0.7)->randomElement([
                    'New member, needs follow-up',
                    'Active in church activities',
                    'Interested in discipleship program',
                    'Regular attendee, committed member',
                    'Needs prayer support',
                    'Recently converted, enthusiastic',
                    'Looking for mentorship opportunity',
                    'Engaged in Bible study',
                    'Serving in ministry',
                    'Growing in faith',
                ]),
            ]);
            $members->push($member);
        }

        $this->command->info('Creating demo classes...');

        // Create discipleship classes
        $classes = collect();

        // New Believers Class (8 weeks, weekly)
        $newBelieversClass = DiscipleshipClass::create([
            'title' => 'New Believers Class',
            'description' => 'Introduction to basic Christian beliefs and practices for new converts. Covers salvation, baptism, prayer, Bible reading, and church involvement.',
            'mentor_id' => $pastor1->id,
            'schedule_type' => 'weekly',
            'schedule_day' => 'sunday',
            'schedule_time' => '10:00',
            'start_date' => Carbon::now()->subWeeks(4),
            'end_date' => Carbon::now()->addWeeks(4),
            'capacity' => 25,
            'duration_weeks' => 8,
            'location' => 'Main Hall',
            'is_active' => true,
        ]);
        $classes->push($newBelieversClass);

        // Biblical Foundations (12 weeks)
        $biblicalFoundations = DiscipleshipClass::create([
            'title' => 'Biblical Foundations',
            'description' => 'Deep dive into essential biblical teachings including creation, sin, redemption, and the character of God.',
            'mentor_id' => $pastor1->id,
            'schedule_type' => 'weekly',
            'schedule_day' => 'wednesday',
            'schedule_time' => '19:00',
            'start_date' => Carbon::now()->subWeeks(6),
            'end_date' => Carbon::now()->addWeeks(6),
            'capacity' => 20,
            'duration_weeks' => 12,
            'location' => 'Conference Room',
            'is_active' => true,
        ]);
        $classes->push($biblicalFoundations);

        // Christian Living (10 weeks)
        $christianLiving = DiscipleshipClass::create([
            'title' => 'Christian Living',
            'description' => 'Practical guidance for living as a Christian in today\'s world. Topics include relationships, work, stewardship, and spiritual disciplines.',
            'mentor_id' => $pastor1->id,
            'schedule_type' => 'weekly',
            'schedule_day' => 'thursday',
            'schedule_time' => '18:30',
            'start_date' => Carbon::now()->subWeeks(5),
            'end_date' => Carbon::now()->addWeeks(5),
            'capacity' => 30,
            'duration_weeks' => 10,
            'location' => 'Fellowship Hall',
            'is_active' => true,
        ]);
        $classes->push($christianLiving);

        // Prayer and Fasting (6 weeks)
        $prayerFasting = DiscipleshipClass::create([
            'title' => 'Prayer and Fasting',
            'description' => 'Understanding the power of prayer and fasting in the Christian life. Practical guidance on developing a deeper prayer life.',
            'mentor_id' => $pastor1->id,
            'schedule_type' => 'biweekly',
            'schedule_day' => 'saturday',
            'schedule_time' => '09:00',
            'start_date' => Carbon::now()->subWeeks(3),
            'end_date' => Carbon::now()->addWeeks(3),
            'capacity' => 15,
            'duration_weeks' => 6,
            'location' => 'Prayer Room',
            'is_active' => true,
        ]);
        $classes->push($prayerFasting);

        $this->command->info('Generating class sessions...');

        // Generate sessions for each class (simulate past and upcoming sessions)
        foreach ($classes as $class) {
            $this->generateClassSessions($class);
        }

        $this->command->info('Creating attendance records...');

        // Create attendance records for class sessions
        foreach ($classes as $class) {
            $this->createAttendanceForClass($class, $members);
        }

        $this->command->info('Creating mentorship relationships...');

        // Get mentor user (only users with 'mentor' role can be mentors)
        $mentorUser = User::where('role', User::ROLE_MENTOR)->first();
        
        if ($mentorUser) {
            // Create mentorship relationships (only using actual mentors, not pastors)
            $this->createMentorships($members, [$mentorUser]);
        } else {
            $this->command->warn('No mentor user found. Skipping mentorship creation.');
        }

        $this->command->info('Demo data created successfully!');
        $this->command->info('Login credentials:');
        $this->command->info('Admin: admin@discipleship.local / password');
        $this->command->info('Pastor: pastor@discipleship.local / password');
        $this->command->info('Mentor: mentor@discipleship.local / password');
        $this->command->info('Member: member@discipleship.local / password');
    }

    /**
     * Generate sessions for a class
     */
    private function generateClassSessions(DiscipleshipClass $class): void
    {
        $startDate = Carbon::parse($class->start_date);
        $endDate = Carbon::parse($class->end_date);
        $sessionNumber = 1;

        // Generate sessions based on schedule type
        if ($class->schedule_type === 'weekly') {
            $interval = 1; // weeks
        } elseif ($class->schedule_type === 'biweekly') {
            $interval = 2; // weeks
        } else {
            $interval = 1; // default to weekly
        }

        for ($date = $startDate; $date <= $endDate; $date->addWeeks($interval)) {
            // Check if the day matches the schedule
            $dayMatches = false;
            switch ($class->schedule_day) {
                case 'sunday':
                    $dayMatches = $date->dayOfWeek === Carbon::SUNDAY;

                    break;
                case 'monday':
                    $dayMatches = $date->dayOfWeek === Carbon::MONDAY;

                    break;
                case 'tuesday':
                    $dayMatches = $date->dayOfWeek === Carbon::TUESDAY;

                    break;
                case 'wednesday':
                    $dayMatches = $date->dayOfWeek === Carbon::WEDNESDAY;

                    break;
                case 'thursday':
                    $dayMatches = $date->dayOfWeek === Carbon::THURSDAY;

                    break;
                case 'friday':
                    $dayMatches = $date->dayOfWeek === Carbon::FRIDAY;

                    break;
                case 'saturday':
                    $dayMatches = $date->dayOfWeek === Carbon::SATURDAY;

                    break;
            }

            if ($dayMatches) {
                $isPast = $date < Carbon::today();

                ClassSession::create([
                    'class_id' => $class->id,
                    'session_date' => $date->toDateString(),
                    'topic' => $this->getSessionTopic($class->title, $sessionNumber),
                    'notes' => $isPast ? fake()->optional(0.6)->randomElement([
                        'Great discussion on the topic',
                        'Members engaged well',
                        'Good attendance and participation',
                        'Covered key points effectively',
                        'Questions answered satisfactorily',
                        'Session went smoothly',
                        'Members showed good understanding',
                    ]) : null,
                    'location' => $class->location,
                    'created_by' => $class->mentor_id,
                ]);

                $sessionNumber++;
            }
        }
    }

    /**
     * Get session topic based on class title
     */
    private function getSessionTopic(string $classTitle, int $sessionNumber): string
    {
        $topics = [
            'New Believers Class' => [
                'Introduction to Salvation',
                'Understanding Baptism',
                'The Power of Prayer',
                'Reading the Bible',
                'Fellowship with Believers',
                'Sharing Your Faith',
                'Growing in Christ',
                'Next Steps in Discipleship',
            ],
            'Biblical Foundations' => [
                'The Nature of God',
                'Creation and Fall',
                'The Promise of Redemption',
                'The Life of Jesus',
                'The Work of the Holy Spirit',
                'The Church and Community',
                'End Times and Eternity',
                'Living by Faith',
                'The Authority of Scripture',
                'Christian Ethics',
                'Worship and Service',
                'Spiritual Warfare',
            ],
            'Christian Living' => [
                'Personal Devotions',
                'Managing Finances God\'s Way',
                'Relationships and Marriage',
                'Parenting with Purpose',
                'Work as Ministry',
                'Dealing with Temptation',
                'Forgiveness and Reconciliation',
                'Serving Others',
                'Time Management',
                'Health and Wellness',
            ],
            'Prayer and Fasting' => [
                'The Purpose of Prayer',
                'Types of Prayer',
                'Praying with Faith',
                'Fasting: Biblical Foundation',
                'Practical Fasting Guidelines',
                'Prayer and Fasting Together',
            ],
        ];

        $classTopics = $topics[$classTitle] ?? ['General Discussion'];
        $topicIndex = ($sessionNumber - 1) % count($classTopics);

        return "Session {$sessionNumber}: {$classTopics[$topicIndex]}";
    }

    /**
     * Create attendance records for a class
     */
    private function createAttendanceForClass(DiscipleshipClass $class, $allMembers): void
    {
        $sessions = $class->sessions;

        foreach ($sessions as $session) {
            // Only create attendance for past sessions
            if (Carbon::parse($session->session_date)->isPast()) {
                // Randomly select some members to attend this session
                $attendingMembers = $allMembers->random(min(rand(5, 15), $allMembers->count()));

                foreach ($attendingMembers as $member) {
                    $status = fake()->randomElement(['present', 'absent', 'excused']);

                    Attendance::create([
                        'class_session_id' => $session->id,
                        'member_id' => $member->id,
                        'status' => $status,
                        'marked_by' => $class->mentor_id,
                        'marked_at' => Carbon::parse($session->session_date)->setTimeFromTimeString(fake()->time('H:i:s')),
                    ]);
                }
            }
        }
    }

    /**
     * Create mentorship relationships
     */
    private function createMentorships($members, $mentors): void
    {
        $membersForMentorship = $members->random(min(20, $members->count()));

        foreach ($membersForMentorship as $member) {
            $mentor = fake()->randomElement($mentors);

            // Only create if no active mentorship exists
            $existingMentorship = Mentorship::where('member_id', $member->id)
                ->where('status', 'active')
                ->first();

            if (! $existingMentorship) {
                $startDate = fake()->dateTimeBetween('-1 year', 'now');
                $status = fake()->randomElement(['active', 'completed']);

                Mentorship::create([
                    'member_id' => $member->id,
                    'mentor_id' => $mentor->id,
                    'start_date' => $startDate->format('Y-m-d'),
                    'end_date' => $status === 'completed' ? fake()->dateTimeBetween($startDate, 'now')->format('Y-m-d') : null,
                    'status' => $status,
                    'meeting_frequency' => fake()->randomElement(['weekly', 'biweekly', 'monthly']),
                    'notes' => fake()->optional(0.8)->randomElement([
                        'Regular mentorship meetings scheduled',
                        'Member showing good progress',
                        'Focusing on spiritual growth',
                        'Building strong relationship',
                        'Addressing specific spiritual needs',
                        'Encouraging member in faith journey',
                        'Providing guidance and support',
                        'Member responding well to mentorship',
                    ]),
                    'completed_at' => $status === 'completed' ? fake()->dateTimeBetween($startDate, 'now') : null,
                ]);
            }
        }
    }
}
