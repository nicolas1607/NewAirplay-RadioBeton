<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username', TextType::class, [
                'label' => 'Saisissez un nom d\'utilisateur'
            ])
            ->add('roles', ChoiceType::class, [
                'label' => 'Roles',
                'choices' => [
                    'bénévole' => 'ROLE_BENEVOLE',
                    'administrateur' => 'ROLE_ADMIN',
                    'super-administrateur' => 'ROLE_SUPERADMIN',
                ],
                'expanded' => false,
                'multiple' => false
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Mot de passe invalide',
                'first_options' => [
                        'label' => 'Choisissez un mot de passe',
                        'attr' => [
                            'class' => 'form-control mt-3'
                        ]
                ],
                'second_options' => [
                    'label' => 'Confirmez ce mot de passe',
                    'attr' => [
                        'class' => 'form-control mt-3'
                    ]
                ],
                'required' => true
            ])
            ->add('Ajouter', SubmitType::class)
        ;

        $builder->get('roles')
                ->addModelTransformer(new CallbackTransformer(
                    function ($rolesArray) {
                        return count($rolesArray)? $rolesArray[0]: null;
                    },
                    function ($rolesString) {
                        return [$rolesString];
                    }
                ));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
