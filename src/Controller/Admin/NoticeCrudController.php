<?php

namespace App\Controller\Admin;

use App\Entity\Notice;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class NoticeCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Notice::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Avis')
            ->setEntityLabelInPlural('Avis')
            ->setPageTitle(Crud::PAGE_INDEX, 'Liste des avis')
            ->setPageTitle(Crud::PAGE_NEW, 'Ajouter un avis')
            ->setPageTitle(Crud::PAGE_EDIT, "Mettre à jour l'avis")
            ->setPageTitle(Crud::PAGE_DETAIL, "Détails de l'avis")
            ->setSearchFields(['id', 'name', 'msg'])
            ->setDefaultSort(['id' => 'DESC'])
            ->setTimezone('Europe/Paris');
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->update(
                Crud::PAGE_INDEX,
                Action::NEW,
                static fn(Action $action) =>
                $action->setLabel("Ajouter un avis")
                    ->setIcon('fa fa-plus')
            )
            ->update(Crud::PAGE_DETAIL, Action::EDIT, static fn(Action $action) => $action->setLabel('Mettre à jour')->setIcon('fa fa-edit'));
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')
                ->hideOnForm(),
            TextField::new('name', 'Prénom et Nom')
                ->setRequired(true)
                ->setFormTypeOption('trim', true),
            TextareaField::new('msg', 'Message')
                ->setRequired(true)
                ->setFormTypeOption('trim', true)
                ->hideOnIndex(),
            BooleanField::new('status', 'Afficher sur le site')
                ->setRequired(false)
                ->renderAsSwitch(false)
                ->setHelp("Si l'avis est activé, il sera affiché sur le site."),
            DateTimeField::new('receivedAt', 'Reçu le')
                ->hideOnForm()
                ->setFormat('dd MMM yyyy à HH:mm'),
            DateTimeField::new('updatedAt', 'Mis à jour le')
                ->hideOnForm()
                ->setFormat('dd MMM yyyy à HH:mm'),
            AssociationField::new('updatedBy', 'Mis à jour par')
                ->formatValue(
                    static fn($value, Notice $notice) =>
                    $notice->getUpdatedBy()?->getName() ?? 'Aucune intervention'
                )
                ->hideOnForm(),
        ];
    }

    public function persistEntity(EntityManagerInterface $entityManager, object  $entityInstance): void
    {
        if ($entityInstance instanceof Notice) {
            $entityInstance->setReceivedAt(new \DateTimeImmutable());
        }

        parent::persistEntity($entityManager, $entityInstance);
    }

    public function updateEntity(EntityManagerInterface $entityManager, object $entityInstance): void
    {
        $user = $this->getUser();

        if ($entityInstance instanceof Notice && $user instanceof User) {
            $entityInstance->setUpdatedAt(new \DateTimeImmutable());
            $entityInstance->setUpdatedBy($user);
        }

        parent::updateEntity($entityManager, $entityInstance);
    }
}