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

class PermissionProviderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Provider\Permission;
     */
    protected $permissionProvider;

    public function setUp()
    {
        $this->permissionProvider = new Provider\Permission();
    }

    public function testInstance()
    {
        $this->assertInstanceOf('Authorize\Provider\Permission\PermissionProviderInterface', $this->permissionProvider);
    }

    public function testGetAll()
    {

        $permissionsArray = $this->permissionProvider->getAll();
        $this->assertTrue(is_array($permissionsArray));
        $this->assertTrue(is_string($permissionName = key($permissionsArray)));
        $this->assertTrue(is_array($permissionsArray[$permissionName]));
    }
}