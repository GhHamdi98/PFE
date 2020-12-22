<?php

namespace App\Form;

use App\Entity\Demande;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DemandeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nature_projet')
            ->add('fonctionalite_projet')
            ->add('principale_projet')
            ->add('site_similaire')
            ->add('module_b2c')
            ->add('module_b2b')
            ->add('langue_projet')
            ->add('couleur_prefere')
            ->add('logo')
            ->add('charte_graphique')
            ->add('echeance')
            ->add('details')
            ->add('prospect')
            ->add('commerciale')
            ->add('partenaire')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Demande::class,
        ]);
    }
}
