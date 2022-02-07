<?php

namespace App\Form;

use App\Entity\Playlist;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class SearchPlaylistType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom de la playlist',
                'required' => false,
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('animator', TextType::class, [
                'label' => 'Animateur',
                'required' => false,
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('entry_date', DateType::class, [
                'label' => 'Date d\'arrivée',
                'required' => false,
                'widget' => 'single_text'
            ])
            ->add('rechercher', SubmitType::class, [
                'attr' => ['class' => 'btn btn-outline-warning py-3 px-5']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Playlist::class,
        ]);
    }
}
