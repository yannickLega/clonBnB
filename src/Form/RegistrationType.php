<?php

namespace App\Form;

use App\Entity\User;
use App\Form\ApplicationType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class RegistrationType extends ApplicationType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName', TextType::class, $this->getConfiguration("Prénom", "Renseignez votre prénom"))
            ->add('lastName', TextType::class, $this->getConfiguration("Nom", "Renseignez votre Nom"))
            ->add('email', EmailType::class, $this->getConfiguration("Email", "Renseignez votre email"))
            ->add('picture', UrlType::class, $this->getConfiguration("Photo de profil", "URL de votre avatar"))
            ->add('hash', PasswordType::class, $this->getConfiguration("Mot de passe", "Créez votre mot de passe"))
            ->add('passwordConfirm', PasswordType::class, $this->getConfiguration("Confirmation du mot de passe", "Veuillez confirmer votre mot de passe"))
            ->add('introduction', TextType::class, $this->getConfiguration("Introduction", "Présentez vous en quelques mots"))
            ->add('description', TextareaType::class, $this->getConfiguration("Description détaillée", "Présentez vous en détail"))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
