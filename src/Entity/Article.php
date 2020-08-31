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

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\MaxDepth;
use Symfony\Component\Serializer\Annotation\Groups;
/**
 * @ORM\Entity
 * @ORM\Table(name="cms_article")
 *
 * Defines the properties of the Article entity to represent the blog articles.
 *
 * See https://symfony.com/doc/current/book/doctrine.html#creating-an-entity-class
 *
 * Tip: if you have an existing database, you can generate these entity class automatically.
 * See https://symfony.com/doc/current/cookbook/doctrine/reverse_engineering.html
 *
 * @author zhangbing <550695@qq.com>
 *
 *
 */
class Article
{
    /**
     * Use constants to define configuration options that rarely change instead
     * of specifying them in app/config/config.yml.
     *
     * See https://symfony.com/doc/current/best_practices/configuration.html#constants-vs-configuration-options
     */
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
     * @var string
     *
     * @ORM\Column(type="string")
     * @Assert\NotBlank
     *
     * @Groups({"normal"})
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     *
     * @Groups({"normal"})
     */
    private $slug;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     * @Assert\NotBlank(message="article.blank_summary")
     * @Assert\Length(max=255)
     *
     * @Groups({"normal"})
     */
    private $summary;

    /**
     * @var string
     *
     * @ORM\Column(type="text")
     * @Assert\NotBlank(message="article.blank_content")
     * @Assert\Length(min=10, minMessage="article.too_short_content")
     *
     * @Groups({"normal"})
     */
    private $content;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     * @Assert\DateTime
     *
     * @Groups({"normal"})
     */
    private $publishedAt;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(nullable=false)
     *
     * @Groups({"normal"})
     */
    private $author;

    /**
     * @var Comment[]|ArrayCollection
     *
     * @ORM\OneToMany(
     *      targetEntity="Comment",
     *      mappedBy="article",
     *      orphanRemoval=true
     * )
     * @ORM\OrderBy({"publishedAt": "DESC"})
     *
     * @Groups({"normal"})
     */
    private $comments;

    /**
     * @var Tag[]|ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="App\Entity\Tag", cascade={"persist"})
     * @ORM\JoinTable(name="cms_article_tag")
     * @ORM\OrderBy({"name": "ASC"})
     * @Assert\Count(max="4", maxMessage="article.too_many_tags")
     *
     * @Groups({"normal"})
     */
    private $tags;

    /**
     * @var Category
     * @MaxDepth(1)
     * @ORM\ManyToOne(targetEntity="Category", inversedBy="articles")
     * @ORM\JoinColumn(nullable=true)
     *
     * @Groups({"normal"})
     */
    private $category;

    /**
     * @var string
     *
     * @ORM\Column(type="string",nullable=true )
     * @Assert\Length(max=255)
     *
     * @Groups({"normal"})
     */
    private $coverImage;


    /**
     * @var boolean | null
     *
     * @ORM\Column(type="boolean",nullable=true )
     *
     * @Groups({"normal"})
     */
    private $frontPage;

    /**
     * @var int|null
     *
     * @ORM\Column(type="integer", nullable = true)
     *
     * @Groups({"normal"})
     */
    private $count;

    public function __construct()
    {
        $this->publishedAt = new \DateTime();
        $this->comments = new ArrayCollection();
        $this->tags = new ArrayCollection();
        $this->frontPage = false;
        $this->count = 0;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * @param string $slug
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;
    }

    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param string $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    public function getPublishedAt()
    {
        return $this->publishedAt;//->format('Y-m-d H:i:s');
    }

    public function setPublishedAt(\DateTime $publishedAt)
    {
        $this->publishedAt = $publishedAt;
    }

    /**
     * @return User
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * @param User $author
     */
    public function setAuthor(User $author)
    {
        $this->author = $author;
    }

    public function getComments()
    {
        return $this->comments;
    }

    public function addComment(Comment $comment)
    {
        $comment->setPost($this);
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
        }
    }

    public function removeComment(Comment $comment)
    {
        $comment->setPost(null);
        $this->comments->removeElement($comment);
    }

    public function getSummary()
    {
        return $this->summary;
    }

    /**
     * @param string $summary
     */
    public function setSummary($summary)
    {
        $this->summary = $summary;
    }

    public function addTag(Tag $tag)
    {
        if (!$this->tags->contains($tag)) {
            $this->tags->add($tag);
        }
    }

    public function removeTag(Tag $tag)
    {
        $this->tags->removeElement($tag);
    }

    public function getTags()
    {
        return $this->tags;
    }

    /**
     * @return Category
     */
    public function getCategory()
    {
        return $this->category;
    }
    
    /**
     * @param Category $category
     * @return void
     */
    public function setCategory(Category $category)
    {
        $this->category = $category;
    }

    /**
     * @return string
     */
    public function getCoverImage()
    {
        return $this->coverImage;
    }

    /**
     * @param string $coverImage
     * @return Article
     */
    public function setCoverImage($coverImage)
    {
        $this->coverImage = $coverImage;
        return $this;
    }

    /**
     * @return bool|null
     */
    public function isFrontPage()
    {
        return $this->frontPage;
    }

    /**
     * @param bool|null $frontPage
     * @return Article
     */
    public function setFrontPage($frontPage)
    {
        $this->frontPage = $frontPage;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getCount()
    {
        return $this->count;
    }

    /**
     * @param int|null $count
     * @return Article
     */
    public function setCount($count)
    {
        if($count == null)
            return;

        $this->count = $count;
        return $this;
    }

    public function updateCount(){
        $this->count++;
    }
}
