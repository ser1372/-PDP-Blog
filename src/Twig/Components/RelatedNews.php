<?php

namespace App\Twig\Components;

use App\Repository\PostRepository;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
final class RelatedNews
{
    public function __construct(private PostRepository $postRepository)
    {}

    public function getNews(): array
    {
        return $this->postRepository->getNews();
    }
}
