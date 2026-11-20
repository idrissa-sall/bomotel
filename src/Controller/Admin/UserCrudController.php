<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Enum\RoleEnum;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Constraints as Assert;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;

class UserCrudController extends AbstractCrudController
{
    /**
     * Injecte le service Symfony utilisé pour hacher les mots de passe avant leur enregistrement.
     */
    public function __construct(private readonly UserPasswordHasherInterface $passwordHasher) {}

    /**
     * Indique à EasyAdmin que ce contrôleur administre l'entité User.
     */
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    /**
     * Configure les libellés, les titres, la recherche et le tri des pages utilisateurs.
     * Les valeurs insérées dans les titres HTML sont échappées et les dates sont affichées à l'heure de Paris.
     */
    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Utilisateur')
            ->setEntityLabelInPlural('Utilisateurs')
            ->setPageTitle(Crud::PAGE_INDEX, 'Liste des utilisateurs')
            ->setPageTitle(Crud::PAGE_NEW, 'Ajouter un utilisateur')
            ->setPageTitle(Crud::PAGE_EDIT, fn(User $user) => sprintf("Modifier l'utilisateur <b>%s</b>", htmlspecialchars(
                $user->getName() ?? $user->getUserIdentifier(),
                ENT_QUOTES | ENT_SUBSTITUTE,
                'UTF-8'
            )))
            ->setPageTitle(Crud::PAGE_DETAIL, fn(User $user) => sprintf("Détails de l'utilisateur <b>%s</b>", htmlspecialchars(
                $user->getName() ?? $user->getUserIdentifier(),
                ENT_QUOTES | ENT_SUBSTITUTE,
                'UTF-8'
            )))
            ->setSearchFields(['id', 'name', 'email'])
            ->setDefaultSort(['id' => 'DESC'])
            ->setTimezone('Europe/Paris');
    }

    /**
     * Personnalise le bouton de création et interdit les suppressions individuelles et groupées,
     * y compris lorsqu'une action est appelée directement par son URL.
     */
    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->update(
                Crud::PAGE_INDEX,
                Action::NEW,
                static fn(Action $action) =>
                $action->setLabel("Ajouter un utilisateur")
                    ->setIcon('fa fa-plus')
            )
            // ->remove(Crud::PAGE_INDEX, Action::DELETE) // supprime juste le bouton
            ->disable(Action::DELETE, Action::BATCH_DELETE); // supprime le bouton et l'action
    }

    /**
     * Définit les colonnes et les champs selon la page EasyAdmin affichée.
     * Propose les rôles supplémentaires au rôle Utilisateur implicite et permet de modifier le statut.
     * Valide le mot de passe en clair avant le hachage : obligatoire à la création,
     * facultatif en modification, avec contrôle de longueur et de robustesse s'il est renseigné.
     * Les dates sont consultables mais ne sont pas modifiables dans les formulaires.
     */
    public function configureFields(string $pageName): iterable
    {
        $roles = [];

        foreach (RoleEnum::cases() as $role) {
            if ($role === RoleEnum::ROLE_USER) {
                continue; // Skip the ROLE_USER role because it is the default role and should not be assigned manually
            }
            $roles[$role->getLabel()] = $role->value;
        }

        return [
            IdField::new('id')
                ->hideOnForm(),
            TextField::new('name', 'Prénom et Nom')
                ->setRequired(true)
                ->setFormTypeOption('trim', true),
            EmailField::new('email', 'Adresse e-mail')
                ->setRequired(true)
                ->setFormTypeOption('trim', true),
            ChoiceField::new('roles', 'Rôles')
                ->setChoices($roles)
                ->allowMultipleChoices()
                ->setRequired(false)
                ->renderExpanded()
                ->setHelp('Tous les comptes possèdent automatiquement le rôle Utilisateur, si ce utilisateur est dois administrer le site, merci de cocher le rôle souhaité.'),
            BooleanField::new('status', 'Compte actif')
                ->setRequired(false)
                ->renderAsSwitch(false)
                ->setHelp("Si le compte est désactivé, l'utilisateur ne pourra pas se connecter à l'application."),
            TextField::new('password', 'Mot de passe')
                ->setFormType(PasswordType::class)
                ->setRequired($pageName === Crud::PAGE_NEW)
                ->setFormTypeOption('trim', false)
                ->setFormTypeOption('constraints', [
                    ...($pageName === Crud::PAGE_NEW
                        ? [new Assert\NotBlank(message: 'Le mot de passe est obligatoire.')]
                        : []
                    ),
                    new Assert\When(
                        expression: 'value !== "" and value !== null',
                        constraints: [
                            new Assert\Length(
                                min: 12,
                                minMessage: 'Le mot de passe doit contenir au moins {{ limit }} caractères.'
                            ),
                            new Assert\PasswordStrength(
                                minScore: Assert\PasswordStrength::STRENGTH_STRONG,
                                message: 'Ce mot de passe est trop faible. Utilisez une phrase de passe plus longue et moins prévisible.'
                            ),
                        ]
                    ),
                ])
                ->setFormTypeOption('empty_data', '')
                ->setHelp(
                    'Au moins 12 caractères. Privilégiez une phrase de passe longue et peu prévisible. '
                        . 'En modification, laissez ce champ vide pour conserver le mot de passe actuel.'
                )
                ->onlyOnForms(),
            DateTimeField::new('lastConnectionAt', 'Dernière connexion')
                ->hideOnForm()
                ->setFormat('dd MMM yyyy à HH:mm'),
            DateTimeField::new('createdAt', 'Créé le')
                ->hideOnForm()
                ->setFormat('dd MMM yyyy à HH:mm'),
            DateTimeField::new('updatedAt', 'Mis à jour le')
                ->hideOnForm()
                ->setFormat('dd MMM yyyy à HH:mm')
        ];
    }

    /**
     * Prépare un nouvel utilisateur actif avant l'application des données du formulaire de création.
     * Cette valeur initiale peut être changée dans le formulaire ; aucune écriture en base n'a lieu ici.
     */
    public function createEntity(string $entityFqcn): User
    {
        $user = new User();
        $user->setStatus(true); // Set the default status to true (active)
        return $user;
    }

    /**
     * Après validation du formulaire de création, hache le mot de passe saisi et renseigne la date de création.
     * Délègue ensuite à EasyAdmin la persistance et le flush, en conservant le statut choisi dans le formulaire.
     *
     * @throws \InvalidArgumentException Si aucun mot de passe n'est fourni.
     */
    public function persistEntity(EntityManagerInterface $entityManager, object $entityInstance): void
    {
        if ($entityInstance instanceof User) {
            $plainPassword = $entityInstance->getPassword();

            if ($plainPassword === null || $plainPassword === '') {
                throw new \InvalidArgumentException('Le mot de passe ne peut pas être vide.');
            }

            $entityInstance->setPassword(
                $this->passwordHasher->hashPassword($entityInstance, $plainPassword)
            )
                ->setCreatedAt(new \DateTimeImmutable());
        }

        parent::persistEntity($entityManager, $entityInstance);
    }

    /**
     * Enregistre les changements après validation du formulaire de modification.
     * Hache la nouvelle saisie ou restaure le hash original suivi par Doctrine si le champ est vide,
     * puis renseigne la date de modification et délègue le flush à EasyAdmin.
     * Ce traitement suppose que le champ password mappé a été soumis par le formulaire ;
     * il ne doit pas être réutilisé tel quel pour une action rapide qui ne soumet pas ce champ.
     */
    public function updateEntity(EntityManagerInterface $entityManager, object $entityInstance): void
    {
        if ($entityInstance instanceof User) {
            $plainPassword = $entityInstance->getPassword();

            if ($plainPassword !== null && $plainPassword !== '') {
                $entityInstance->setPassword(
                    $this->passwordHasher->hashPassword($entityInstance, $plainPassword)
                );
            } else {
                // If the password field is empty, we don't change the password
                $originalUser = $entityManager->getUnitOfWork()->getOriginalEntityData($entityInstance);
                if (isset($originalUser['password'])) {
                    $entityInstance->setPassword($originalUser['password']);
                }
            }

            $entityInstance->setUpdatedAt(new \DateTimeImmutable());
        }

        parent::updateEntity($entityManager, $entityInstance);
    }
}
