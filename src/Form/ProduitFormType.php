<?php

namespace App\Form;

use App\Entity\Produit;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProduitFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('Nom', TextType::class, ['label' => 'Nom du produit', 'attr' => ['class' => 'form-control']])
            ->add('Description', TextareaType::class, ['label' => 'Description', 'required' => false, 'attr' => ['class' => 'form-control']])
            ->add('prix', MoneyType::class, ['label' => 'Prix', 'currency' => 'EUR', 'attr' => ['class' => 'form-control']])
            ->add('stock', IntegerType::class, ['label' => 'Stock', 'attr' => ['class' => 'form-control']]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => Produit::class]);
    }
}
