<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class UserFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // Create a test User
        $testUser = new User();
        $testUser
            ->setEmail('test@mspr4.com')
            ->setPassword('$2y$13$.VwexFoV5cWjVsmDIHGGnOLDlsmbyUr4uBi8iFcb7Qoe8CHSxMqce');

        $manager->persist($testUser);

        // Flush User Fixtures
        $manager->flush();
    }
}
