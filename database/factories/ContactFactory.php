<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class ContactFactory extends Factory
{
    public function definition(): array
    {
        return [
            'first_name' => $this->faker->lastName(),
            'last_name' => $this->faker->firstName(),
            'gender' => $this->faker->numberBetween(1, 3),
            'email' => $this->faker->unique()->safeEmail(),
            'tel' => $this->faker->numerify('###########'),
            'address' => $this->faker->address(),
            'building' => $this->faker->secondaryAddress(),
            'detail' => $this->faker->randomElement([
                '商品の配送状況について教えてください。発送はいつ頃になりますでしょうか。',
                '注文した商品と異なるものが届きました。至急ご確認いただけますでしょうか。',
                '返品の手続き方法について教えてください。未開封の状態です。',
                'サイズ交換は可能でしょうか。1つ大きいサイズを希望しております。',
                '会員登録の際にエラーが表示されます。確認をお願いいたします。',
                '商品到着後、初期不良が見つかりました。対応方法を教えてください。',
                '注文をキャンセルしたいのですが、手続き方法を教えてください。',
                'クーポンコードが使用できません。有効期限を確認いただけますでしょうか。',
            ]),
            'category_id' => Category::inRandomOrder()->first()->id,
        ];
    }
}
