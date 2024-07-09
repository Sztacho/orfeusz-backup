<?php

namespace App\Controller\Admin\Field;


use App\Repository\ImageRepository;
use EasyCorp\Bundle\EasyAdminBundle\Field\FieldTrait;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;

class ImageRepositoryField implements FieldInterface
{
    use FieldTrait;

    public const OPTION_CHOICES = 'choices';

    public static function new(string $propertyName, ?string $label = null): ImageRepositoryField
    {
        return (new self())
            ->setProperty($propertyName)
            ->setLabel($label)
            ->setTemplateName('crud/field/choice')
            ->setFormType(ChoiceType::class)
            ->addCssClass('field-select')
            ->hideOnIndex();
    }

    public function setImageRepository(ImageRepository $imageRepository): ImageRepositoryField
    {
        $this
            ->setFormTypeOption(self::OPTION_CHOICES, $this->getImagesList($imageRepository))
            ->setDisabled(empty($this->getImagesList($imageRepository)))
            ->setHelp(empty($this->getImagesList($imageRepository))? 'Repozytorium obrazów jest puste, najpierw dodaj jakiś obraz.' : '');

        return $this;
    }

    private function getImagesList(ImageRepository $repository): array
    {
        foreach ($repository->findAll() as $item) {
            $images[$item->getName()] = $item->getImage();
        }

        return $images ?? [];
    }
}