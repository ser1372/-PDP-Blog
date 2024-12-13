<?php

namespace App\Controller\Admin\Post;

use App\Entity\Post;
use App\Enum\PostStatusEnum;
use App\Enum\RoleEnum;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Bundle\SecurityBundle\Security;
use \EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use \EasyCorp\Bundle\EasyAdminBundle\Config\Actions;

class PostCrudController extends AbstractCrudController
{
    private Security $security;
    private EntityManagerInterface $entityManager;
    public function __construct(Security $security, EntityManagerInterface $entityManager)
    {
        $this->security = $security;
        $this->entityManager = $entityManager;
    }
    public static function getEntityFqcn(): string
    {
        return Post::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('name'),
            AssociationField::new('category', 'Category')
                ->setRequired(true)
                ->setFormTypeOption('choice_label', 'title'),
            TextEditorField::new('description'),
            TextField::new('status')
            ->hideOnDetail()
            ->hideOnForm()
            ->formatValue(fn($value) => strtoupper($value)),
        ];
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if ($entityInstance instanceof Post) {
            $entityInstance->setUser($this->security->getUser());
        }

        parent::persistEntity($entityManager, $entityInstance);
    }


    public function publishPost(AdminContext $context): \Symfony\Component\HttpFoundation\RedirectResponse
    {
        $post = $context->getEntity()->getInstance();

        if (!$post instanceof Post) {
            throw new \Exception('Entity is not a Post');
        }

        $post->setStatus(PostStatusEnum::PUBLISHED->value);
        $post->setActive(true);
        $this->entityManager->flush();

        $this->addFlash('success', sprintf('Post "%s" has been published.', $post->getName()));

        return $this->redirect($this->container->get(AdminUrlGenerator::class)
            ->setController(PostCrudController::class)
            ->setAction(Action::INDEX)
            ->generateUrl());
    }

    public function unpublishPost(AdminContext $context): \Symfony\Component\HttpFoundation\RedirectResponse
    {
        $post = $context->getEntity()->getInstance();

        if (!$post instanceof Post) {
            throw new \Exception('Entity is not a Post');
        }

        $post->setStatus(PostStatusEnum::MODERATION->value);
        $post->setActive(false);
        $this->entityManager->flush();

        $this->addFlash('success', sprintf('Post "%s" has been unpublished.', $post->getName()));
        return $this->redirect($this->container->get(AdminUrlGenerator::class)
            ->setController(PostCrudController::class)
            ->setAction(Action::INDEX)
            ->generateUrl());
    }


    public function active(AdminContext $context): \Symfony\Component\HttpFoundation\RedirectResponse
    {
        $post = $context->getEntity()->getInstance();

        if (!$post instanceof Post) {
            throw new \Exception('Entity is not a Post');
        }

        $post->setActive(true);
        $this->entityManager->flush();

        $this->addFlash('success', sprintf('Post "%s" has been active.', $post->getName()));
        return $this->redirect($this->container->get(AdminUrlGenerator::class)
            ->setController(PostCrudController::class)
            ->setAction(Action::INDEX)
            ->generateUrl());
    }

    public function inactive(AdminContext $context): \Symfony\Component\HttpFoundation\RedirectResponse
    {
        $post = $context->getEntity()->getInstance();

        if (!$post instanceof Post) {
            throw new \Exception('Entity is not a Post');
        }

        $post->setActive(false);
        $this->entityManager->flush();

        $this->addFlash('success', sprintf('Post "%s" has been unactive.', $post->getName()));
        return $this->redirect($this->container->get(AdminUrlGenerator::class)
            ->setController(PostCrudController::class)
            ->setAction(Action::INDEX)
            ->generateUrl());
    }


    public function configureActions(Actions $actions): Actions
    {
        if($this->security->isGranted(RoleEnum::ADMIN->value)) {
            $publishAction = Action::new('publish', 'Publish')
                ->linkToCrudAction('publishPost')
                ->setCssClass('btn btn-success')
                ->displayIf(function ($entity) {
                    return $entity instanceof Post && $entity->getStatus() === PostStatusEnum::MODERATION->value;
                });

            $unpublishAction = Action::new('unpublish', 'Unpublish')
                ->linkToCrudAction('unpublishPost')
                ->setCssClass('btn btn-warning')
                ->displayIf(function ($entity) {
                    return $entity instanceof Post && $entity->getStatus() === PostStatusEnum::PUBLISHED->value;
                });

            $actions
                ->add(Crud::PAGE_INDEX, $publishAction)
                ->add(Crud::PAGE_INDEX, $unpublishAction)
                ->add(Crud::PAGE_DETAIL, $publishAction)
                ->add(Crud::PAGE_DETAIL, $unpublishAction);
        }

        if($this->security->isGranted(RoleEnum::AUTHOR->value) || $this->security->isGranted(RoleEnum::ADMIN->value)){
            $active = Action::new('active', 'Active')
                ->linkToCrudAction('active')
                ->setCssClass('btn btn-success')
                ->displayIf(function ($entity) {
                    return $entity instanceof Post && !$entity->isActive();
                });

            $inactive = Action::new('inactive', 'Inactive')
                ->linkToCrudAction('inactive')
                ->setCssClass('btn btn-warning')
                ->displayIf(function ($entity) {
                    return $entity instanceof Post && $entity->isActive();
                });

            $actions
                ->add(Crud::PAGE_INDEX, $inactive)
                ->add(Crud::PAGE_INDEX, $active)
                ->add(Crud::PAGE_DETAIL, $inactive)
                ->add(Crud::PAGE_DETAIL, $active);
        }

        return $actions;
    }
}
