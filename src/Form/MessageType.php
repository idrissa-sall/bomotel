<?php

namespace App\Form;

use App\Entity\Message;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class MessageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Prénom et Nom',
                'required' => true,
                'label_attr' => [
                    'class' => 'text-text-secondary font-semibold'
                ],
                'attr' => [
                    'placeholder' => 'Votre prénom et ou votre nom',
                    'class' => 'border border-text-secondary rounded-md text-text-secondary w-full bg-default p-1 focus:border-text-secondary focus:border-outline-2 focus:outline-offset-2 focus:outline-text-secondary'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Ce champ ne peut pas être vide.'
                    ]),
                    new Length([
                        'max' => 50,
                        'maxMessage' => 'Le nom ne peut pas dépasser {{ limit }} caractères.'
                    ])
                ]
            ])
            ->add('email', EmailType::class, [
                'label' => 'Adresse e-mail',
                'required' => true,
                'label_attr' => [
                    'class' => 'text-text-secondary font-semibold'
                ],
                'attr' => [
                    'placeholder' => 'Votre adresse e-mail',
                    'class' => 'border border-text-secondary rounded-md text-text-secondary w-full bg-default p-1 focus:border-text-secondary focus:border-outline-2 focus:outline-offset-2 focus:outline-text-secondary'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Ce champ ne peut pas être vide.'
                    ]),
                    new Length([
                        'max' => 50,
                        'maxMessage' => "L'adresse e-mail ne peut pas dépasser {{ limit }} caractères."
                    ])
                ]
            ])
            ->add('subject', TextType::class, [
                'label' => 'Sujet',
                'required' => true,
                'label_attr' => [
                    'class' => 'text-text-secondary font-semibold'
                ],
                'attr' => [
                    'placeholder' => 'Le sujet de votre message',
                    'class' => 'border border-text-secondary rounded-md text-text-secondary w-full bg-default p-1 focus:border-text-secondary focus:border-outline-2 focus:outline-offset-2 focus:outline-text-secondary'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Ce champ ne peut pas être vide.'
                    ]),
                    new Length([
                        'max' => 100,
                        'maxMessage' => 'Le sujet ne peut pas dépasser {{ limit }} caractères.'
                    ])
                ]
            ])
            ->add('msg', TextareaType::class, [
                'label' => 'Message',
                'required' => true,
                'label_attr' => [
                    'class' => 'text-text-secondary font-semibold'
                ],
                'attr' => [
                    'rows' => 5,
                    'placeholder' => 'Votre message',
                    'class' => 'border border-text-secondary rounded-md text-text-secondary w-full bg-default p-1 focus:border-text-secondary focus:border-outline-2 focus:outline-offset-2 focus:outline-text-secondary'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Ce champ ne peut pas être vide.'
                    ]),
                    new Length([
                        'max' => 500,
                        'maxMessage' => 'Le message ne peut pas dépasser {{ limit }} caractères.'
                    ])
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Message::class,
        ]);
    }
}
