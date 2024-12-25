<?php

namespace App\Service;

use App\Entity\Subscription;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class SubscriptionService
{

    public function __construct(private EntityManagerInterface $entityManager)
    {}

    public function createSubscription(string $email, UserInterface $user): Subscription
    {
        $subscription = new Subscription();
        $subscription->setEmail($email);
        $subscription->setUser($user);

        $this->entityManager->persist($subscription);
        $this->entityManager->flush();

        return $subscription;
    }
}
