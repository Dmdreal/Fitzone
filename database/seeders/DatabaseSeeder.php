<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\ClientChat;
use App\Models\ClientChatMessage;
use App\Models\DietPlan;
use App\Models\GymEquipment;
use App\Models\InventoryLog;
use App\Models\LoyaltyPoint;
use App\Models\Membership;
use App\Models\MembershipPackage;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Models\ProgressRecord;
use App\Models\Referral;
use App\Models\TrainerProfile;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WorkoutPlan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $users = [
            ['name' => 'Admin User', 'email' => 'admin@fitzone.test', 'role' => 'admin'],
            ['name' => 'Café Staff', 'email' => 'cafe@fitzone.test', 'role' => 'cafe'],
            ['name' => 'Rahul Trainer', 'email' => 'trainer@fitzone.test', 'role' => 'trainer'],
            ['name' => 'Sonia Wellness', 'email' => 'sonia@fitzone.test', 'role' => 'trainer'],
            ['name' => 'Amit Verma', 'email' => 'member@fitzone.test', 'role' => 'member'],
            ['name' => 'Priya Singh', 'email' => 'priya@fitzone.test', 'role' => 'member'],
            ['name' => 'Kevin Otieno', 'email' => 'kevin@fitzone.test', 'role' => 'member'],
            ['name' => 'Maya Njeri', 'email' => 'maya@fitzone.test', 'role' => 'member'],
        ];

        foreach ($users as $user) {
            $password = in_array($user['role'], ['admin', 'trainer', 'cafe'], true)
                ? '123456789'
                : 'password';

            $createdUser = User::updateOrCreate(
                ['email' => $user['email']],
                $user + ['status' => 'active', 'password' => $password]
            );
            $createdUser->ensureMemberIdentity();
        }

        $packages = [
            [
                'name' => 'Daily Pass',
                'slug' => 'daily-pass',
                'duration_unit' => 'day',
                'duration_count' => 1,
                'price' => 500,
                'access_level' => 'basic',
                'trainer_access' => false,
                'benefits' => ['Gym floor access', 'Locker access', 'Basic equipment'],
            ],
            [
                'name' => 'Weekly Plan',
                'slug' => 'weekly-plan',
                'duration_unit' => 'week',
                'duration_count' => 1,
                'price' => 2500,
                'access_level' => 'standard',
                'trainer_access' => true,
                'benefits' => ['Gym access', 'Cardio zone', 'One trainer check-in'],
            ],
            [
                'name' => 'Monthly Plan',
                'slug' => 'monthly-plan',
                'duration_unit' => 'month',
                'duration_count' => 1,
                'price' => 7500,
                'access_level' => 'premium',
                'trainer_access' => true,
                'benefits' => ['All equipment', 'Workout plans', 'Attendance tracking', 'Diet plan'],
            ],
            [
                'name' => 'Yearly Plan',
                'slug' => 'yearly-plan',
                'duration_unit' => 'year',
                'duration_count' => 1,
                'price' => 75000,
                'access_level' => 'elite',
                'trainer_access' => true,
                'benefits' => ['Unlimited access', 'Personal trainer', 'Nutrition program', 'Priority support'],
            ],
        ];

        MembershipPackage::where('slug', 'trial-prompt')->update(['is_active' => false]);

        foreach ($packages as $package) {
            MembershipPackage::updateOrCreate(['slug' => $package['slug']], $package);
        }

        $rahul = User::where('email', 'trainer@fitzone.test')->first();
        $sonia = User::where('email', 'sonia@fitzone.test')->first();
        $amit = User::where('email', 'member@fitzone.test')->first();
        $priya = User::where('email', 'priya@fitzone.test')->first();
        $kevin = User::where('email', 'kevin@fitzone.test')->first();
        $maya = User::where('email', 'maya@fitzone.test')->first();
        $cafeStaff = User::where('email', 'cafe@fitzone.test')->first();
        $weekly = MembershipPackage::where('slug', 'weekly-plan')->first();
        $monthly = MembershipPackage::where('slug', 'monthly-plan')->first();
        $yearly = MembershipPackage::where('slug', 'yearly-plan')->first();

        TrainerProfile::updateOrCreate(
            ['user_id' => $rahul->id],
            ['specialty' => 'Strength and hypertrophy', 'category' => 'strength', 'rating' => 4.80, 'experience_years' => 7, 'bio' => 'Builds practical programs for muscle gain and injury-safe lifting.']
        );

        TrainerProfile::updateOrCreate(
            ['user_id' => $sonia->id],
            ['specialty' => 'Yoga and wellness', 'category' => 'wellness', 'rating' => 4.90, 'experience_years' => 5, 'bio' => 'Focuses on mobility, recovery, weight loss, and sustainable habits.']
        );

        $membership = Membership::updateOrCreate(
            ['member_id' => $amit->id, 'membership_package_id' => $monthly->id],
            [
                'trainer_id' => $rahul->id,
                'starts_at' => now()->startOfMonth()->toDateString(),
                'ends_at' => now()->startOfMonth()->addMonth()->toDateString(),
                'status' => 'active',
                'activated_at' => now(),
            ]
        );

        Membership::updateOrCreate(
            ['member_id' => $priya->id, 'membership_package_id' => $yearly->id],
            [
                'trainer_id' => $sonia->id,
                'starts_at' => now()->subMonths(2)->toDateString(),
                'ends_at' => now()->addMonths(10)->toDateString(),
                'status' => 'active',
                'activated_at' => now()->subMonths(2),
            ]
        );

        foreach ([$kevin, $maya] as $index => $weeklyMember) {
            Membership::updateOrCreate(
                ['member_id' => $weeklyMember->id, 'membership_package_id' => $weekly->id],
                [
                    'trainer_id' => $index === 0 ? $rahul->id : $sonia->id,
                    'starts_at' => now()->subDays(2 + $index)->toDateString(),
                    'ends_at' => now()->addDays(5 - $index)->toDateString(),
                    'status' => 'active',
                    'activated_at' => now()->subDays(2 + $index),
                ]
            );
        }

        Payment::updateOrCreate(
            ['reference' => 'MPESA-FITZONE-001'],
            ['member_id' => $amit->id, 'membership_id' => $membership->id, 'amount' => 7500, 'method' => 'mpesa', 'status' => 'paid', 'paid_at' => now()->subDays(5), 'notes' => 'Monthly package activation']
        );

        Payment::updateOrCreate(
            ['reference' => 'CARD-FITZONE-002'],
            ['member_id' => $priya->id, 'membership_id' => null, 'amount' => 75000, 'method' => 'card', 'status' => 'paid', 'paid_at' => now()->subMonths(2), 'notes' => 'Yearly package activation']
        );

        foreach (range(1, 10) as $day) {
            Attendance::updateOrCreate(
                ['member_id' => $amit->id, 'attendance_date' => now()->subDays($day)->toDateString()],
                [
                    'marked_by' => $rahul->id,
                    'check_in_at' => '07:00:00',
                    'check_out_at' => '08:20:00',
                    'status' => in_array($day, [3, 8], true) ? 'absent' : 'present',
                    'qr_code' => 'FITZONE-'.$amit->id.'-'.$day,
                ]
            );
        }

        $workoutPlan = WorkoutPlan::updateOrCreate(
            ['member_id' => $amit->id, 'title' => 'Chest and Shoulder Program'],
            ['trainer_id' => $rahul->id, 'focus_area' => 'Strength', 'notes' => 'Controlled reps and progressive overload.', 'starts_at' => now()->toDateString(), 'ends_at' => now()->addWeeks(4)->toDateString(), 'is_active' => true]
        );

        $exercises = [
            ['exercise_name' => 'Bench Press', 'sets' => 4, 'reps' => 12, 'instructions' => 'Keep shoulder blades tight.', 'trainer_notes' => 'Avoid locking elbows.', 'sort_order' => 1],
            ['exercise_name' => 'Incline Dumbbell Press', 'sets' => 4, 'reps' => 12, 'instructions' => 'Use a medium incline.', 'trainer_notes' => 'Slow tempo.', 'sort_order' => 2],
            ['exercise_name' => 'Push Ups', 'sets' => 3, 'reps' => 15, 'instructions' => 'Full range of motion.', 'trainer_notes' => 'Brace core.', 'sort_order' => 3],
        ];

        foreach ($exercises as $exercise) {
            $workoutPlan->exercises()->updateOrCreate(
                ['exercise_name' => $exercise['exercise_name']],
                $exercise
            );
        }

        DietPlan::updateOrCreate(
            ['member_id' => $amit->id, 'name' => 'Muscle Gain Meal Plan'],
            [
                'membership_package_id' => $monthly->id,
                'goal' => 'muscle_gain',
                'daily_calories' => 2800,
                'meal_schedule' => [
                    'breakfast' => 'Oats, eggs, banana',
                    'lunch' => 'Chicken, rice, vegetables',
                    'snack' => 'Greek yogurt and nuts',
                    'dinner' => 'Fish, sweet potato, greens',
                ],
                'meal_delivery_available' => true,
                'is_active' => true,
            ]
        );

        ProgressRecord::updateOrCreate(
            ['member_id' => $amit->id, 'recorded_at' => now()->toDateString()],
            ['trainer_id' => $rahul->id, 'weight_kg' => 78.40, 'body_fat_percentage' => 17.50, 'notes' => 'Improved pressing form and consistency.']
        );

        foreach ([
            ['name' => 'Treadmill 01', 'category' => 'cardio', 'serial_number' => 'CARDIO-TM-001', 'status' => 'available'],
            ['name' => 'Smith Machine', 'category' => 'strength', 'serial_number' => 'STR-SM-001', 'status' => 'maintenance'],
        ] as $equipment) {
            GymEquipment::updateOrCreate(['serial_number' => $equipment['serial_number']], $equipment);
        }

        Referral::updateOrCreate(
            ['code' => 'AMIT100'],
            ['referrer_id' => $amit->id, 'referred_user_id' => $priya->id, 'status' => 'converted', 'reward_points' => 100]
        );

        LoyaltyPoint::updateOrCreate(
            ['member_id' => $amit->id, 'source' => 'referral'],
            ['points' => 100, 'description' => 'Referral reward for inviting Priya Singh.']
        );

        foreach ([$amit, $priya, $kevin, $maya] as $walletMember) {
            Wallet::updateOrCreate(
                ['member_id' => $walletMember->id],
                ['balance' => $walletMember->is($amit) ? 3500 : 1500]
            );
        }

        $products = [
            ['name' => 'Protein Shake', 'category' => 'drink', 'price' => 450, 'stock_quantity' => 30, 'low_stock_threshold' => 8],
            ['name' => 'Electrolyte Water', 'category' => 'drink', 'price' => 180, 'stock_quantity' => 40, 'low_stock_threshold' => 10],
            ['name' => 'Chicken Wrap', 'category' => 'food', 'price' => 650, 'stock_quantity' => 14, 'low_stock_threshold' => 5],
            ['name' => 'Fruit Bowl', 'category' => 'food', 'price' => 350, 'stock_quantity' => 5, 'low_stock_threshold' => 5],
        ];

        foreach ($products as $productData) {
            $product = Product::updateOrCreate(['name' => $productData['name']], $productData + ['is_active' => true]);
            InventoryLog::firstOrCreate(
                ['product_id' => $product->id, 'type' => 'restock', 'notes' => 'Opening stock'],
                ['user_id' => $cafeStaff->id, 'quantity_change' => $product->stock_quantity, 'stock_after' => $product->stock_quantity]
            );
        }

        $shake = Product::where('name', 'Protein Shake')->first();
        $water = Product::where('name', 'Electrolyte Water')->first();
        $sampleOrder = Order::firstOrCreate(
            ['order_number' => 'ORD-SAMPLE-001'],
            ['member_id' => $amit->id, 'handled_by' => $cafeStaff->id, 'status' => 'completed', 'total_amount' => 630, 'paid_at' => now()]
        );

        $sampleOrder->items()->firstOrCreate(
            ['product_id' => $shake->id],
            ['quantity' => 1, 'unit_price' => 450, 'line_total' => 450]
        );

        $sampleOrder->items()->firstOrCreate(
            ['product_id' => $water->id],
            ['quantity' => 1, 'unit_price' => 180, 'line_total' => 180]
        );

        $monthlyChat = ClientChat::firstOrCreate(
            ['type' => 'package_group', 'membership_package_id' => $monthly->id],
            ['title' => 'Monthly Plan Members Chat']
        );

        $weeklyChat = ClientChat::firstOrCreate(
            ['type' => 'package_group', 'membership_package_id' => $weekly->id],
            ['title' => 'Weekly Plan Members Chat']
        );

        $yearlyChat = ClientChat::firstOrCreate(
            ['type' => 'package_group', 'membership_package_id' => $yearly->id],
            ['title' => 'Yearly Plan Members Chat']
        );

        $trainerChat = ClientChat::firstOrCreate(
            ['type' => 'trainer_direct', 'member_id' => $amit->id, 'trainer_id' => $rahul->id],
            ['title' => 'Chat with Rahul Trainer']
        );

        ClientChatMessage::firstOrCreate(
            ['client_chat_id' => $monthlyChat->id, 'sender_id' => $amit->id, 'body' => 'Hey everyone, what time do you usually train on weekdays?']
        );

        ClientChatMessage::firstOrCreate(
            ['client_chat_id' => $weeklyChat->id, 'sender_id' => $kevin->id, 'body' => 'Weekly members, I am training after work today if anyone wants to join.']
        );

        ClientChatMessage::firstOrCreate(
            ['client_chat_id' => $yearlyChat->id, 'sender_id' => $priya->id, 'body' => 'Yearly plan crew, has anyone tried the wellness sessions yet?']
        );

        ClientChatMessage::firstOrCreate(
            ['client_chat_id' => $trainerChat->id, 'sender_id' => $rahul->id, 'body' => 'Welcome Amit. Send me a message here whenever you need help with your workout plan.']
        );
    }
}
