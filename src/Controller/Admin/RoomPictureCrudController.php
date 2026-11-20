<?php

namespace App\Controller\Admin;

use App\Entity\RoomCategory;
use App\Entity\RoomPicture;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use Override;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Filesystem\Filesystem;

class RoomPictureCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return RoomPicture::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Image de chambre')
            ->setEntityLabelInPlural('Images de chambre')
            ->setPageTitle(Crud::PAGE_INDEX, 'Liste des images de chambre')
            ->setPageTitle(Crud::PAGE_NEW, 'Ajouter une image de chambre')
            ->setPageTitle(Crud::PAGE_EDIT, "Mettre à jour l'image")
            ->setPageTitle(Crud::PAGE_DETAIL, "Détails de l'image")
            ->setSearchFields(['id', 'filename', 'alt'])
            ->setDefaultSort(['id' => 'DESC'])
            ->setTimezone('Europe/Paris');
    }

    #[Override]
    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(
                EntityFilter::new('roomCategory', 'Catégorie de chambre')
                    ->setFormTypeOption(
                        'value_type_options.choice_label',
                        static fn(RoomCategory $category) =>
                        $category->getLabel()?->value ?? 'Sans catégorie'
                    )
            );;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->update(Crud::PAGE_INDEX, Action::NEW, static fn(Action $action) => $action->setLabel('Ajouter une image de chambre')->setIcon('fa fa-plus'))
            ->update(Crud::PAGE_DETAIL, Action::EDIT, static fn(Action $action) => $action->setLabel("Mettre à jour l'image")->setIcon('fa fa-edit'));
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            ImageField::new('filename', 'Image')
                ->setBasePath('/uploads/room-categories')
                ->setUploadDir('public/uploads/room-categories')
                ->setUploadedFileNamePattern('[uuid].[extension]')
                ->setRequired($pageName === Crud::PAGE_NEW)
                ->setFileConstraints(new Assert\Image(
                    maxSize: '5M',
                    mimeTypes: ['image/jpeg', 'image/png', 'image/webp']
                )),
            TextField::new('alt', 'Description alternative')
                ->setRequired(true),
            IntegerField::new('position', "Ordre d'affichage")
                ->setRequired(true),
            AssociationField::new('roomCategory', 'Catégorie')
                ->setRequired(true)
                ->setFormTypeOption(
                    'choice_label',
                    static fn(RoomCategory $category) =>
                    $category->getLabel()?->value ?? 'Sans libellé'
                )
                ->formatValue(
                    static fn($value, RoomPicture $picture) =>
                    strtoupper($picture->getRoomCategory()?->getLabel()?->value)
                        ?? 'Sans catégorie'
                ),
            DateTimeField::new('createdAt', 'Ajoutée le')
                ->hideOnForm()
                ->setFormat('dd MMM yyyy HH:mm'),
        ];
    }

    public function persistEntity(EntityManagerInterface $entityManager, object $entityInstance): void
    {
        if ($entityInstance instanceof RoomPicture) {
            $entityInstance->setCreatedAt(new \DateTimeImmutable());
        }

        parent::persistEntity($entityManager, $entityInstance);
    }

    /** Supprime le fichier physique après la suppression réussie de la ligne en base. */
    public function deleteEntity(EntityManagerInterface $entityManager, object $entityInstance): void
    {
        $filename = $entityInstance instanceof RoomPicture
            ? $entityInstance->getFilename()
            : null;

        if ($filename !== null && $filename !== '' && $filename !== basename($filename)) {
            throw new \LogicException('Nom de fichier image invalide.');
        }

        parent::deleteEntity($entityManager, $entityInstance);

        if ($filename === null || $filename === '') {
            return;
        }

        $path = $this->getParameter('kernel.project_dir')
            .'/public/uploads/room-categories/'
            .$filename;

        (new Filesystem())->remove($path);
    }
}
