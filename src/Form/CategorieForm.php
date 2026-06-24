<?php
<<<<<<< HEAD
=======
<<<<<<< HEAD

namespace App\Form;

use App\Entity\Categorie;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
=======
>>>>>>> ef7cc5654fc803bae3e04cd492ab462c8f40373d
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\Categorie;
<<<<<<< HEAD
=======
>>>>>>> 43fbb94 (Initial project Symfony boutique)
>>>>>>> ef7cc5654fc803bae3e04cd492ab462c8f40373d

class CategorieForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
<<<<<<< HEAD
        $builder->add('nom');
=======
<<<<<<< HEAD
=======
        $builder->add('nom');
>>>>>>> 43fbb94 (Initial project Symfony boutique)
>>>>>>> ef7cc5654fc803bae3e04cd492ab462c8f40373d
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Categorie::class,
        ]);
    }
}
