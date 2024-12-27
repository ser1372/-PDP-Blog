<?php

namespace App\Twig\Components;

use \Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use App\Form\SubscriptionFormType;

#[AsTwigComponent]
final class MailingList
{
    public FormView $form;
    public function __construct(FormFactoryInterface $formFactory)
    {
        $this->form = $formFactory->create(SubscriptionFormType::class, null, [
            'action' => '/subscribe',
            'method' => 'POST',
        ])->createView();
    }
}
