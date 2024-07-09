<?php

namespace App\Controller\Admin\Crud;

use App\Controller\Admin\Crud\Trait\ActionTrait;
use App\Entity\Episode;
use App\Entity\VideoPlayer;
use App\Enum\PlayerSource;
use Doctrine\Persistence\ManagerRegistry;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class VideoPlayerCrud extends AbstractCrudController
{
    use ActionTrait;

    public function __construct(private readonly ManagerRegistry $registry)
    {
    }

    public static function getEntityFqcn(): string
    {
        return VideoPlayer::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $this->configure($actions);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            $this->getAssociationRelationField($this->registry, 'episode', EpisodeCrud::class, Episode::class),
            ChoiceField::new('source')->setChoices(PlayerSource::cases()),
            TextField::new('iframe')->setLabel('iFrame source url')
        ];
    }
}