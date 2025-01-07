<?php

namespace App\Twig\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
final class PostGallery
{
    public string $galleries;

    public function getImages(): array
    {
        return !empty($this->galleries) ? explode(',', $this->galleries) : [];
    }
}
