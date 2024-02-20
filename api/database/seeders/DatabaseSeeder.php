<?php

namespace Database\Seeders;

use App\Services\BookService;
use Faker\Generator;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        DB::table('books')->truncate();
        $total = 2000000;
        $chunkSize = 400;

        $books = $this->generateBookData($total, $chunkSize);
        $bookService = resolve(BookService::class);

        foreach ($books as $chunk) {
            $bookService->createBulk($chunk);
        }
    }

    /**
     * @param int $total
     * @param int $chunkSize
     * @return \Generator
     */
    private function generateBookData(int $total, int $chunkSize): \Generator
    {
        $faker = resolve(Generator::class);

        for ($i = 0; $i < ($total / $chunkSize); $i++) {
            $books = [];

            for ($chunkIndex = 0; $chunkIndex < $chunkSize; $chunkIndex++) {
                $numberOfAuthor = rand(1, 3);
                $authors = [];

                for ($authorIndex = 0; $authorIndex < $numberOfAuthor; $authorIndex++) {
                    $authors[] = $faker->firstName() . ' ' . $faker->lastName();
                }

                $title = $faker->sentence(rand(3, 8));
                $summary = $faker->realText();
                $publisher = $faker->company();
                $allData = implode("\n", array_merge([
                    $title,
                    $summary,
                    $publisher
                ], $authors));

                $books[] = [
                    'title'         =>  $title,
                    'summary'       =>  $summary,
                    'publisher'     =>  $publisher,
                    'authors'       =>  json_encode($authors),
                    'all_data'      =>  $allData,
                    'created_at'    =>  now(),
                    'updated_at'    =>  now(),
                ];
            }

            yield $books;
        }
    }
}
