<?php

namespace App\Form;

use App\Entity\Transaction;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class RetraitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
           
            ->add('nomcompletRecepteur')
            ->add('telRecepteur')
            ->add('codeTransaction',TextType::class)
            ->add('montant')
            ->add('CNIrecepteur')
            ->add('statut')
            ->add('recevedAt')
            ->add('commissionRetrait')
            ->add('userRetrait')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Transaction::class,
        ]);
    }
}
