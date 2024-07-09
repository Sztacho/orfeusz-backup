<?php

namespace App\Controller\Admin\Crud;

use App\Controller\Admin\Crud\Trait\ActionTrait;
use App\Entity\Comment;
use App\Entity\Tag;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class CommentCrud extends AbstractCrudController
{
    use ActionTrait;

    public function configureActions(Actions $actions): Actions
    {
        return $this->configure($actions)
            ->disable(Action::NEW)
            ->remove(Crud::PAGE_DETAIL, Action::EDIT)
            ->remove(Crud::PAGE_INDEX, Action::EDIT);
    }

    public static function getEntityFqcn(): string
    {
        return Comment::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id');
        yield TextEditorField::new('content');
        yield TextField::new('nickname')->hideOnForm();
        yield EmailField::new('email')->hideOnForm();
        yield AssociationField::new('anime')->hideOnForm();
        yield BooleanField::new('isActive');
    }
}