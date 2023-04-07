<?php

namespace App\Form;

use App\Entity\Product;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Image;

class ProductFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre du produit',
            ])
            ->add('descript', TextareaType::class, [
                'label' => 'Description du produit',
            ])
            ->add('color',TextType::class, [
                'label' => 'Couleur du produit',
            ])
            ->add('size', ChoiceType::class, [
                'label' => 'Taille',
                'choices' => [
                    'XS' => 'xs',
                    'S' => 's',
                    'M' => 'm',
                    'L' => 'l',
                    'XL' => 'xl'
                ],
            ])
            ->add('collection', ChoiceType::class, [
                'label' => 'Collection',
                'choices' => [
                    'Homme' => 'homme',
                    'Femme' => 'femme',
                    'Mixte' => 'mixte'
                ],
            ])
            ->add('photo', FileType::class, [
                'label' => 'Photo',
                'data_class' => null,
            ])
            ->add('price',TextType::class, [
                'label' => 'Prix du produit',
            ])
            ->add('stock',TextType::class, [
                'label' => 'Quantité en stock',
            ])
            ->add('submit', SubmitType::class, [
                'label' => "Ajouter",
                'validate' => false,
                'attr' => [
                    'class' => "d-block mx-auto my-3 btn btn-success col-3"
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
            'allow_file_upload' => true,
            'photo' => null,
        ]);
    }
}
