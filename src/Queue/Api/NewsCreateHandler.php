<?php

namespace App\Queue\Api;

use App\Service\PostService;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class NewsCreateHandler
{
    public function __construct(private readonly PostService $postService)
    {}

    public function __invoke(NewsCreate $newsCreate)
    {
        try {
            return $this->postService->createNews($newsCreate);
        } catch (\Exception $e){
            error_log($e->getMessage());
        }
    }
}
