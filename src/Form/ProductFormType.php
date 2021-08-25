<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Product;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('Name', TextType::class,
                [
                    'label' => 'Product Name',
                    'required' => true
                ])
            ->add('price', MoneyType::class,
                [
                    'currency' => 'USD'
                ])
            ->add('quantity', IntegerType::class)
            ->add('description', TextareaType::class)
            ->add('Image', FileType::class,
                [
                    'label' => 'Product Image',
                    'data_class' => null,
                    'required' => is_null($builder->getData()->getImage())
                ])
            ->add('category', EntityType::class,
                [
                    'class' => Category::class,
                    'choice_label' => 'name',
                    'multiple' => false,
                    'expanded' => true
                ]);

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
