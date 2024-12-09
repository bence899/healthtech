<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class MedicalDocumentFactory extends Factory
{
    public function definition()
    {
        return [
            'title' => $this->faker->sentence,
            'file_path' => $this->faker->filePath(),
            'file_type' => 'pdf',
            'file_size' => $this->faker->numberBetween(1000, 5000000),
            'description' => $this->faker->paragraph,
            'upload_month' => now()->format('Y-m')
        ];
    }
} 