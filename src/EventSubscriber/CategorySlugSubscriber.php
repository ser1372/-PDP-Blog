<?php

namespace App\EventSubscriber;

use App\Repository\CategoryRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerArgumentsEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelEvents;

class CategorySlugSubscriber implements EventSubscriberInterface
{
    private CategoryRepository $categoryRepository;

    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::CONTROLLER_ARGUMENTS => 'onKernelControllerArguments',
        ];
    }

    public function onKernelControllerArguments(ControllerArgumentsEvent $event): void
    {
        $request = $event->getRequest();
        $route = $request->attributes->get('_route');

        if ($route !== 'app_category') {
            return;
        }

        $slug = $request->attributes->get('slug');

        if (!$slug) {
            throw new NotFoundHttpException('Slug not provided');
        }

        $category = $this->categoryRepository->findOneBy(['slug' => $slug]);

        if (!$category) {
            throw new NotFoundHttpException(sprintf('Category with slug "%s" not found', $slug));
        }

        $request->attributes->set('category', $category);
    }
}
