<?php

namespace App\Twig\Components;

use App\Repository\PostRepository;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
final class PopularPostSlider
{
    public function __construct(private PostRepository $postRepository)
    {}

    public function getLastPosts(): array
    {
        return $this->postRepository->getLast();
    }
}
