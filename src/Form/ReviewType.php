<?php

namespace App\Form;

use App\Entity\Review;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReviewType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('rating', ChoiceType::class, [
                'choices' => [
                    '5 Stars ★★★★★' => 5,
                    '4 Stars ★★★★' => 4,
                    '3 Stars ★★★' => 3,
                    '2 Stars ★★' => 2,
                    '1 Star ★' => 1,
                ],
                'label' => 'Rating',
            ])
            ->add('comment', TextareaType::class, [
                'label' => 'Your Review',
                'attr' => [
                    'rows' => 4,
                    'placeholder' => 'Share your thoughts...',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Review::class,
        ]);
    }
}