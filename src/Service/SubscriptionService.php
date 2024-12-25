<?php

namespace App\Service;

use App\Entity\Subscription;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class SubscriptionService
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

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
