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
        $faker = Factory::create('fr_FR'); 

        $images = ['brosse.png', 'deodorant.png', 'cotons.png', 'brosseADents.png', 'couverts.png', 'ananas.png', 'bougie.png', 'gourde.png', 'the.png'];


        for ($i = 0; $i < 12; $i++) {
            $product = new Product();
            $product->setName($faker->words(3, true)); 
            $product->setShortDescription($faker->sentence(1, true)); 
            $product->setFullDescription($faker->paragraphs(3, true)); 
            $product->setPrice($faker->randomFloat(2, 4, 40)); 
            $product->setPicture($images[array_rand($images)]); 

            $manager->persist($product);
        }

        $manager->flush();
    }
}
