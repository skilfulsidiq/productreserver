<?php

namespace Database\Factories;

use App\Models\ProductGallery;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductGalleryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ProductGallery::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
          return [
            //
            'product_id'=>$this->faker->numberBetween(1,20),
            'file_url'=>$this->faker->imageUrl()
        ];
    }
}
