<?php

namespace App\Form;

use App\Entity\Pays;
use App\Entity\Service;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ServiceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nomService')
            ->add('prix')
            ->add('pays', EntityType::class, array(
                'class' => Pays::class,
                'label' => false
            ))
            ->add('contrat', TextareaType::class, array(
                'label' => false,
            ))
            ->add('options', CollectionType ::class, [
                'entry_type' => OptionType::class,
                'entry_options' => ['label' => false],
                'allow_add'=>true,
                'label'=>'Ajouter Option :',
                'allow_delete'=>true,
                'prototype'=>true,
            ])
           ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Service::class,
        ]);
    }
}
