<?php

namespace My\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class AvatarType extends AbstractType
{

        public function buildForm(FormBuilderInterface $builder, array $options)
        {
                $now = new \DateTime('now');

                $builder
                        ->add('file','file',array(
                        	'required'=> false,
                        	'label'=> "Votre avatar",
                        	))
                        ->add('updated','hidden',array(
                            'data'=> $now->format('Y-m-d H:i:s')
                            ))
                        ;
        }

        public function setDefaultOptions(OptionsResolverInterface $resolver)
        {
            $resolver->setDefaults(array(
                    'data_class' => 'My\UserBundle\Entity\Avatar',
            ));
        }

        public function getName()
        {
                return 'avatar_type';
        }
}
