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

/**
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 * @author Christophe Coevoet <stof@notk.org>
 */
interface GroupInterface
{
    /**
     * @return integer
     */
    public function getId();

    /**
     * @param string $name
     *
     * @return self
     */
    public function setName($name);

    /**
     * @return string
     */
    public function getName();

    /**
     * @param array $roles
     *
     * @return self
     */
    public function setRoles(array $roles);

    /**
     * @param string $role
     *
     * @return self
     */
    public function addRole($role);

    /**
     * @param string $role
     *
     * @return boolean
     */
    public function hasRole($role);

    /**
     * @return array
     */
    public function getRoles();

    /**
     * @param string $role
     *
     * @return self
     */
    public function removeRole($role);
}
