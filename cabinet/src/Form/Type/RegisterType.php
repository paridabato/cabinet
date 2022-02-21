<?php

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class RegisterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $data = $builder->getData();
        $builder
            ->add('FirstName', TextType::class, [
                'row_attr' => [
                    'class' => 'form-group'
                ],
                'label' => false,
                'attr' => [
                    'placeholder' => 'First Name',
                    'class' => 'form-control-user'
                ]
            ])
            ->add('LastName', TextType::class, [
                'row_attr' => [
                    'class' => 'form-group'
                ],
                'label' => false,
                'attr' => [
                    'placeholder' => 'Last Name',
                    'class' => 'form-control-user'
                ]
            ])
        ;
        if(!isset($data['ID'])) {
            $builder
                ->add('Email', EmailType::class, [
                    'row_attr' => [
                        'class' => 'form-group'
                    ],
                    'label' => false,
                    'attr' => [
                        'placeholder' => 'Email',
                        'class' => 'form-control-user'
                    ],
                    'disabled' => isset($data['ID'])
                ]);
        }
        if(isset($data['ID'])) {
            $builder
                ->add('Address', TextType::class, [
                    'row_attr' => [
                        'class' => 'form-group'
                    ],
                    'label' => false,
                    'attr' => [
                        'placeholder' => 'Address',
                        'class' => 'form-control-user'
                    ]
                ])
                ->add('City', TextType::class, [
                    'row_attr' => [
                        'class' => 'form-group'
                    ],
                    'label' => false,
                    'attr' => [
                        'placeholder' => 'City',
                        'class' => 'form-control-user'
                    ]
                ])
            ;
        }
        $builder
            ->add('IPCountry', CountryType::class, [
                'row_attr' => [
                    'class' => 'form-group'
                ],
                'label' => false,
                'attr' => [
                    'placeholder' => 'Country of residence',
                    'class' => 'form-control-user'
                ]
            ])
        ;
        if(isset($data['ID'])) {
            $builder
                ->add('Email', EmailType::class, [
                    'row_attr' => [
                        'class' => 'form-group'
                    ],
                    'label' => false,
                    'attr' => [
                        'placeholder' => 'Email',
                        'class' => 'form-control-user'
                    ],
                    'disabled' => isset($data['ID'])
                ])
            ;
        }
        $builder
            ->add('PhoneNumber', TelType::class, [
                'row_attr' => [
                    'class' => 'form-group'
                ],
                'label' => false,
                'attr' => [
                    'class' => 'form-control-user'
                ],
                'disabled' => isset($data['ID'])
            ])
        ;
        if(!isset($data['ID'])) {
            $builder
                ->add('Password', PasswordType::class, [
                    'row_attr' => [
                        'class' => 'form-group'
                    ],
                    'label' => false,
                    'attr' => [
                        'placeholder' => 'Password'
                    ]
                ])
            ;
        }
        $builder
            ->add('save', SubmitType::class, [
                'label' => !isset($data['ID'])?'Register Now':'Save profile data',
                'row_attr' => [
                    'class' => 'form-group'
                ],
                'attr' => [
                    'class' => 'btn btn-primary btn-user btn-block'
                ]
            ])
        ;
    }
}