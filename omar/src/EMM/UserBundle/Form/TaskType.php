<?php

namespace EMM\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityRepository;
class TaskType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add('title')
                ->add('description')
                ->add('user','entity',array('class'=>'EMMUserBundle:User','query_builder'=>function(EntityRepository $er){
                            return $er->createQueryBuilder('u')
                                    ->where('u.role=:only')
                                    ->setParameter('only','ROLE_USER');
            
            
                },
                'choice_label'=>'getFullName'/*idea mostrar un selecbox, en ese campo mostramos el nombre compuesto*/
                    
                    
                    
                    
                    
                    ))
                 ->add('save','submit',array('label'=>'Save task'))        ;
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'EMM\UserBundle\Entity\Task'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'task';
    }


}
