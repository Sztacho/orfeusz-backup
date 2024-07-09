<?php

namespace App\Controller\Admin\Crud;

use App\Controller\Admin\Crud\Trait\ActionTrait;
use App\Entity\Studio;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class StudioCrud extends AbstractCrudController
{
    use ActionTrait;

    public function configureActions(Actions $actions): Actions
    {
        return $this->configure($actions);
    }


    public static function getEntityFqcn(): string
    {
        return Studio::class;
    }
}