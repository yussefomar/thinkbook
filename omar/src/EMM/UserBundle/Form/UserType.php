<?php

namespace EMM\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('username')
                ->add('firstName')
                ->add('lstaName')
                ->add('email','email')/*tipo email*/
                ->add('password','password')
                ->add('role','choice',array('choices'=>array('ROLE_ADMIN'=>'administrador','ROLE_USER'=>'User'),'placeholder'=>'Select a role'))/*para mostrar que opcion poner*/
                ->add('isActive','checkbox')
                ->add('createdAt')/* esto queremos que se geener de manera automaticapor lo tanto se hara por afuera del formulario*/
                ->add('updatedAt')      
                ->add('save','submit',array('label'=>'Save user'))   ;
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'EMM\UserBundle\Entity\User'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()/*el nombre del formulario que va a tomar*/
    {
        return 'user';
    }


}
