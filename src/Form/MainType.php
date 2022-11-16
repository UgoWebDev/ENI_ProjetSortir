<?php

namespace App\Form;

use App\Entity\Campus;
use Doctrine\DBAL\Types\ArrayType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MainType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('siteOrganisateur', EntityType::class, [
                'class' => Campus::class,
                'choice_label' => 'nom',
                'label' => 'Campus',
            ])
            ->add('searchName', TextType::class, [
                'label' => 'Le nom de la sortie contient : ',
                'required' => false,
            ])
            ->add('dateDebut', DateType::class, [
                'html5' => true,
                'widget' => 'single_text',
                'label' => 'Entre ',
                'required' => false,
            ])
            ->add('dateFin', DateType::class, [
                'html5' => true,
                'widget' => 'single_text',
                'label' => 'et ',
                'required' => false,
            ])
            ->add('isOrganisateur', CheckboxType::class, [
                'label'    => 'Sorties dont je suis l\'organisateur',
                'required' => false,
            ])
            ->add('isInscrit', CheckboxType::class, [
                'label'    => 'Sorties auxquelles je suis inscrit',
                'required' => false,
            ])
            ->add('isNotInscrit', CheckboxType::class, [
                'label'    => 'Sorties auxquelles je ne suis inscrit',
                'required' => false,
            ])
            ->add('isPassed', CheckboxType::class, [
                'label'    => 'Sorties passées',
                'required' => false,
            ])
            ->add('search', SubmitType::class, [
                'label' => 'Rechercher !'
            ])
            ->add('create', SubmitType::class, [
                'label' => 'Créer une sortie !'
            ])
        ;

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
        ]);
    }
}
