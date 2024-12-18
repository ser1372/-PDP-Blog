<?php

namespace App\Controller;

use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted("IS_AUTHENTICATED_FULLY")]
class MainController extends AbstractController
{
    public function __construct(private PostRepository $postRepository)
    {}

    #[Route('/', name: 'app_main')]
    public function index(): Response
    {
        return $this->render('pages/main/index.html.twig', [
            'lastPosts' => $this->postRepository->getLast(),
        ]);
    }
}
