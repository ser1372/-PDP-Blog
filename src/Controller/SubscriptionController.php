<?php

namespace App\Controller;

use App\Entity\Subscription;
use App\Form\SubscriptionFormType;
use App\Service\SubscriptionService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SubscriptionController extends AbstractController
{

    public function __construct(private SubscriptionService $subscriptionService)
    {}

    #[Route('/subscribe', name: 'app_subscribe')]
    public function subscribe(Request $request): Response
    {
        $form = $this->createForm(SubscriptionFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            try {
                $this->subscriptionService->createSubscription($data['email_address'], $this->getUser());
                $this->addFlash('success', 'Thank you for subscribing!');
            } catch (\Exception $e) {
                $this->addFlash('error', 'An error occurred: ' . $e->getMessage());
            }

            return $this->redirectToRoute('app_main');
        }

        $this->addFlash('error', 'Please check your data and try again!');
        return $this->redirectToRoute('app_main');
    }
}
