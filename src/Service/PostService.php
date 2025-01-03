<?php

namespace App\Service;

use App\Entity\Post;
use App\Entity\User;
use App\Enum\PostTypeEnum;
use App\Enum\RoleEnum;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;

class PostService{

    const SUPER_ADMIN = 1;

    public function __construct(
        private EntityManagerInterface $entityManager,
        private ImageService $imageService,
        private UserRepository $userRepository
    )
    {}

    public function createNews(object $data): Post
    {
        $currentNews = $data->content;

        $news = new Post();
        $news->setType(PostTypeEnum::NEWS);
        $news->setName($currentNews->title, $currentNews->url);
        $news->setImg($this->imageService->downloadImage($currentNews->urlToImage, 'posts'));
        $news->setDescription($currentNews->description);
        $news->setUser($this->userRepository->find(self::SUPER_ADMIN));

        $this->entityManager->persist($news);
        $this->entityManager->flush();

        return $news;
    }

}
