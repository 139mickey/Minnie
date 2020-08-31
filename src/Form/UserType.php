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

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use App\Entity\Group;
use App\Entity\User;
/**
 * Defines the form used to create and manipulate blog posts.
 *
 * @author Ryan Weaver <weaverryan@gmail.com>
 * @author Javier Eguiluz <javier.eguiluz@gmail.com>
 */
class UserType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // For the full reference of options defined by each form field type
        // see http://symfony.com/doc/current/reference/forms/types.html

        // By default, form fields include the 'required' attribute, which enables
        // the client-side form validation. This means that you can't test the
        // server-side validation errors from the browser. To temporarily disable
        // this validation, set the 'required' attribute to 'false':
        //
        //     $builder->add('title', null, array('required' => false, ...));

        $builder
            //->add('id', HiddenType::class)
            ->add('fullName', TextType::class, array(
                'attr' => array('autofocus' => true),
                'label' => 'label.fullName',
            ))
            ->add('username', TextType::class, array(
                    'attr' => array('autofocus' => true),
                    'label' => 'label.username',
                ))
            ->add('email', EmailType::class, array(
                'label' => 'label.email',
            ))
            ->add('password', RepeatedType::class, array(
            		'type' => PasswordType::class,
            		'invalid_message' => 'The password fields must match.',
            		'options' => array('attr' => array('class' => 'password-field')),
                    'first_options'  => array('label' => 'label.password'),
                    'second_options' => array('label' => 'label.repeat_password'),
            		'required' => false
            ))
            ->add("groups", EntityType::class,
                        array(
                            //'property' => 'name',
                            'label' =>'用户组',
                            'class' => Group::class,
                            'attr'=>array("class"=>"select","style"=>"width: 100%;"),
                            'multiple' => true,
                            'expanded' => false,
                            'choice_label' => 'name',
                            'choice_name' => 'name',
                            //'choices'=>$groupsOfSys,
                            //'data'=>$groupsOfUser,
                            'required' => false,
                        )
                    );
        ;

        $groupsOfSys = $options["groupsOfSys"];
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($groupsOfSys) {
            /** @var User $user */
            $user = $event->getData();
            $form = $event->getForm();
            // $groupsOfUser = new ArrayCollection();

            $groupsOfUser = array();
            //get the group has roles
            if ($user && $user->getId() > 0) {
                // 获取该组具备的角色实体对象集
                unset($groupsOfUser);
                $groupsOfUser = $user->getGroups();
/*                $i  = 0;
                foreach($groupOfUser as $groupsOfUser) {
                    unset($groupOfUser[$i]);
                    $groupsOfUser[strtoupper($roles)] =$roles;
                    ++$i;
                }*/
            }


            //foreach($groupsOfSys as $key=>$role){
            //$copy = clone $groupsOfUser;
            $form->add("groups", EntityType::class,
                array(
                    //'property' => 'name',
                    'label' =>'用户组',
                    'class' => Group::class,
                    'attr'=>array("class"=>"select2","style"=>"width: 100%;"),
                    'multiple' => true,
                    'expanded' => false,
                    'choice_label' => 'name',
                    'choice_name' => 'name',
                    //'choices'=>$groupsOfSys,
                    //'data'=>$groupsOfUser,
                    'required' => false,
                )
            );
            //}
        });

        $builder->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) use ($groupsOfSys) {
            /** @var User $user */
            $user = $event->getData();
            $form = $event->getForm();
            if($user){
                // first, remove all group of this user.
                /** @var Group[] $oldGroups */
                /*
                $oldGroups = $user->getGroups();
                $user->removeGroups($oldGroups);
                foreach($oldGroups as $group){
                    $group->removeUser($user);
                }
*/
                /** @var Group[] $groupsOfUser */
                $groupsOfUser = $form->get("groups")->getData();
                foreach($groupsOfUser as $key=>$group){
                    $user->addGroup($group);
                    $group->addUser($user);
                }
                unset($groupsOfUser);
            }

        });

    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'groupsOfSys' => array(),
            'data_class' => 'App\Entity\User',
            'csrf_protection' => false
        ));

        $allowedTypes = array(
            'groupsOfSys' => 'array',
        );

        foreach ($allowedTypes as $option => $allowedType) {
            $resolver->addAllowedTypes($option, $allowedType);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        // Best Practice: use 'app_' as the prefix of your custom form types names
        // see http://symfony.com/doc/current/best_practices/forms.html#custom-form-field-types
        return 'app_user';
    }
}
