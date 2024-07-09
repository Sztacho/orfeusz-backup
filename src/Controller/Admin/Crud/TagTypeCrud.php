<?php

namespace App\Controller\Admin\Crud;

use App\Controller\Admin\Crud\Trait\ActionTrait;
use App\Entity\TagType;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class TagTypeCrud extends AbstractCrudController
{
    use ActionTrait;

    public function configureActions(Actions $actions): Actions
    {
        return $this->configure($actions);
    }

    public static function getEntityFqcn(): string
    {
        return TagType::class;
    }
}