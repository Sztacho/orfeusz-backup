<?php

namespace App\Controller\Admin\Crud;

use App\Controller\Admin\Crud\Trait\ActionTrait;
use App\Controller\Admin\Field\ImageRepositoryField;
use App\Entity\Anime;
use App\Enum\AgeRatingSystem;
use App\Repository\ImageRepository;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;

class AnimeCrud extends AbstractCrudController
{

    use ActionTrait;

    public function __construct(private readonly ImageRepository $imageRepository, private readonly AdminUrlGenerator $adminUrlGenerator)
    {
    }

    public static function getEntityFqcn(): string
    {
        return Anime::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $this->configureDetailsCustom($actions, $this->adminUrlGenerator, EpisodeCrud::class, 'Dodaj odcinek', 'fa fa-film');
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('name'),
            TextEditorField::new('description')->onlyOnIndex(),
            TextareaField::new('description')->hideOnIndex(),
            ChoiceField::new('ageRatingSystem')->setChoices(AgeRatingSystem::cases()),
            AssociationField::new('tags')->setCrudController(TagCrud::class),
            AssociationField::new('season')->setCrudController(SeasonCrud::class)->hideOnIndex(),
            TextField::new('season')->setCssClass('text-capitalize')->formatValue(function ($value) {
                $color = match (true) {
                    str_contains($value, 'spring') => 'badge-success',
                    str_contains($value, 'summer') => 'badge-warning',
                    str_contains($value, 'fall') => 'badge-danger',
                    str_contains($value, 'winter') => 'badge-primary',
                    default => '',
                };

                return '<span class="badge ' . $color . '">' . $value . '</span>';
            })->onlyOnIndex(),
            AssociationField::new('studios')->setCrudController(StudioCrud::class),
            AssociationField::new('translateBy')->setCrudController(UserCrud::class),
            ImageRepositoryField::new('image')->setImageRepository($this->imageRepository)->onlyOnForms(),
            ImageField::new('image')->hideOnForm(),
            DateField::new('releaseDate'),
            AssociationField::new('episodes')->onlyOnDetail()
        ];
    }
}