<?php
/**
 * Created by PhpStorm.
 * User: sokool
 * Date: 03/06/14
 * Time: 14:38
 */

namespace AuthorizeTest;

use AuthorizeTest\Asset\Provider\Permission as PermissionProvider;
use AuthorizeTest\Asset\Provider\Role as RoleProvider;
use MintSoft\Authorize\Service\RbacService;

class RbacServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var RbacService
     */
    protected $service;

    public function setUp()
    {
        $this->service = new RbacService(new RoleProvider(), new PermissionProvider());
    }

    public function testDefaultDataProviders()
    {
        $this->assertInstanceOf('MintSoft\Authorize\Provider\Permission\PermissionProviderInterface', $this->service->getPermissionProvider());
        $this->assertInstanceOf('MintSoft\Authorize\Provider\Role\RoleProviderInterface', $this->service->getRoleProvider());
    }

    public function testCache()
    {
        $cache = $this->service->getCacheAdapter();
        $this->assertNull($cache->getItem(RbacService::CACHE_KEY));

        //provoke RBAC container to run cache
        $this->service->hasRole('none', 'none');

        $this->assertNotEmpty($cache->getItem(RbacService::CACHE_KEY));
    }

    public function testUserHasRole()
    {
        //user not exist and role not exist
        $this->assertFalse($this->service->hasRole('none-user@here.fr', 'NotRole'));
        //user not exists but role exist
        $this->assertFalse($this->service->hasRole('none-user@here.fr', 'Download'));
        //user exists but role not exist
        $this->assertFalse($this->service->hasRole('some-user@some-domain.here', 'NotRole'));
        //user exist and role exist
        $this->assertTrue($this->service->hasRole('some-user@some-domain.here', 'Download'));
    }

    public function testUserHasPermission()
    {
        //user not exist and permission not exist
        $this->assertFalse($this->service->hasPermission('none-user@here.fr', 'permission-which-not-exist'));
        //user not exist but permission exist
        $this->assertFalse($this->service->hasPermission('none-user@here.fr', 'render-form'));
        //user exist but he has no permission
        $this->assertFalse($this->service->hasPermission('some-user@some-domain.here', 'grant-for-all-users'));
        //user exist and he has permission
        $this->assertTrue($this->service->hasPermission('some-user@some-domain.here', 'render-form'));
    }

	/**
	 * Testing:
	 * getContainer()
	 * getCacheAdapter()
	 *
	 * Check if getter will return from cache early saved container
	 */
	public function testGetContainerFromCache()
	{
		$tempCacheContainer = [
			'someConf' => true
		];

		$cache = $this->service->getCacheAdapter();
		$cache->setItem(RbacService::CACHE_KEY, serialize($tempCacheContainer));

		$class  = new \ReflectionClass(get_class($this->service));
		$method = $class->getMethod('getContainer');
		$method->setAccessible(true);

		$loadedContainer = $method->invoke($this->service);

		$this->assertEquals($tempCacheContainer, $loadedContainer);

		// clear cache
		$cache->removeItem(RbacService::CACHE_KEY);
	}
}