<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory
 */
class BookFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $numberOfAuthor = rand(1, 3);
        $authors = [];

        for ($i = 0; $i < $numberOfAuthor; $i++) {
            $authors[] = $this->faker->firstName() . ' ' . $this->faker->lastName();
        }

        return [
            'title' =>  $this->faker->sentence(rand(3, 8)),
            'summary'   =>  $this->faker->realText(),
            'publisher' =>  $this->faker->company(),
            'authors'   =>  $authors,
        ];
    }
}
