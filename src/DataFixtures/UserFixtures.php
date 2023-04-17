<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class UserFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // Create an admin User
        $adminUser = new User();
        $adminUser
            ->setEmail('admin@mspr4.com')
            ->setFirstName('Admin')
            ->setLastName('Test')
            ->setRoles(['ROLE_ADMIN'])
            ->setPassword('$2y$13$.VwexFoV5cWjVsmDIHGGnOLDlsmbyUr4uBi8iFcb7Qoe8CHSxMqce');

        $manager->persist($adminUser);

        // Create a webshop User
        $webshopUser = new User();
        $webshopUser
            ->setEmail('webshop@mspr4.com')
            ->setFirstName('Webshop')
            ->setLastName('Test')
            ->setRoles(['ROLE_WEBSHOP'])
            ->setPassword('$2y$13$.VwexFoV5cWjVsmDIHGGnOLDlsmbyUr4uBi8iFcb7Qoe8CHSxMqce');

        $manager->persist($webshopUser);

        // Create a retailer User
        $retailerUser = new User();
        $retailerUser
            ->setEmail('retailer@mspr4.com')
            ->setFirstName('Retailer')
            ->setLastName('Test')
            ->setRoles(['ROLE_RETAILER'])
            ->setPassword('$2y$13$.VwexFoV5cWjVsmDIHGGnOLDlsmbyUr4uBi8iFcb7Qoe8CHSxMqce');

        $manager->persist($retailerUser);

        // Flush User Fixtures
        $manager->flush();
    }
}
