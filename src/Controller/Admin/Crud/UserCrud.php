<?php

namespace App\Controller\Admin\Crud;

use App\Controller\Admin\Crud\Helpers\RoleBadgeConfiguration;
use App\Controller\Admin\Crud\Trait\ActionTrait;
use App\Entity\User;
use App\Repository\EpisodeRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserCrud extends AbstractCrudController
{
    use ActionTrait;

    public function __construct(
        private readonly UserPasswordHasherInterface $hasher,
        private readonly EpisodeRepository           $episodeRepository,
        private readonly UserRepository              $userRepository
    )
    {
    }

    public function configureActions(Actions $actions): Actions
    {
        return $this->configure($actions);
    }

    public function configureFields(string $pageName): iterable
    {
        yield FormField::addPanel('User General Manager')->setIcon('user')->onlyOnDetail();
        yield FormField::addTab('User General Manager')->setIcon('user')->onlyOnForms();
        yield EmailField::new('email');

        yield TextField::new('password')->setFormType(PasswordType::class)->onlyOnForms()->setRequired(false);

        yield TextField::new('username');
        yield TextField::new('nickname');
        yield AssociationField::new('episodes', 'Episode translated')->hideOnForm();

        yield AssociationField::new('episodes', 'Translated this mount')
            ->formatValue(function ($value, $entity) {
                return $this->episodeRepository->countTranslatedEpisodesInCurrentMonthForUser($entity) ?? 0;
            })
            ->onlyOnDetail();

        yield AssociationField::new('articles', 'Count of added articles')
            ->onlyOnDetail();

        yield FormField::addPanel('Role Manager')->setIcon('user')->onlyOnDetail();
        yield FormField::addTab('Role Manager')->setIcon('user')->onlyOnForms();

        $roles = array();
        $hierarchy = $this->getParameter('security.role_hierarchy.roles');

        array_walk_recursive($hierarchy, function ($role) use (&$roles) {
            $roles[$role] = $role;
        });

        yield ChoiceField::new('roles')
            ->setChoices($roles)
            ->allowMultipleChoices()
            ->hideOnIndex()->renderAsBadges(RoleBadgeConfiguration::getRoleBadges());
    }

    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $entityInstance->setPassword($this->hasher->hashPassword($entityInstance, $entityInstance->getPassword()));

        parent::persistEntity($entityManager, $entityInstance);
    }

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if (preg_match('/^\$2y\$/', $entityInstance->getPassword()) !== false) {
            parent::updateEntity($entityManager, $entityInstance);
        }

        $entityInstance->setPassword($this->hasher->hashPassword($entityInstance, $entityInstance->getPassword()));

        parent::updateEntity($entityManager, $entityInstance);
    }
}