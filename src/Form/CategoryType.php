<?php

/*
* This file is part of the MyCMS package.
*
* (c) ZhangBing <550695@qq.com>
*
* Date: 2018/11/26
* Time: 8:40
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace App\Form;

use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
/**
 * Defines the form used to create and manipulate blog posts.
 *
 * @author Ryan Weaver <weaverryan@gmail.com>
 * @author Javier Eguiluz <javier.eguiluz@gmail.com>
 * @author Yonel Ceruto <yonelceruto@gmail.com>
 */
class CategoryType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // For the full reference of options defined by each form field type
        // see https://symfony.com/doc/current/reference/forms/types.html

        // By default, form fields include the 'required' attribute, which enables
        // the client-side form validation. This means that you can't test the
        // server-side validation errors from the browser. To temporarily disable
        // this validation, set the 'required' attribute to 'false':
        // $builder->add('title', null, ['required' => false, ...]);

        $builder
            ->add('id', HiddenType::class)
            ->add('name', null, [
                'attr' => ['autofocus' => true],
                'label' => 'label.title',
                //'help' => 'Input a name of category.',
            ])
            ->add('parent', EntityType::class, array(
                'label' => 'label.category',
                // looks for choices from this entity
                'class' => Category::class,
                'choice_label' => 'name',
                'choice_name' => 'name',
                'placeholder' => 'label.no_category',
                'required' => false,
                'label' => 'label.category',
            ))
            ->add('description', TextareaType::class,
                array(
                    'attr' => ['rows' => 10],
                    'label' => 'label.description'
                ))
            ->add('sort', NumberType::class, array(
                'label' => 'label.sort',
                'required' => false,
                'attr' => array(
                    'class' => 'validate',
                    'id' => 'icon_telephone'
                )))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Category::class,
            'csrf_protection' => false
        ]);
    }
}
