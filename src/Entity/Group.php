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

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="sys_group")
 *
 */
class Group implements GroupInterface
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    protected $name;

    /**
     *
     * @var string
     * @ORM\Column(type="text",nullable=true)
     */
    private $description;

    /**
     * @var array
     *
     * @ORM\Column(type="json_array", nullable = true)
     */
    protected $roles;

    /**
     * @ORM\ManyToMany(targetEntity="User", mappedBy="groups")
     */
    private $users;

    /**
     * Group constructor.
     * @param $name
     * @param array $roles
     */
    public function __construct($name, $roles = array())
    {
        $this->name = $name;
        $this->roles = $roles;
        $this->description = "";
        $this->users = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return Group
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set description for this device
     * @param string $description
     * @return $this
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @param string $role
     *
     * @return Group
     */
    public function addRole($role)
    {
        if (!$this->hasRole($role)) {
            $this->roles[] = strtoupper($role);
        }

        return $this;
    }

    /**
     * @param string $role
     *
     * @return boolean
     */
    public function hasRole($role)
    {
        return in_array(strtoupper($role), $this->roles, true);
    }

    public function getRoles()
    {
        return $this->roles;
    }

    public function getRolesString()
    {
        return implode(" | ", $this->roles);
    }
    /**
     * @param array $roles
     *
     * @return Group
     */
    public function setRoles(array $roles)
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @param string $role
     *
     * @return Group
     */
    public function removeRole($role)
    {
        if (false !== $key = array_search(strtoupper($role), $this->roles, true)) {
            unset($this->roles[$key]);
            $this->roles = array_values($this->roles);
        }

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * {@inheritDoc}
     */
    public function addUser(User $user)
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
        }
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function removeUser(User $user)
    {
        if ($this->users->contains($user)) {
            $this->users->removeElement($user);
        }
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function removeAllUsers()
    {
        foreach($this->users as $user){
            $this->removeUser($user);
        }
    }
}