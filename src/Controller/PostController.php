<?php

namespace App\Controller;

use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class PostController extends AbstractController
{
    public function __construct(private PostRepository $postRepository)
    {}

    #[Route('/post/{slug}', name: 'app_post')]
    public function index(Request $request): Response
    {
        $post = $request->attributes->get('post');
        $user = $post->getUser();
        return $this->render('pages/post/view.html.twig', compact('post','user'));
    }
}
