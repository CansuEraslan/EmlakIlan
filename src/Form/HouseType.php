<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\House;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class HouseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'title',
            ])

            ->add('title')
            ->add('keywords')
            ->add('description')
            ->add('image',FileType::class,[
                'label'=>'House Main Image',
                'mapped'=>false,
                'required'=>false,
                'constraints'=>[
                    new \Symfony\Component\Validator\Constraints\File([
                        'maxSize'=>'1024k',
                        'mimeTypes'=>[
                            'image/*',

                        ],
                        'mimeTypesMessage'=>'Please upload a valid Image File',
                    ])
                ],
            ])
            ->add('m2')
            ->add('kat')
            ->add('oda', ChoiceType::class,[
                'choices'=>[
                    '1 oda'=>'1',
                    '2 oda'=>'2',
                    '3 oda'=>'3',
                    '4 oda'=>'4',
                    '5 oda'=>'5',
                    '6 oda'=>'6',
                ],
            ])
            ->add('banyo')
            ->add('mutfak')
            ->add('address')
            ->add('phone')
            ->add('fax')
            ->add('email')
            ->add('price')
            ->add('city',ChoiceType::class,[
                'choices'=>[
                    'Ankara'=>'Ankara',
                    'Istanbul'=>'Istanbul',
                    'Antalya'=>'Antalya',
                    'İzmir'=>'İzmir',
                    'Paris'=>'Paris',
                    'Moscow'=>'Moscow'],
            ])
            ->add('country',ChoiceType::class,[
                'choices'=>[
                    'Turkiye'=>'Turkiye',
                    'Spain'=>'Spain',
                    'Italy'=>'Italy',
                    'Russia'=>'Russia',
                    'France'=>'France',
                    'Greece'=>'Greece'],
            ])

            ->add('location')
            ->add('detail',CKEditorType::class, array(
                'config'=> array(
                    'uiColor'=>'#ffffff',
                ),
            ))
            ->add('status', ChoiceType::class,[
                'choices'=>[
                    'True'=>'True',
                    'False'=>'False'],

            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => House::class,
        ]);
    }
}
