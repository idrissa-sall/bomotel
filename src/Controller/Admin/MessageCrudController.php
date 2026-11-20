<?php

namespace App\Controller\Admin;

use App\Entity\Message;
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
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Config\KeyValueStore;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use Symfony\Component\HttpFoundation\Response;

class MessageCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Message::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Message')
            ->setEntityLabelInPlural('Messages')
            ->setPageTitle(Crud::PAGE_INDEX, 'Liste des messages')
            ->setPageTitle(Crud::PAGE_NEW, 'Ajouter un message')
            ->setPageTitle(Crud::PAGE_EDIT, 'Mettre à jour le message')
            ->setPageTitle(Crud::PAGE_DETAIL, 'Détails du message')
            ->setSearchFields(['id', 'name', 'email', 'subject'])
            ->setDefaultSort(['id' => 'DESC'])
            ->setTimezone('Europe/Paris');
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->update(Crud::PAGE_DETAIL, Action::EDIT, static fn(Action $action) => $action->setLabel('Mettre à jour')->setIcon('fa fa-edit'))
            ->disable(Action::NEW, Action::DELETE, Action::BATCH_DELETE);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')
                ->hideOnForm(),
            TextField::new('name', 'Prénom et Nom')
                ->hideOnForm(),
            TextField::new('email', 'Adresse e-mail')
                ->hideOnForm(),
            TextField::new('subject', 'Sujet')
                ->hideOnForm(),
            TextareaField::new('msg', 'Message')
                ->onlyOnDetail(),
            BooleanField::new('status', 'Traité')
                ->setRequired(false)
                ->renderAsSwitch(false),
            DateTimeField::new('receivedAt', 'Reçu le')
                ->setFormat('dd MMM yyyy à HH:mm')
                ->hideOnForm(),
            DateTimeField::new('openedAt', 'Ouvert le')
                ->setFormat('dd MMM yyyy à HH:mm')
                ->hideOnForm(),
            AssociationField::new('updatedBy', 'Mis à jour par')
                ->formatValue(
                    static fn($value, Message $message) =>
                    $message->getUpdatedBy()?->getName() ?? 'Aucune intervention'
                )
                ->hideOnForm(),
        ];
    }

    public function detail(AdminContext $context): KeyValueStore|Response
    {
        $response = parent::detail($context);

        // Le parent vérifie les permissions avant notre traitement.
        if (!$response instanceof KeyValueStore) {
            return $response;
        }

        $message = $context->getEntity()->getInstance();

        if ($message instanceof Message && $message->getOpenedAt() === null) {
            $message->setOpenedAt(new \DateTimeImmutable());
            $entityManager = $this->container->get('doctrine')->getManagerForClass(Message::class);

            $entityManager->flush();
        }

        return $response;
    }

    public function updateEntity(EntityManagerInterface $entityManager, object $entityInstance): void
    {
        $user = $this->getUser();

        if ($entityInstance instanceof Message && $user instanceof User) {
            $entityInstance->setUpdatedBy($user);
        }

        parent::updateEntity($entityManager, $entityInstance);
    }
}
