<?php

namespace App\Form;

// 
// definir drop down com o idioma da sessao, que afecta as traduções
//

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Contracts\Translation\TranslatorInterface;

class UserEditType extends AbstractType
{
    public function __construct(
        private TranslatorInterface $translator
    ) {}

    public function buildForm(
        FormBuilderInterface $builder,
        array $options
    ): void
    {
        $builder
            ->add('password', 
                PasswordType::class, [
                'constraints' => [new UserPassword()],
                'required' => true,
                'label' => $this->translator->trans('current_password')
            ])
            ->add('new_password', 
                PasswordType::class, [
                'constraints' => [new Assert\NotBlank()],
                'required' => true,
                'label' => $this->translator->trans('new_password')
            ])
            ->add('confirm_new_password', PasswordType::class, [
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\IdenticalTo([
                        'propertyPath' => 'parent.all[new_password].data',
                        'message' => $this->translator->trans('passwords_must_match')
                    ])
                ],
                'required' => true,
                'label' => $this->translator->trans('confirm_new_password')
            ])
            ->add('save', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'mapped' => false,
        ]);
    }
}
