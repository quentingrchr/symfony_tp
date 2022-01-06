<?php

namespace App\DataFixtures;

use App\Factory\AnswerFactory;
use App\Factory\CategoryFactory;
use App\Factory\PostFactory;
use App\Factory\QuestionFactory;
use App\Factory\UserFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);
        UserFactory::createMany(10);
        CategoryFactory::createOne(['name' => 'posters']);
        CategoryFactory::createOne(['name' => 'vehicules']);
        CategoryFactory::createOne(['name' => 'videos games']);
        PostFactory::createMany(30, function () {
            $number = rand(1, 3);
            $images = [];
            for ($i = 0; $i <= $number; $i++) {
                $images[] = PostFactory::faker()->imageUrl();
            }
            return ["images" => $images];
        });


        QuestionFactory::createMany(20);
        AnswerFactory::createMany(20);
        $manager->flush();

    }
}
