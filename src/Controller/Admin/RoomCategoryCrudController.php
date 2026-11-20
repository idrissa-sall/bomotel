<?php

namespace App\Controller\Admin;

use App\Entity\RoomCategory;
use App\Entity\RoomCategoryPreference;
use App\Entity\User;
use App\Enum\RoomCategoryEnum;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;

class RoomCategoryCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return RoomCategory::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Catégorie de chambre')
            ->setEntityLabelInPlural('Catégories de chambre')
            ->setPageTitle(Crud::PAGE_INDEX, 'Liste des catégories de chambre')
            ->setPageTitle(Crud::PAGE_NEW, 'Ajouter une catégorie de chambre')
            ->setPageTitle(Crud::PAGE_EDIT, "Mettre à jour la catégorie de chambre")
            ->setPageTitle(Crud::PAGE_DETAIL, "Détails de la catégorie de chambre")
            ->setSearchFields(['id', 'label', 'pricePerNight', 'description'])
            ->setDefaultSort(['id' => 'DESC'])
            ->setTimezone('Europe/Paris');
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->update(Crud::PAGE_INDEX, Action::NEW, static fn(Action $action) => $action->setLabel('Ajouter une catégorie de chambre')->setIcon('fa fa-plus'))
            ->update(Crud::PAGE_DETAIL, Action::EDIT, static fn(Action $action) => $action->setLabel('Mettre à jour la catégorie')->setIcon('fa fa-edit'))
            ->disable(Action::DELETE, Action::BATCH_DELETE);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')
                ->hideOnForm(),
            ChoiceField::new('label', 'Catégorie de chambre')
                ->setRequired(true)
                ->setChoices([
                    'Single' => RoomCategoryEnum::SINGLE,
                    'Double' => RoomCategoryEnum::DOUBLE,
                    'Triple' => RoomCategoryEnum::TRIPLE,
                    'Quadruple' => RoomCategoryEnum::QUAD,
                    'Familiale' => RoomCategoryEnum::FAMILIALE,
                ])
                ->setDisabled(true),
            NumberField::new('pricePerNight', 'Prix par nuit (€)')
                ->setRequired(true)
                ->setNumDecimals(2)
                ->setStoredAsString(true)
                ->setHelp('Montant en euros.'),
            TextareaField::new('description', 'Description'),
            AssociationField::new('roomCategoryPreferences', 'Préférences')
                ->setFormTypeOption('choice_label', 'label')
                ->setFormTypeOption('by_reference', false)
                ->setRequired(false)
                ->formatValue(static function ($value, RoomCategory $roomCategory): string {
                    $labels = $roomCategory->getRoomCategoryPreferences()
                        ->map(
                            static fn(RoomCategoryPreference $preference) =>
                            $preference->getLabel()
                        )
                        ->toArray();

                    return $labels === []
                        ? 'Aucune préférence'
                        : implode(', ', $labels);
                }),
            DateTimeField::new('createdAt', 'Créé le')
                ->hideOnForm()
                ->setFormat('dd MMM yyyy à HH:mm'),
            DateTimeField::new('updatedAt', 'Mis à jour le')
                ->hideOnForm()
                ->setFormat('dd MMM yyyy à HH:mm'),
            AssociationField::new('updatedBy', 'Mis à jour par')
                ->formatValue(
                    static fn($value, RoomCategory $roomCategory) =>
                    $roomCategory->getUpdatedBy()?->getName() ?? 'Aucune intervention'
                )
                ->hideOnForm(),
        ];
    }

    public function persistEntity(EntityManagerInterface $entityManager, object $entityInstance): void
    {
        $user = $this->getUser();

        if ($entityInstance instanceof RoomCategory && $user instanceof User) {
            $entityInstance->setCreatedAt(new \DateTimeImmutable());
            $entityInstance->setCreatedBy($user);
        }

        parent::persistEntity($entityManager, $entityInstance);
    }

    public function updateEntity(EntityManagerInterface $entityManager, object $entityInstance): void
    {
        $user = $this->getUser();

        if ($entityInstance instanceof RoomCategory && $user instanceof User) {
            $entityInstance->setUpdatedAt(new \DateTimeImmutable());
            $entityInstance->setUpdatedBy($user);
        }

        parent::updateEntity($entityManager, $entityInstance);
    }
}