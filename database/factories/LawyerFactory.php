<?php

namespace Database\Factories;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Lawyer>
 */
class LawyerFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'phone' => '+92' . $this->faker->numberBetween(3000000000, 3499999999),
            'password' => static::$password ??= Hash::make('password'), // Hashed password
            'location' => $this->faker->city() . ', Pakistan',
            'availability_from' => Carbon::now()->addDays(rand(1, 10))->format('H:i:s'),
            'availability_to' => Carbon::now()->addDays(rand(20, 30))->format('H:i:s'),
            'specialization' => $this->faker->randomElement(['Corporate Law', 'Criminal Law', 'Family Law', 'Property Law', 'Tax Law']),
            'experience' => rand(1, 25), // Years of experience
            'price' => $this->faker->numberBetween(1000, 10000), // Random price between 5,000 and 50,000 PKR
            'qualification' => $this->faker->randomElement(['LLB', 'LLM', 'JD', 'PhD in Law']),
            'verified_email' => $this->faker->boolean(80),
        ];
    }
}
