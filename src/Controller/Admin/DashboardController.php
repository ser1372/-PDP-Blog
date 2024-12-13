<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Entity\Post;
use App\Entity\User;
use App\Enum\RoleEnum;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DashboardController extends AbstractDashboardController
{

    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        return $this->render('pages/admin/dashboard/dashboard.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Dashboard');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linktoDashboard('Dashboard', 'fa fa-home');

        if($this->security->isGranted(RoleEnum::ADMIN->value))
        {
            yield MenuItem::linkToCrud('Categories', 'fa fa-list-alt', Category::class);
            yield MenuItem::linkToCrud('Users', 'fa fa-users', User::class);
        }

        if($this->security->isGranted(RoleEnum::ADMIN->value) || $this->security->isGranted(RoleEnum::AUTHOR->value))
        {
            yield MenuItem::linkToCrud('Posts', 'fas fas fa-book', Post::class);
        }
    }
}
