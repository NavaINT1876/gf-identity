<?php

namespace NavaINT1876\GfIdentity;

/**
 * Class User
 *
 * Class represents user data retrieved from JWT token
 *
 * @package NavaINT1876\GfIdentity
 */
class User
{
    /** @var int */
    public $id;

    /** @var array */
    public $roles;

    /** @var string */
    public $username;

    /** @var string */
    public $full_name;

    /** @var string */
    public $email;
}
