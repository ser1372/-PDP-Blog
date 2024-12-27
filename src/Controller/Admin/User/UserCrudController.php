<?php

namespace App\Controller\Admin\User;

use App\Entity\User;
use App\Enum\RoleEnum;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted("ROLE_ADMIN")]
class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('email'),
            ChoiceField::new('roles', 'Roles')
                ->setChoices([
                    'ADMIN'  => RoleEnum::ADMIN->value,
                    'USER'   => RoleEnum::USER->value,
                    'AUTHOR' => RoleEnum::AUTHOR->value,
                ])
                ->setSortable(false)
                ->allowMultipleChoices(true)
                ->renderExpanded(true)
        ];
    }
}
