<?php

namespace App\Controller\Admin\Crud;

use App\Controller\Admin\Crud\Trait\ActionTrait;
use App\Entity\Webhook;
use App\Enum\EventType;
use App\Enum\PlatformEvent;
use App\Enum\WebhookAction;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class WebhookCrud extends AbstractCrudController
{
    use ActionTrait;

    public static function getEntityFqcn(): string
    {
        return Webhook::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $this->configure($actions);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('name'),
            ChoiceField::new('action')
                ->setChoices([
                    'Send Notification' => WebhookAction::SEND_NOTIFICATION
                ]),
            ChoiceField::new('eventType')
                ->setChoices([
                    'Add Anime' => EventType::ANIME_ADD_EVENT->value,
                    'Edit Anime' => EventType::ANIME_EDIT_EVENT->value,
                    'Delete Anime' => EventType::ANIME_DELETE_EVENT->value,
                    'Add Episode' => EventType::ANIME_EPISODE_ADD_EVENT->value,
                    'Edit Episode' => EventType::ANIME_EPISODE_EDIT_EVENT->value,
                    'Delete Episode' => EventType::ANIME_EPISODE_DELETE_EVENT->value,
                ]),
            ChoiceField::new('platform')
                ->setChoices([
                    'Discord' => PlatformEvent::DISCORD,
                    'WebApp' => PlatformEvent::WEB,
                ]),
            TextField::new('url', 'Url'),
            BooleanField::new('active', 'isActive?')->hideOnIndex()
        ];
    }
}