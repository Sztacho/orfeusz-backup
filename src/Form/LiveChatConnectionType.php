<?php

namespace App\Form;

use App\Entity\Episode;
use App\Entity\LiveChatConnection;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LiveChatConnectionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('connection')
            ->add('episode', EntityType::class, [
                'class' => Episode::class,
                'choice_label' => 'title',
                'choice_value' => function (?Episode $entity) {
                    return $entity ? $entity->getId() : '';
                },
            ])
        ;
    }

    public function getBlockPrefix(): string
    {
        return '';
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => LiveChatConnection::class,
            'csrf_protection' => false
        ]);
    }
}
