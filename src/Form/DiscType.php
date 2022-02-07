<?php

namespace App\Form;

use App\Entity\Disc;
use App\Entity\Type;
use App\Entity\Genre;
use App\Entity\Language;
use App\Entity\LeaveReason;
use App\Entity\Nationality;
use App\Repository\DiscRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;


class DiscType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('album', TextType::class, [
                'label' => 'Album',
                'attr' => [
                    'class' => 'form-control'
                ],
            ])
            ->add('groupe', TextType::class, [
                'label' => 'Groupe',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('label', TextType::class, [
                'label' => 'Label',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('type', EntityType::class, [
                'class' => Type::class,
                'choice_label' => 'lib',
                'label' => 'Format de l\'album',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('genre', EntityType::class, [
                'class' => Genre::class,
                'choice_label' => function (Genre $genre) {
                    return $genre->getGenre() . ' - ' . $genre->getLib();
                },
                'label' => 'Genre musical',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('nationality', EntityType::class, [
                'class' => Nationality::class,
                'choice_label' => 'lib',
                'label' => 'Nationalité',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('language', EntityType::class, [
                'class' => Language::class,
                'choice_label' => 'lib',
                'label' => 'Langue',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('arrivalDate', DateType::class, [
                'label' => 'Date d\'arrivée',
                'widget' => 'single_text'
            ])
            ->add('leaveDate', DateType::class, [
                'label' => 'Date d\'entrée en playlist',
                'widget' => 'single_text',
                'required' => false
            ])
            ->add('leaveName', TextType::class, [
                'label' => 'Nom de l\'emprunteur',
                'attr' => [
                    'class' => 'form-control'
                ],
                'required' => false
            ])
            ->add('leaveReason', EntityType::class, [
                'class' => LeaveReason::class,
                'choice_label' => 'lib',
                'label' => 'Motif de sortie',
                'attr' => [
                    'class' => 'form-control'
                ],
                'required' => false
            ])
            ->add('concert', CheckboxType::class, [
                'label' => 'Concert',
                'required' => false,
            ])
            ->add('aucard', CheckboxType::class, [
                'label' => 'Aucard',
                'required' => false,
            ])
            ->add('ferarock', CheckboxType::class, [
                'label' => 'Férarock',
                'required' => false,
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Valider',
                'attr' => [
                    'class' => 'btn btn-outline-warning py-3 px-5'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Disc::class,
        ]);
    }
}
