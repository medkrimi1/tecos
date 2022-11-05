<?php

namespace App\Form;
use App\Entity\Professions;
use App\Data\SearchDataCandidate;
use App\Entity\Candidatexp;
use App\Entity\Skills;
use App\Entity\Candidates;
use App\Entity\Country;
use App\Entity\Applications;
use App\Entity\Jobs;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;


class SearchFormCandidate extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('s', TextType::class , [
            'label' => false ,
            'required' => false ,
            'attr' => [
                'placeholder' =>'Rechercher' ,
             'csrf_protection' => false


            ]
       ])
             ->add('mail', TextType::class , [
            'label' => false ,
            'required' => false ,
            'attr' => [
                'placeholder' =>'Rechercher' ,
             'csrf_protection' => false


            ]
       ])
             ->add('Jobs', EntityType::class, [
               'class' => Jobs::class,
               
               'required'=> false,
                  'query_builder' => function (EntityRepository $er) {
              return $er->createQueryBuilder('u')
             ->Where('u.id != :check')
              ->setParameter('check', 609679);
    },
             
            ])

          ->add('Experience', EntityType::class , [
            'label' => false ,
            'required' => false ,
            'class' => Candidatexp::class,
            'multiple' => true


        
            
       ])

           ->add('Skills', EntityType::class , [
            'label' => false ,
            'required' => false ,
            'class' => Skills::class,
            'multiple' => true,
          

            
       ])

             ->add('Country', EntityType::class , [
            'label' => false ,
            'required' => false ,
            'class' => Country::class,
            'multiple' => true

            
       ])

                ->add('dfiltre', CheckboxType::class, [
                'label' => 'Activer/Désactiver ',
                'required' => false,
            ])
                ->add('startdate', DateType::class, [
    
    
    'attr' => ['class' => 'js-datepicker'],
    'label' => false ,
     'format' => 'ddMMyyyy',
 'required' => true ,
    'input' => 'string',
    'input_format' => 'Y-m-d' // ajouté en 4.3
])
                   ->add('enddate', DateType::class, [
    
   
    'attr' => ['class' => 'js-datepicker'],
    'label' => false ,
    'format' => 'ddMMyyyy',
    'required' => true ,
    'input' => 'string'])
     
          
             
        ;
    }

   public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => SearchDataCandidate::class,
            'method' => 'GET',
            'csrf_protection' => false
        ]);
    }


    public function getblockPrefix()

    {
       return '' ;

    }
}
