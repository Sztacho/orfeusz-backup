<?php

namespace App\Controller\Admin\Crud;

use App\Controller\Admin\Crud\Trait\ActionTrait;
use App\Entity\Season;
use App\Repository\SeasonRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;

class SeasonCrud extends AbstractCrudController
{
    use ActionTrait;

    public function __construct(private readonly SeasonRepository $seasonRepository)
    {
    }

    public function configureActions(Actions $actions): Actions
    {
        return $this->configure($actions);
    }

    public static function getEntityFqcn(): string
    {
        return Season::class;
    }

    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        return parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters)
            ->orderBy('entity.year', 'DESC')
            ->addOrderBy('entity.sequence', 'DESC');
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $this->setSequence($entityManager, $entityInstance);
        parent::persistEntity($entityManager, $entityInstance);
    }

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $this->setSequence($entityManager, $entityInstance);
        parent::updateEntity($entityManager, $entityInstance);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            ChoiceField::new('season')
                ->setChoices(\App\Enum\Season::cases())
                ->renderAsBadges([
                    'winter' => 'primary',
                    'spring' => 'success',
                    'summer' => 'warning',
                    'fall' => 'danger',
                ]),
            NumberField::new('year')->setFormTypeOption('attr.maxlength', 4)->setFormTypeOption('attr.step', 1)->setNumDecimals(0),
            BooleanField::new('active'),
        ];
    }

    private function setSequence(EntityManagerInterface $entityManager, Season $entityInstance): void
    {
        $last = $this->seasonRepository->findOneBy(['active' => true]);
        if ($last && $entityInstance->isActive()) {
            $last->setActive(false);
            $entityManager->persist($last);
        }

        $entityInstance->setSequence(match ($entityInstance->getSeason()) {
            'winter' => 1,
            'spring' => 2,
            'summer' => 3,
            'fall' => 4,
        });
    }
}