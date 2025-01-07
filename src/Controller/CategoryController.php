<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CategoryController extends AbstractController
{
    public function __construct(private PostRepository $postRepository)
    {}

    #[Route('/category/{slug}', name: 'app_category')]
    public function index(EntityManagerInterface $entityManager, Request $request): Response
    {
        $posts = $this->postRepository->getAllByCategory($request);
        return $this->render('pages/category/index.html.twig', [
            'category' => $request->attributes->get('category'),
            'posts' => $posts,
            'currentPage' => max($request->query->getInt('page', 1), 1),
            'totalPages' => ceil(count($posts) / 10),
        ]);
    }
}
