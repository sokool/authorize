<?php
/**
 * Created by PhpStorm.
 * User: sokool
 * Date: 03/06/14
 * Time: 14:38
 */

namespace AuthorizeTest;

use Authorize\Factory\RbacFactory;
use Authorize\Service\RbacService;
use FloTest\Bootstrap;

class RbacServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var RbacService
     */
    protected $service;

    public function setUp()
    {
        Bootstrap::getServiceManager()
            ->setAllowOverride(true)
            ->setFactory('Authorize\Provider\Role', 'AuthorizeTest\Asset\Provider\Role')
            ->setFactory('Authorize\Provider\Permission', 'AuthorizeTest\Asset\Provider\Permission');

        $this->service = (new RbacFactory())->createService(Bootstrap::getServiceManager());
    }

    public function testDefaultDataProviders()
    {
        $this->assertInstanceOf('Authorize\Provider\Permission\PermissionProviderInterface', $this->service->getPermissionProvider());
        $this->assertInstanceOf('Authorize\Provider\Role\RoleProviderInterface', $this->service->getRoleProvider());
    }

    public function testUserHasRole()
    {
        $this->assertFalse($this->service->hasRole('none-user@here.fr', 'NotRole'));
        $this->assertFalse($this->service->hasRole('none-user@here.fr', 'Download'));
        $this->assertFalse($this->service->hasRole('some-user@some-domain.here', 'NotRole'));
        $this->assertTrue($this->service->hasRole('some-user@some-domain.here', 'Download'));
    }

    public function testUserHasPermission()
    {
        //user not exist and has no permission
        $this->assertFalse($this->service->hasPermission('none-user@here.fr', 'permission-which-not-exist'));
        //user exist but has no permission
        $this->assertFalse($this->service->hasPermission('some-user@some-domain.here', 'grant-for-all-users'));
        //user exist and has permission
        $this->assertTrue($this->service->hasPermission('some-user@some-domain.here', 'render-form'));
    }
}