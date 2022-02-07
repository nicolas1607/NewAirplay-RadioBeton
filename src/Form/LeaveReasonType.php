<?php

namespace App\Form;

use App\Entity\LeaveReason;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class LeaveReasonType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('sortie', TextType::class, [
                'label' => 'Sortie',
                'attr' => ['class' => 'mg-15 form-control']
            ])
            ->add('lib', TextType::class, [
                'label' => 'Libellé',
                'attr' => ['class' => 'mg-15 form-control']
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Ajouter',
                'attr' => ['class' => 'btn btn-outline-primary mt-3']
            ]);;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => LeaveReason::class,
        ]);
    }
}
