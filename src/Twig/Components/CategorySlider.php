<?php

namespace App\Twig\Components;

use App\Repository\CategoryRepository;
use App\Repository\PostRepository;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
final class CategorySlider
{

    public function __construct(private CategoryRepository $categoryRepository)
    {}

    public function getCategories(): array
    {
        return $this->categoryRepository->getAll(true);
    }
}
