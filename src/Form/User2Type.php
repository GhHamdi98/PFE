<?php

namespace App\Form;

use App\Entity\Pays;
use App\Entity\Role;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class User2Type extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username',TextType::class, [
                'attr' => [
                    'placeholder' => 'Nom et Prénom',
                    'label'=>'Nom et Prénom ',
                ]])
            ->add('password',PasswordType::class, [
                'attr' => [
                    'placeholder' => 'Mot de passe',
                    'label'=>'Mot de passe ',
                ]])
            ->add('email',EmailType::class, [
                'attr' => [
                    'placeholder' => 'Email',
                    'label'=>'Email ',
                ]])
                ->add('pays', EntityType::class, array(
                'class' => Pays::class,
                'label'=>'Pays ',
            ))
            ->add('adresse',TextType::class, [
                'attr' => [
                    'placeholder' => 'Adresse',
                    'label'=>'Adresse ',

                ]])
            ->add('activite',TextType::class, [
                'attr' => [
                    'placeholder' => 'Activité',
                    'label'=>'Activité ',

                ]])
            ->add('telephone',TelType::class, [
                'attr' => [
                    'placeholder' => 'Numero de telephone',
                    'label'=>'Numero de telephone ',

                ]])
            ->add('roles',CollectionType::class,array(
                'entry_type'=>ChoiceType::class,
                'prototype_data'=>'Numero de telephone',
                'label'=>'Role',
                'entry_options'=>[
                    'choices'  => [
                        'Administrateur' => 'ROLE_ADMIN',
                        'Partenaire'     => 'ROLE_PARTENAIRE',
                        'Commerciale'    => 'ROLE_COMMERCIALE',
                        'Prospect'    => 'ROLE_PROSPECT']
                ]
            ))
            ->add('level', EntityType::class, array(
                'class' => User::class,
                'label'=>'Affecté a ',
                'query_builder' => function (UserRepository $er) {
                    return $qb = $er->createQueryBuilder('s')
                        ->where('s.roles LIKE \'%"ROLE_PARTENAIRE"%\'');
                }
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
