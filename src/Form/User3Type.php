<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class User3Type extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username')
            ->add('email',EmailType::class)
            ->add('adresse')
            ->add('activite')
            ->add('telephone')
            ->add('roles',CollectionType::class,array(
                'entry_type'=>ChoiceType::class,
                'label'=>'Role',
                'disabled'=>true,
                'entry_options'=>[
                    'choices'  => [
                        'ROLE_Prospect'    => 'ROLE_Prospect']
                ]
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
