<?php

namespace App\Controller\Admin\Category;

use App\Entity\Category;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Validator\Constraints\Image;

#[IsGranted("ROLE_ADMIN")]
class CategoryCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Category::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('title'),
            ImageField::new('img')
                ->setBasePath('uploads/categories')
                ->setUploadDir('public/uploads/categories')
                ->setRequired($pageName === Crud::PAGE_NEW)
                ->setFormTypeOption('allow_delete', false)
                ->setFileConstraints(new Image(
                    maxSize: '2M',
                    mimeTypes: ['image/jpeg', 'image/png'],
                )),
            TextEditorField::new('description'),
            BooleanField::new('showInSlider')
                ->setLabel('Show in Slider')
        ];
    }
}
