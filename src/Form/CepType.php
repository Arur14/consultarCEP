<?php
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CepType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        //adiciona os campos que vão ter no formulário
        $builder
            ->add('cep', TextType::class, [
                'label' => 'CEP: ',
                'attr' => ['placeholder' => 'Digite o CEP'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([]);
    }
}