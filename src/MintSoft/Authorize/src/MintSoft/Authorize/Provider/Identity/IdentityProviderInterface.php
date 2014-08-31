<?php
/**
 * Created by PhpStorm.
 * User: sokool
 * Date: 16/05/14
 * Time: 08:35
 */

namespace MintSoft\Authorize\Provider\Identity;

interface IdentityProviderInterface
{
    /**
     * Get unique identifier of user
     *
     * @return string
     */
    public function getId();

    /**
     * Get unique name of the user. Can be everything like email, NI number or any other readable unique value.
     *
     * @return string
     */
    public function getUserName();
} 