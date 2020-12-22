<?php

namespace App\Form;

use App\Entity\Pays;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', \Symfony\Component\Form\Extension\Core\Type\TextType::class, [
                'attr' => [
                    'placeholder' => 'Nom et Prénom',
                    'label'=>'Nom et Prénom ',
                ]])
            ->add('password',PasswordType::class, [
                'attr' => [
                    'placeholder' => 'Mot de passe',
                    'label'=>'Mot de passe ',
                ]])
            ->add('pays', EntityType::class, array(
                'class' => Pays::class,
                'label'=>'Pays ',
            ))
            ->add('adresse',\Symfony\Component\Form\Extension\Core\Type\TextType::class, [
                'attr' => [
                    'placeholder' => 'Adresse',
                    'label'=>'Adresse ',
                ]])
            ->add('activite',\Symfony\Component\Form\Extension\Core\Type\TextType::class, [
                'attr' => [
                    'placeholder' => 'Activité',
                    'label'=>'Activité ',
                ]])
            ->add('telephone',TelType::class, [
                'attr' => [
                    'placeholder' => 'Numero de telephone',
                    'label'=>'Numero de telephone ',
                ]])
            ->add('email',EmailType::class, [
                'attr' => [
                    'placeholder' => 'Numero de telephone',
                    'label'=>'Numero de telephone ',
                ]])

        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
