<?php

namespace App\Form;

use App\Entity\Post;
use App\Entity\Tag;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Image;

class PostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label'    => 'Nom',
                'required' => true
            ])
            ->add('content', TextareaType::class, [
                'label'    => 'contenu',
                'required' => true
            ])
            ->add(
                'tags',
                EntityType::class,
                [
                    'class'        => Tag::class,
                    'choice_label' => 'name',
                    'label'        => 'Tags',
                    'multiple'     => true,
                    'expanded'     => true,
                    'required'     => false,
                    'by_reference' => false
                ]
            )
            ->add('imageName', FileType::class, [
                'label'       => 'Image',
                'required'    => false,
                'data_class' => null,
                'constraints' => [
                    new Image([
                        'mimeTypes'        => [
                            'image/*',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid Image',
                    ])
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Post::class,
            'toto'       => false
        ]);
        $resolver->setAllowedTypes('toto', 'bool');
    }
}
