<?php

namespace App\Controller\Admin;

use App\Entity\RoomCategoryPreference;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class RoomCategoryPreferenceCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return RoomCategoryPreference::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Préférence des catégories de chambres')
            ->setEntityLabelInPlural('Préférences des catégories de chambres')
            ->setPageTitle(Crud::PAGE_INDEX, 'Préférences des catégories de chambres')
            ->setPageTitle(Crud::PAGE_NEW, 'Ajouter une préférence')
            ->setPageTitle(Crud::PAGE_EDIT, "Mettre à jour la préférence")
            ->setPageTitle(Crud::PAGE_DETAIL, "Détails de la préférence")
            ->setSearchFields(['id', 'label'])
            ->setDefaultSort(['id' => 'DESC']);
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->update(Crud::PAGE_INDEX, Action::NEW, static fn(Action $action) => $action->setLabel('Ajouter une préférence')->setIcon('fa fa-plus'))
            ->update(Crud::PAGE_DETAIL, Action::EDIT, static fn(Action $action) => $action->setLabel('Mettre à jour la préférence')->setIcon('fa fa-edit'));
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')
                ->hideOnForm(),
            TextField::new('label', 'Libellé')
                ->setRequired(true)
                ->setFormTypeOption('trim', true),
        ];
    }
}