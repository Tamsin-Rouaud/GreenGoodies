<?php

namespace App\DataFixtures;

use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class ProductFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR'); // 🇫🇷 pour des titres et descriptions en français

        $images = ['brosse.png', 'deodorant.png', 'cotons.png', 'brosseADents.png', 'couverts.png', 'ananas.png', 'bougie.png', 'gourde.png', 'the.png'];


        for ($i = 0; $i < 12; $i++) {
            $product = new Product();
            $product->setName($faker->words(3, true)); // ex : "Savon Bio Fraîcheur"
            $product->setShortDescription($faker->sentence(1, true)); // 1 phrase
            $product->setFullDescription($faker->paragraphs(3, true)); // 3 paragraphes
            $product->setPrice($faker->randomFloat(2, 4, 40)); // prix entre 4 et 40 €
            $product->setPicture($images[array_rand($images)]); 

            $manager->persist($product);
        }

        $manager->flush();
    }
}
