<?php
namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class LoginType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('Email', EmailType::class, [
                'row_attr' => [
                    'class' => 'form-group'
                ],
                'label' => false,
                'attr' => [
                    'placeholder' => 'Enter Email Address...',
                    'class' => 'form-control-user'
                ]
            ])
            ->add('Password', PasswordType::class, [
                'row_attr' => [
                    'class' => 'form-group'
                ],
                'label' => false,
                'attr' => [
                    'placeholder' => 'Password'
                ]
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Login',
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