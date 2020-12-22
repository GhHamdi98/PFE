<?php

namespace App\Form;

use App\Entity\Demande;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Demande1Type extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('partenaire',EntityType::class, array(
                'class' => User::class,
                'choice_label'=>'username',
                'mapped'=>false,
                'required'=>false,
                'placeholder'=>'Choisir partenaire...',
                'query_builder' => function (UserRepository $er) {
                    $qb = $er->createQueryBuilder('s')
                        ->where('s.roles LIKE :role')
                        ->setParameter('role','%"ROLE_PARTENAIRE"%');
                    return $qb;
                }
            ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Demande::class,
        ]);
    }
}
