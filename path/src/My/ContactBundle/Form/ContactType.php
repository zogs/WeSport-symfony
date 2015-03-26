<?php

namespace My\ContactBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Security\Core\SecurityContext;

use My\ContactBundle\Exception\RobotUsingContactFormException;

class ContactType extends AbstractType
{

    private $user;

    public function __construct(SecurityContext $security)
    {
        $this->user = $security->getToken()->getUser();
    }
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $date = new \Datetime('now');
        $builder
            ->add('email','email',array(
                'label' => "Votre email",
                'attr' => array(
                    'placeholder' => "Email",
                    )
                ))
            ->add('title','text',array(
                'label' => 'Sujet du message',     
                'attr' => array(
                    'placeholder' => "Sujet"
                    )         
                ))
            ->add('message','textarea',array(
                'label'=>'Contenu du Message',
                'attr' => array(
                    'placeholder' => "Message",
                    )
                ))
            //security field            
            ->add('date','hidden',array(
                'data'=> $date->format('Y-m-d H:i:s')
                ))
            //hidden field that must be empty
            ->add('login','hidden',array(
                'data'=>'',
                'mapped'=> false,
                'required'=>false
                ))           
        ;


        $builder->addEventListener(FormEvents::PRE_SUBMIT, array($this, 'onPreSubmit'));
        $builder->addEventListener(FormEvents::SUBMIT, array($this, 'onSubmit'));
    }

    /*
        On submit, perform some security check
    */
    public function onPreSubmit(FormEvent $event)
    {
        $form = $event->getForm();
        $data = $event->getData();

        //the login field must be empty
        if(!empty($data['login'])) {
            throw new RobotUsingContactFormException('The login trap field must be empty. Somebody is trying to submit the contact form the wrong way ( '.(isset($_SERVER['REMOTE_ADDR'])? $_SERVER['REMOTE_ADDR'] : "-REMOTE_ADDR undefined-").' is probably a robot!)');
        }

        //second to fill the form must be > 2 s
        $now = new \Datetime('now');
        $old = \DateTime::createFromFormat('Y-m-d H:i:s',$data['date']);

        $interval = $now->diff($old);
        $seconds = $interval->format('%s');

        if($seconds <=2){
            throw new RobotUsingContactFormException('The form have been submitted in '.$seconds.'s... Too fast for human, you are probably a robot ! (IP:'.(isset($_SERVER['REMOTE_ADDR'])? $_SERVER['REMOTE_ADDR'] : "-REMOTE_ADDR undefined-").'))');
        }        


    }

    public function onSubmit(FormEvent $event)
    {
        $form = $event->getForm();
        $data = $event->getData();

        if( null !== $this->user) {
            $data->setUser($this->user);
        }

    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'My\ContactBundle\Entity\Contact',
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'contact_form';
    }
}
