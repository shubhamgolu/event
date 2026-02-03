<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Event;
use App\Models\Survey;
use App\Models\Participant;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class EventSystemSeeder extends Seeder
{
    public function run(): void
    {
        // Get or create admin user
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'System Administrator',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );

        // Get or create regular user
        $user = User::firstOrCreate(
            ['email' => 'user@example.com'],
            [
                'name' => 'John Doe',
                'password' => Hash::make('user123'),
                'role' => 'user',
                'email_verified_at' => now(),
            ]
        );

        // Create some events
        $events = [
            [
                'name' => 'Laravel Conference 2024',
                'description' => 'Annual Laravel developers conference with workshops and networking.',
                'date' => Carbon::now()->addDays(30),
                'time' => '09:00:00',
                'type' => 'online',
                'meeting_link' => 'https://meet.google.com/laravel-2024',
                'price' => 0,
                'capacity' => 100,
                'is_active' => true,
                'user_id' => $admin->id,
            ],
            [
                'name' => 'Web Development Workshop',
                'description' => 'Hands-on workshop for modern web development techniques.',
                'date' => Carbon::now()->addDays(15),
                'time' => '14:00:00',
                'type' => 'offline',
                'location' => 'Tech Hub, Downtown',
                'price' => 50.00,
                'capacity' => 50,
                'is_active' => true,
                'user_id' => $admin->id,
            ],
            [
                'name' => 'AI & Machine Learning Seminar',
                'description' => 'Introduction to AI and ML concepts for beginners.',
                'date' => Carbon::now()->addDays(45),
                'time' => '10:00:00',
                'type' => 'online',
                'meeting_link' => 'https://meet.google.com/ai-seminar',
                'price' => 25.00,
                'capacity' => 200,
                'is_active' => true,
                'user_id' => $admin->id,
            ],
        ];

        foreach ($events as $eventData) {
            $event = Event::firstOrCreate(
                ['name' => $eventData['name']],
                $eventData
            );

            // Create survey for event
            $survey = Survey::firstOrCreate(
                ['event_id' => $event->id],
                [
                    'title' => $event->name . ' Feedback Survey',
                    'description' => 'Please provide your feedback for ' . $event->name,
                    'questions' => json_encode([
                        [
                            'type' => 'multiple_choice',
                            'question' => 'How would you rate the event?',
                            'options' => ['Excellent', 'Good', 'Average', 'Poor'],
                            'required' => true,
                        ],
                        [
                            'type' => 'text',
                            'question' => 'What did you like most about the event?',
                            'required' => false,
                        ],
                        [
                            'type' => 'multiple_choice',
                            'question' => 'Will you attend future events?',
                            'options' => ['Yes', 'No', 'Maybe'],
                            'required' => true,
                        ],
                        [
                            'type' => 'text',
                            'question' => 'Any suggestions for improvement?',
                            'required' => false,
                        ],
                    ]),
                    'is_active' => true,
                    'send_on_checkin' => true,
                    'send_on_checkout' => false,
                ]
            );

            // Register user for first event
            if ($event->name === 'Laravel Conference 2024') {
                Participant::firstOrCreate(
                    [
                        'user_id' => $user->id,
                        'event_id' => $event->id,
                    ],
                    [
                        'registration_number' => 'REG-' . strtoupper(uniqid()),
                        'checked_in' => true,
                        'checked_in_at' => Carbon::now()->subHours(2),
                        'survey_sent' => true,
                    ]
                );
            }
        }

        // Create more test users
        User::factory(8)->create(['role' => 'user']);

        $this->command->info('✅ Database seeded successfully!');
        $this->command->info('Admin: admin@example.com / admin123');
        $this->command->info('User: user@example.com / user123');
    }
}