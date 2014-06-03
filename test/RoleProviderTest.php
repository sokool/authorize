<?php
/**
 * Created by PhpStorm.
 * User: sokool
 * Date: 03/06/14
 * Time: 12:44
 */

namespace AuthorizeTest;

use AuthorizeTest\Asset\Provider;
use FloTest\Bootstrap;

class RoleProviderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Provider\Role;
     */
    protected $roleProvider;

    public function setUp()
    {
        $this->roleProvider = new Provider\Role();
    }

    public function testInstance()
    {
        $this->assertInstanceOf('Authorize\Provider\Role\RoleProviderInterface', $this->roleProvider);
    }

    public function testGetAll()
    {
        $rolesArray = $this->roleProvider->getAll();

        $this->assertTrue(is_array($rolesArray));
        $this->assertTrue(is_string(reset($rolesArray)));
    }

    public function testGetByUser()
    {
        $this->assertTrue(is_array($this->roleProvider->getByUser('some-user@some-domain.here')));
    }
}