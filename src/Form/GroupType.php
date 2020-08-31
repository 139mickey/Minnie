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

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
//use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use AppBundle\Entity\User;
use Symfony\Component\OptionsResolver\OptionsResolver;
use AppBundle\Entity\Group;

class GroupType extends AbstractType
{
    /**
     *
     * @var array
     */
    private $rolesOfApp=array();

    /**
     * GroupFormType constructor.
     * @param array $rolesOfApp
     * @throws \InvalidArgumentException

    public function __construct(array $rolesOfApp)
    {
        $this->rolesOfApp = $rolesOfApp;
    }
     */

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        /*
          $choices = array();
          // $objects = $this->entityManager->getRepository('AppBundle:Group')->findAll();
          / ** @var Query $query * /
    	$query = $this->entityManager->createQuery("select o from AppBundle:Role o");
    	$objects = $query->getResult(Query::HYDRATE_ARRAY);

    	//$objects = $query->getArrayResult();
    	foreach($objects as $arrItem){
   			$choices[$arrItem['name']]=$arrItem['raw_name'];
    	}
        $builder->add('name', null, array('label' => 'form.group_name', 'translation_domain' => 'FOSUserBundle'));
*/

        //a:2:{i:0;s:12:"ROLE_TEACHER";i:1;s:12:"ROLE_CHECKER";}

        //获取已注册的模块的所有角实体集
        $rolesOfApp = $options["app_roles"];

        $builder->add('name', TextType::class, array('label' => 'label.name'))
        ->add('description', TextareaType::class,
            array(
                'label' => 'label.description',
                'required'=>false,
            ));

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($rolesOfApp) {
            /** @var Group $group */
            $group = $event->getData();
            $form = $event->getForm();
           // $rolesOfGroup = new ArrayCollection();

            $rolesOfGroup = array();
            //get the group has roles
            if ($group && $group->getId() > 0) {
                // 获取该组具备的角色实体对象集
                unset($rolesOfGroup);
                $rolesOfGroup = $group->getRoles();
                $i  = 0;
                foreach($rolesOfGroup as $roles) {
                    unset($rolesOfGroup[$i]);
                    $rolesOfGroup[strtoupper($roles)] =$roles;
                    ++$i;
                }
            }


            //foreach($rolesOfApp as $key=>$role){
                //$copy = clone $rolesOfGroup;
                $form->add("roles", ChoiceType::class,
                    array(
                        //'property' => 'name',
                        'label' =>'权限',
                        'attr'=>array("class"=>"select2","style"=>"width: 100%;"),
                        'multiple' => true,
                        'expanded' => false,
                        'choices'=>$rolesOfApp,
                        'data'=>$rolesOfGroup,
                        'required' => false,
                    )
                );
            //}
        });

        $builder->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) use ($rolesOfApp) {
            /** @var Group $group */
            $group = $event->getData();
            $form = $event->getForm();
            if($group){
                // first, remove all role of this group.
                $group->setRoles(array());
                $rolesOfGroup = $form->get("roles")->getData();
                foreach($rolesOfGroup as $key=>$roles){
                        $group->addRole($roles);
                }
                unset($rolesOfGroup);
            }

        });
            /*
            $builder->add('user_in_groups', 'entity', array(
                'class' => 'AppBundle\Entity\Group',
            //    'data' => $this->user->getGroups(),
                'property' => 'name',
                'choice_value'=>'name',
                'choice_name'=> function ($group) {
                    return $group->getId();
                },
                //'choices_as_values'=>false,
                'label' => 'label.user_groups',
                'expanded' => true,
                'multiple' => true,
                'required' => false
            ));
            */
    }

    // BC for SF < 2.7
    /*
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $this->configureOptions($resolver);
    }
*/
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'app_roles' => array(),
            'data_class' => 'AppBundle\Entity\Group',
        ));

        $allowedTypes = array(
            'app_roles' => 'array',
        );

        foreach ($allowedTypes as $option => $allowedType) {
            $resolver->addAllowedTypes($option, $allowedType);
        }

    }

    // BC for SF < 3.0
    public function getName()
    {
        return $this->getBlockPrefix();
    }

    public function getBlockPrefix()
    {
        return 'app_user_group';
    }
}
