<?php

namespace App\Controller\Admin\Crud\Trait;

use Doctrine\Persistence\ManagerRegistry;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;

trait ActionTrait
{
    public function configure(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->update(Crud::PAGE_INDEX, Action::EDIT, function (Action $action) {
                return $action->setIcon('fa fa-pencil')->setLabel(false);
            })
            ->update(Crud::PAGE_INDEX, Action::DELETE, function (Action $action) {
                return $action->setIcon('fa fa-trash')->setLabel(false);
            })
            ->update(Crud::PAGE_INDEX, Action::DETAIL, function (Action $action) {
                return $action->setIcon('fa fa-list')->setLabel(false);
            });
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud->showEntityActionsInlined();
    }

    public function configureDetailsCustom($action, AdminUrlGenerator $adminUrlGenerator, string $crudController, string $label, string $icon, string $name = 'add'): Actions
    {
        return $this
            ->configure($action)
            ->add(
                Crud::PAGE_DETAIL,
                $this->getRelatingAction($adminUrlGenerator, $crudController, $label, $icon, $name)
            );
    }

    public function getRelatingAction(AdminUrlGenerator $adminUrlGenerator, string $crudController, string $label, string $icon, string $name = 'add'): Action
    {
        $action = Action::new($name, $label, $icon);

        $action->linkToUrl(fn(object $entity) => $adminUrlGenerator
            ->setController($crudController)
            ->setAction(Action::NEW)
            ->set('filter', ['relatedId' => $entity->getId()])
            ->setEntityId(null)
            ->generateUrl()
        );

        return $action;
    }

    public function getAssociationRelationField(ManagerRegistry $registry, string $propertyName, string $crudController, string $entity): AssociationField
    {
        $field = AssociationField::new($propertyName)
            ->setCrudController($crudController);

        if ($relatedId = $this->getContext()->getRequest()->query->all()['filter']['relatedId'] ?? 0) {
            $field->setFormTypeOption('data',
                $registry->getRepository($entity)->find($relatedId)
            );
        }

        return $field;
    }
}