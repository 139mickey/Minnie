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

namespace App\Entity;

use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Serializer\Annotation\MaxDepth;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity
 * @ORM\Table(name="cms_category")
 *
 * Defines the properties of the Category entity to represent the Article category.
 *
 * See https://symfony.com/doc/current/book/doctrine.html#creating-an-entity-class
 *
 * @author zhangbing <550695@qq.com>
 */
class Category
{
    const NUM_ITEMS = 10;
    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     *
     * @Groups({"normal"})
     */
    private $id;

    /**
     * @ORM\OneToMany(targetEntity="Category", mappedBy="parent")
     *
     * @Groups({"normal"})
     */
    private $children;

    /**
     * @var Category | NULL
     *
     * @ORM\ManyToOne(targetEntity="Category", inversedBy="children")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id")
     *
     * @Groups({"normal"})
     */
    private $parent;


    /**
     * @var string
     *
     * @ORM\Column(type="string", unique=true)
     *
     * @Groups({"normal"})
     */
    private $name;

    /**
     * @var Article[]|ArrayCollection
     * @MaxDepth(2)
     * @ORM\OneToMany(
     *      targetEntity="Article",
     *      mappedBy="category",
     *      orphanRemoval=true,
     *      cascade={"persist"}
     * )
     *
     * @Groups({"articles"})
     */
    private $articles;

    /**
     * @var Article | null
     *
     * @ORM\OneToOne(targetEntity="Article")
     * @ORM\JoinColumn(name="front_page_id", referencedColumnName="id", nullable=true)
     *
     * @Groups({"normal"})
     *
     */
    private $frontPage;

    /**
     * @var string | null
     *
     * @ORM\Column(type="text", nullable=true)
     *
     * @Groups({"normal"})
     */
    private $description;

    /**
     * @var int | null

     * @ORM\Column(type="integer", nullable=true, options={"default" : 0})
     *
     * @Groups({"normal"})
     */
    private $sort = 0;

    public function __construct()
    {
        $this->parent = null;
        $this->children = new ArrayCollection();
        $this->articles = new ArrayCollection();
        $this->frontPage = null;
        $this->sort = 0;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @param $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getChildren()
    {
        return $this->children;
    }

    public function setChildren(ArrayCollection $children)
    {
        $this->children = $children;
        return $this;
    }

    public function addChildren(Category $category)
    {
        $category->setParent($this);
        if (!$this->children->contains($category)) {
            $this->children->add($category);
        }
    }

    public function removeChildren(Category $category)
    {
        $category->setParent(null);
        $this->articles->removeElement($category);
    }

    /**
     * @return Category|null
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @param mixed $parent
     * @return Category
     */
    public function setParent($parent)
    {
        $this->parent = $parent;
        return $this;
    }

    /**
     * @param int $count
     * @return Collection
     */
    public function getArticles($count = -1)
    {
        $posts = null;
        $now = new \DateTime();
        $criteria = Criteria::create();
        $criteria
            ->andWhere(Criteria::expr()->lte('publishedAt', $now))
            ->orderBy(array('publishedAt'=>'DESC'))
        ;

        if($count > 0){
            $criteria->setMaxResults($count);
            $posts = $this->articles->matching($criteria);
        }else{
            /*
             * Maximum records per page
             */
            ;
            $posts = $this->articles->matching($criteria);
        }
        //dump($posts);
        return $posts;
    }

    public function addArticle(Article $post)
    {
        $post->setCategory($this);
        if (!$this->articles->contains($post)) {
            $this->articles->add($post);
        }
    }

    public function removeArticle(Article $post)
    {
        $post->setCategory(null);
        $this->articles->removeElement($post);
    }

    public function __toString (){
        return $this->getName();
    }

    /**
     * @return Article|null
     */
    public function getFrontPage()
    {
        return $this->frontPage;
    }

    /**
     * @param Article|null $frontPage
     * @return Category
     */
    public function setFrontPage($frontPage)
    {
        $this->frontPage = $frontPage;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getDescription()
    {
        return $this->description;
    }


    /**
     * @param string | null $description
     * @return Category
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getSort()
    {
        return $this->sort;
    }

    /**
     * @param int|null $sort
     * @return Category
     */
    public function setSort($sort)
    {
        $this->sort = $sort;
        return $this;
    }
}
