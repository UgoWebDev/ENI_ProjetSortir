<?php

namespace App\Form;

use App\Entity\Campus;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
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
            ])
            ->add('dateDebut', DateType::class, [
                'html5' => true,
                'widget' => 'single_text',
                'label' => 'Entre',
            ])
            ->add('dateFin', DateType::class, [
                'html5' => true,
                'widget' => 'single_text',
                'label' => 'et ',
            ])
            ->add('isOrganisateur', CheckboxType::class, [
                'label'    => 'Sorties dont je suis l\'organisateur',
            ])
            ->add('isInscrit', CheckboxType::class, [
                'label'    => 'Sorties auxquelles je suis inscrit',
            ])
            ->add('isNotInscrit', CheckboxType::class, [
                'label'    => 'Sorties auxquelles je ne suis inscrit',
            ])
            ->add('isPassed', CheckboxType::class, [
                'label'    => 'Sorties passées',
            ]);

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
        ]);
    }
}
