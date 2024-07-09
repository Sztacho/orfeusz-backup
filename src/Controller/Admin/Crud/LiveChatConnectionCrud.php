<?php

namespace App\Controller\Admin\Crud;

use App\Entity\LiveChatConnection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class LiveChatConnectionCrud extends AbstractCrudController
{

    public static function getEntityFqcn(): string
    {
        return LiveChatConnection::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        $actions->remove(Crud::PAGE_INDEX, Action::NEW);
        $actions->remove(Crud::PAGE_NEW, Action::SAVE_AND_ADD_ANOTHER);
        return parent::configureActions($actions);
    }
}