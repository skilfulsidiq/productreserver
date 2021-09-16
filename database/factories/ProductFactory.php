<?php

namespace Database\Factories;

use App\Models\Brand;
use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Product::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'product_name'=>$this->faker->name,
            'product_discount'=>$this->faker->numberBetween(1,10),
            'product_price'=>$this->faker->numberBetween(500,30000),
            'product_cover_image'=>$this->faker->imageUrl(),
            'product_description'=>$this->faker->paragraph($nbSentences = 3, $variableNbSentences = true),
            'discount_start_date'=>$this->faker->date($format = 'Y-m-d', $max = 'now'),
            'discount_end_date'=>$this->faker->date($format = 'Y-m-d', $max = 'now'),
            'slug'=>$this->faker->numberBetween(0,1)
        ];
    }
}
