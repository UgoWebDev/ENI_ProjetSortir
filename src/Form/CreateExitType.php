<?php

namespace App\Form;


use App\Entity\Campus;
use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Entity\Ville;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CreateExitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom de la sortie :'
            ])
            ->add('dateHeureDebut', DateTimeType::class,[
                'html5' => true,
                'widget' => 'single_text',
                'label' => 'Date et heure de la sortie :'
            ])
            ->add('dateLimiteInscription', DateType::class, [
                'html5' => true,
                'widget' => 'single_text',
                'label' => 'Date limite d\'inscription :',
            ])
            ->add('nbInscriptionsMax', null, [
                'label' => 'Nombre de places :'
            ])
            ->add('duree', null, [
                'label' => 'Durée :'
            ])
            ->add('infosSortie', TextareaType::class, [
                'label' => 'Description et infos :'
            ])
            ->add('siteOrganisateur', EntityType::class, [
                'label' => 'Campus :',
                'class' => Campus::class,
                'choice_label' => 'nom',
            ])
            ->add('ville', EntityType::class,[
                'class' => Ville::class,
                'choice_label' => 'nom',
                'placeholder' => 'Sélectionner la ville de la sortie',
                'label' => 'Ville :',
                'mapped' => false,
            ]);

        $formModifier = function (FormInterface $form, Ville $ville = null, Lieu $lieu = null) {
            $lieux = null === $ville ? [] : $ville->getNom();
            $rues = null === $lieu ? [] : $lieu->getNom();
            $codePostal = null === $ville ? [] : $ville->getCodePostal();

            $form->add('lieu', EntityType::class, [
                'class' => Lieu::class,
                'choice_label' => 'nom',
                'placeholder' => 'Sélectionner le lieu de la sortie',
                'label' => 'Lieu :',
                'choices' => $lieux,
            ])
                ->add('rue', EntityType::class, [
                    'class' => Lieu::class,
                    'choice_label' => 'rue',
                    'placeholder' => 'affichage de la rue',
                    'label' => 'Rue :',
                    'choices' => $rues,
                    'mapped' => false,
                ])
                ->add('codePostal', EntityType::class, [
                    'class' => Ville::class,
                    'choice_label' => 'codePostal',
                    'placeholder' => 'affichage du code postal',
                    'label' => 'Code Postal :',
                    'choices' => $codePostal,
                    'mapped' => false,
                ]);;
        };

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function(FormEvent $event) use ($formModifier) {

                $data = $event->getData();

                $formModifier($event->getForm(), $data->getNom());
            }
        );

        $builder->get('ville')->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) use ($formModifier) {
                $ville = $event->getForm()->getData();
                $lieu = $event->getForm()->getData();

                $formModifier($event->getForm()->getParent(), $ville, $lieu);
            }

        );
    }
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}
