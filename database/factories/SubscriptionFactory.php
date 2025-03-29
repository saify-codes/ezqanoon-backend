<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Subscription>
 */
class SubscriptionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $subscriptionTypes = [
            'BASIC',
            'STANDARD',
            'PREMIUM',
            'ENTERPRISE',
        ];

        $features = [
            'Unlimited access',
            'Priority support',
            'Custom branding',
            'Advanced analytics',
            'API access',
            'Team collaboration',
            'Document templates',
            'Automated workflows',
            'Mobile app access',
            'Data backup'
        ];

        return [
            'name'          => $this->faker->unique()->randomElement($subscriptionTypes),
            'description'   => $this->faker->paragraph(1),
            'price'         => $this->faker->randomElement([500, 1000, 2000, 3000, 4000, 5000]),
            'features'      => $this->faker->randomElements($features, rand(4, 8)),
            'status'        => $this->faker->randomElement(['active', 'inactive']),
        ];
    }
}
