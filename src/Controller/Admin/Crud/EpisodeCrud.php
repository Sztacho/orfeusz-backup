<?php

namespace App\Controller\Admin\Crud;

use App\Controller\Admin\Crud\Trait\ActionTrait;
use App\Controller\Admin\Field\ImageRepositoryField;
use App\Entity\Anime;
use App\Entity\Episode;
use App\Enum\EventType;
use App\Message\WebhookNotification;
use App\Repository\AnimeRepository;
use App\Repository\ImageRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AvatarField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\Messenger\MessageBusInterface;

class EpisodeCrud extends AbstractCrudController
{
    use ActionTrait;

    public function __construct(
        private readonly AnimeRepository     $animeRepository,
        private readonly ManagerRegistry     $registry,
        private readonly AdminUrlGenerator   $adminUrlGenerator,
        private readonly MessageBusInterface $bus,
        private readonly ImageRepository     $imageRepository
    )
    {
    }

    public static function getEntityFqcn(): string
    {
        return Episode::class;
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $entityInstance->setCreatedAt(new DateTimeImmutable());
        parent::persistEntity($entityManager, $entityInstance);
        $this->bus->dispatch(new WebhookNotification($entityInstance->getId(), $entityInstance, EventType::ANIME_EPISODE_ADD_EVENT));
    }

    public function configureActions(Actions $actions): Actions
    {
        return $this->configureDetailsCustom($actions, $this->adminUrlGenerator, VideoPlayerCrud::class, 'Dodaj player', 'fa fa-play');
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            FormField::addTab('Episode Manager')->setIcon('video'),
            $this->getAssociationRelationField($this->registry, 'anime', AnimeCrud::class, Anime::class),
            IntegerField::new('number'),
            TextField::new('title'),
            AssociationField::new('translateBy')->setCrudController(UserCrud::class),
            CollectionField::new('videoPlayers')->onlyOnDetail(),
            DateTimeField::new('createdAt')->onlyOnDetail(),
            TextEditorField::new('description'),
            ImageRepositoryField::new('image')->setImageRepository($this->imageRepository)->onlyOnForms(),
            ImageField::new('image')->onlyOnDetail(),
            FormField::addTab('Premiere Manager')->setIcon('video'),
            DateTimeField::new('premiereDate')->hideOnIndex(),
            BooleanField::new('premiere', 'Live chat'),
        ];
    }
}