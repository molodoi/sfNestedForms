<?php

namespace AppBundle\Form;

use AppBundle\Entity\City;
use AppBundle\Entity\Department;
use AppBundle\Entity\Region;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PostType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name')->add('region', EntityType::class, [
            'class'         => 'AppBundle\Entity\Region',
            'placeholder'   => 'Sélectionner votre région',
            'mapped'        => false,
            'required'      => false
        ]);
        $builder->get('region')->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event){
                  $form = $event->getForm();
                  $this->addDepartmentField($form->getParent(), $form->getData());
            }
        );
        $builder->addEventListener(
            FormEvents::POST_SET_DATA,
            function (FormEvent $event){
                $data = $event->getData();
                /* @var $city City */
                $city = $data->getCity();
                $form = $event->getForm();
                if($city){
                    $department = $city->getDepartment();
                    $region = $department->getRegion();
                    $this->addDepartmentField($form, $region);
                    $this->addCityField($form, $department);
                    // Populate form
                    $form->get('region')->setData($region);
                    $form->get('department')->setData($department);
                }else{
                    $this->addDepartmentField($form, null);
                    $this->addCityField($form, null);
                }
            }
        );
    }


    /**
     * Add department field to form
     * @param FormInterface $form
     * @param Region $region
     */
    private function addDepartmentField(FormInterface $form, ?Region $region){
        $builder = $form->getConfig()->getFormFactory()->createNamedBuilder(
            'department',
            EntityType::class,
            null,
            [
                'class'         => 'AppBundle\Entity\Department',
                'placeholder'   => $region ? 'Sélectionner votre département' : 'Sélectionner d\'abord la région',
                'mapped'        => false,
                'required'      => false,
                'auto_initialize' => false,
                //Seulement les départements de la région
                'choices'       => $region ? $region->getDepartments() : []
            ]
        );
        $builder->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event){
                $form = $event->getForm();
                $this->addCityField($form->getParent(), $form->getData());
            }
        );
        $form->add($builder->getForm());
    }


    /**
     * Add city field to form
     * @param FormInterface $form
     * @param Department $department
     */
    private function addCityField(FormInterface $form, ?Department $department){
        $form->add('city', EntityType::class,[
            'class' => 'AppBundle\Entity\City',
            'placeholder' => $department ? 'Sélectionner votre ville' : 'Sélectionner d\'abord votre département',
            'choices' => $department ? $department->getCities() : []
        ]);
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Post'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_post';
    }


}
