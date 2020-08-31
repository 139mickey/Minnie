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
use App\Entity\Article;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
// use App\Form\Type\TagsInputType;
// use App\Form\Type\CKFinderFileChooserType;
// use CKSource\Bundle\CKFinderBundle\Form\Type\CKFinderFileChooserType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\CallbackTransformer;

/**
 * Defines the form used to create and manipulate blog posts.
 *
 * @author Ryan Weaver <weaverryan@gmail.com>
 * @author Javier Eguiluz <javier.eguiluz@gmail.com>
 * @author Yonel Ceruto <yonelceruto@gmail.com>
 */
class ArticleType extends AbstractType
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
            ->add('title', null, [
                'attr' => ['autofocus' => true],
                'label' => 'label.title',
                // 'help' => 'label.post_title_help',
            ])
            ->add('category', EntityType::class, array(
                'label' => 'label.category',
                'class' => Category::class,
                'choice_label' => 'name',
                'choice_name' => 'name',
            ))
            ->add('summary', TextareaType::class, [
                'label' => 'label.summary',
                // 'help' => 'label.post_summary_help',
            ])
            ->add('content', null, [
                'attr' => ['rows' => 150, 'class'=>'wyswyg'],
                // 'help' => 'label.post_content_help',
                'label' => 'label.content',
            ])
            ->add('publishedAt', DateTimeType::class, [
                'label' => 'label.published_at',
                'widget' => 'single_text',
                //'input'=>'string',
                // 'format'=>'Y-m-d H:i:s',
                //'input_format' => 'yyyy-MM-dd\'T\'HH:mm:ssZZZZZ',
               // 'format' => 'yyyy-MM-dd\'T\'HH:mm:ssZZZZZ',
                'html5'=>false
            ])
            /*
            ->add('tags', TagsInputType::class, [
                'label' => 'label.tags',
                // 'help' => 'label.post_tags_help',
                'required' => false,
            ])
            */
            ->add('coverImage', TextType::class,
                array(
                    'label' => 'label.image',
                    // 'help' => 'label.post_image_help',
                    //"mapped"=>false,
                    "data_class"=>null,
                    "required"=>false,
                ))
            /*
            ->add('file_chooser1', CKFinderFileChooserType::class, [
                'label' => 'Photo',
                'required' => false,
                'button_text' => 'Browse photos',
                'button_attr' => [
                    'class' => 'my-fancy-class'
                ]
            ])
                         */
            ->add('frontPage', CheckboxType::class, array(
                'label'    => 'label.push_to_front',
                'required' => false,
            ))
        ;
/*
        $builder->get('publishedAt')
            ->addModelTransformer(new CallbackTransformer(
                function ($datetimeToString) {
                    // transform “model data” => “norm data”
                    $format = "Y-m-d H:i:s";
                    $datetime = \DateTime::createFromFormat($format,$datetimeToString );
                    //$strDateTime = $datetimeToString->format("yyyy-MM-dd'T'HH:mm:ssZZZZZ");
                    return $datetime;//implode(', ', $tagsAsArray);
                },
                function ($stringToDatetime) {
                    // transform “norm data” => “model data”
                    $format = "Y-m-d H:i:s";
                    $datetime = \DateTime::createFromFormat($format,$stringToDatetime );
                    return $datetime;//explode(', ', $tagsAsString);
                }
            ))
        ;
*/
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
            'csrf_protection' => false
        ]);
    }
}
