<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Url;

class NoteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('type', ChoiceType::class, [
                'required'  => true,
                'choices' => [
                    'note',
                    'link',
                ],
                'constraints' => [
                    new NotBlank(),
                ],
            ]);

        $formModifier = function(FormInterface $form, $note = '')
        {
            if($note === 'note'){
               $form->add('title', TextType::class, [
                   'required'   => true,
                   'constraints' => [
                       new NotBlank(),
                   ],
               ])
                   ->add('content', TextType::class, [
                       'required' => true,
                        'constraints' => [
                            new NotBlank(),
                        ],
                   ]);
            }
            elseif ($note === 'link'){
                $form->add('content', UrlType::class, [
                    'required'   => true,
                    'constraints' => [
                        new NotBlank(),
                        new Url([
                            'checkDNS' => true,
                        ]),
                    ],
                ]);
            }
        };

        $builder->get('type')->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) use ($formModifier) {
                // It's important here to fetch $event->getForm()->getData(), as
                // $event->getData() will get you the client data (that is, the ID)
                $note = $event->getForm()->getData();

                // since we've added the listener to the child, we'll have to pass on
                // the parent to the callback functions!
                $formModifier($event->getForm()->getParent(), $note);
            }
        );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Note',
        ));
    }

    public function getBlockPrefix()
    {
        return 'note_form';
    }
}