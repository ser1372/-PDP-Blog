<?php

namespace App\Twig\Components\Header;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('header','components/header/Header.html.twig')]
final class Header
{
    public function __construct(private CategoryRepository $categoryRepository)
    {
    }

    public function getCategories(): array
    {
        return $this->categoryRepository->findAll();
    }
}
