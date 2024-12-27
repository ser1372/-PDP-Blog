<?php

namespace App\EventSubscriber;

use App\Repository\PostRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelEvents;

class PostSlugSubscriber implements EventSubscriberInterface
{


    public function __construct(private PostRepository $postRepository)
    {}

    public function onKernelController(ControllerEvent $event): void
    {
        $request = $event->getRequest();

        if ($request->attributes->get('_route') !== 'app_post') {
            return;
        }

        $slug = $request->attributes->get('slug');
        $post = $this->postRepository->getPostBySlug($slug);

        if (!$post) {
            throw new NotFoundHttpException('Post not found.');
        }

        $request->attributes->set('post', $post);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::CONTROLLER => 'onKernelController',
        ];
    }
}

