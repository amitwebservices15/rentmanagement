<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SubscriptionPlan;

class SubscriptionPlanSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Basic Plan',
                'price' => 199,
                'validity_days' => 30,
                'message_credits' => 20,
                'max_properties' => 2,
                'max_rooms' => 10,
                'features' => ['Basic property listing', 'Tenant management', 'Email support'],
                'is_active' => true,
                'is_popular' => false,
            ],
            [
                'name' => 'Premium Plan',
                'price' => 499,
                'validity_days' => 30,
                'message_credits' => 100,
                'max_properties' => 10,
                'max_rooms' => 50,
                'features' => ['Unlimited property listing', 'Advanced tenant management', 'Priority support', 'Analytics dashboard'],
                'is_active' => true,
                'is_popular' => true,
            ],
            [
                'name' => 'Enterprise Plan',
                'price' => 999,
                'validity_days' => 30,
                'message_credits' => 500,
                'max_properties' => 50,
                'max_rooms' => 200,
                'features' => ['Everything in Premium', 'Custom integrations', 'Dedicated support', 'White-label solution'],
                'is_active' => true,
                'is_popular' => false,
            ],
        ];

        foreach ($plans as $plan) {
            SubscriptionPlan::updateOrCreate(
                ['name' => $plan['name']],
                $plan
            );
        }
    }
}