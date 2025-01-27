<?php

namespace App\Controller\Admin\Post;

use App\Entity\Post;
use App\Enum\PostStatusEnum;
use App\Enum\RoleEnum;
use App\Form\GalleryType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Bundle\SecurityBundle\Security;
use \EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use \EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use Symfony\Component\Validator\Constraints\Image;


class PostCrudController extends AbstractCrudController
{
    private Security $security;
    private EntityManagerInterface $entityManager;
    public function __construct(Security $security, EntityManagerInterface $entityManager)
    {
        $this->security = $security;
        $this->entityManager = $entityManager;
    }


    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        $user = $this->security->getUser();
        $queryBuilder = parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters);

        if (!$this->security->isGranted(RoleEnum::ADMIN->value)) {
            $queryBuilder->andWhere('entity.user = :currentUser')
                ->setParameter('currentUser', $user);
        }

        return $queryBuilder;
    }


    public static function getEntityFqcn(): string
    {
        return Post::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('name'),
            ImageField::new('img')
                ->setBasePath('uploads/posts')
                ->setUploadDir('public/uploads/posts')
                ->setRequired($pageName === Crud::PAGE_NEW)
                ->setFormTypeOption('allow_delete', false)
                ->setFileConstraints(new Image(
                    maxSize: '2M',
                    mimeTypes: ['image/jpeg', 'image/png'],
                )),
            AssociationField::new('category')
                ->setRequired(true)
                ->setFormTypeOption('choice_label', 'title')
                ->renderAsHtml()
                ->formatValue(function ($value, $entity) {
                    return $value ? $value->getTitle() : '-';
                }),
            CollectionField::new('gallery')
                ->setEntryType(GalleryType::class)
                ->setFormTypeOption('by_reference', false)
                ->onlyOnForms(),
            TextEditorField::new('description')
                ->hideOnIndex(),
            TextField::new('status')
                ->hideOnDetail()
                ->hideOnForm()
                ->formatValue(fn($value) => strtoupper($value)),
            DateTimeField::new('createdAt', 'Created At')
                ->hideOnForm(),
            DateTimeField::new('updatedAt', 'Updated At')
                ->hideOnForm(),
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

        if($this->isAdmin()) $post->setStatus(PostStatusEnum::PUBLISHED->value);

        $post->setActive(true);
        $this->entityManager->flush();

        $this->addFlash('success', sprintf('Post "%s" has been published.', $post->getName()));

        return $this->redirect($this->container->get(AdminUrlGenerator::class)
            ->setController(PostCrudController::class)
            ->setAction(Action::INDEX)
            ->generateUrl());
    }


    protected function isAdmin(): bool
    {
        return in_array(RoleEnum::ADMIN->value, $this->getUser()->getRoles());
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
