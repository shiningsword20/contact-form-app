<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class TagFactory extends Factory
{
    public function definition(): array
    {
        $words = ['配送', '返品', '交換', '不具合', '要望', '相談', '確認', '問合せ', '対応', '案内'];

        return [
            'name' => $this->faker->unique()->randomElement($words),
        ];
    }
}
