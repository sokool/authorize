<?php
/**
 * Created by PhpStorm.
 * User: sokool
 * Date: 03/06/14
 * Time: 15:19
 */

namespace AuthorizeTest;

use AuthorizeTest\Asset\Authentication\FakeAdapter;
use AuthorizeTest\Asset\Provider\Permission as PermissionProvider;
use AuthorizeTest\Asset\Provider\Role as RoleProvider;
use MintSoft\Authorize\Annotation\AnnotationBuilder;
use MintSoft\Authorize\Annotation\Authorize;
use MintSoft\Authorize\Service\MvcKeeper;
use MintSoft\Authorize\Service\RbacService;
use Zend\Authentication\AuthenticationService;
use Zend\Authentication\Storage\NonPersistent;
use Zend\EventManager\EventManager;
use Zend\Http\Response;
use Zend\Mvc\Application;
use Zend\Mvc\Controller\ControllerManager;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\Router\RouteMatch;

//use FloTest\Bootstrap;

class MvcKeeperServiceTest extends \PHPUnit_Framework_TestCase
{
    CONST IDENTITY = 'some-user@some-domain.here';

    /**
     * @var MvcKeeper
     */
    protected $mvcKeeper;

    public function setUp()
    {
        $rbacService                = new RbacService(new RoleProvider(), new PermissionProvider());
        $authenticationService      = new AuthenticationService(new NonPersistent(), new FakeAdapter());
        $authorizeAnnotationBuilder = new AnnotationBuilder(
            (new ControllerManager())
                ->setAllowOverride(true)
                ->setInvokableClass('can-be-anything', AnnotationBuilderServiceTest::ANNOTATION_A_CLASS)
                ->setServiceLocator(\Bootstrap::getServiceManager())
        );

        //Authentication of fake identity (like login process)
        FakeAdapter::$userIdentity = self::IDENTITY;
        $authenticationService->authenticate();

        $this->mvcKeeper = new MvcKeeper($rbacService, $authenticationService, $authorizeAnnotationBuilder);
    }

    public function testInstance()
    {
        $this->assertInstanceOf('MintSoft\Authorize\Service\RbacService', $this->mvcKeeper->getRbac());
        $this->assertInstanceOf('MintSoft\Authorize\Annotation\AnnotationBuilder', $this->mvcKeeper->getAuthorizeBuilder());
        $this->assertSame($this->mvcKeeper->getIdentity(), self::IDENTITY);
    }

	public function testAccess()
	{
		$this->assertTrue($this->mvcKeeper->hasAccess(AnnotationBuilderServiceTest::ANNOTATION_A_CLASS, 'forUploadAndDownloadAction'));
	}

	/**
	 * Testing:
	 * attach()
	 *
	 * Check if attaching listeners properly save callback in protected container
	 */
	public function testAttach()
	{
		$event = new EventManager();

		/** @var \Closure $closure */
		$closure = function() {
			return $this->listeners;
		};

		$this->mvcKeeper->attach($event);

		$getListeners = $closure->bindTo($this->mvcKeeper, get_class($this->mvcKeeper));
		$listeners    = $getListeners();

		foreach ($listeners as $listener) {
			$this->assertInstanceOf('Zend\Stdlib\CallbackHandler', $listener);
		}
	}

	/**
	 * Testing:
	 * isAllowed()
	 *
	 * Check if without identity you will be not allowed to gain access
	 */
	public function testIsAllowedNoIdentity()
	{
		$mock = $this->getMock(
			get_class($this->mvcKeeper),
			[
				'getIdentity',
				'getRbac'
			],
			[
				$this->mvcKeeper->getRbac(),
				$this->mvcKeeper->getAuthenticationService(),
				$this->mvcKeeper->getAuthorizeBuilder()
			]
		);

		$class  = new \ReflectionClass(get_class($mock));
		$method = $class->getMethod('isAllowed');
		$method->setAccessible(true);

		$authorize = new Authorize([]);

		$this->assertFalse($method->invoke($mock, $authorize));
	}

	/**
	 * Testing:
	 * isAllowed()
	 *
	 * Checking two cases:
	 *  - check access by permission
	 *  - check access by role
	 */
	public function testIsAllowed()
	{
		$class  = new \ReflectionClass(get_class($this->mvcKeeper));
		$method = $class->getMethod('isAllowed');
		$method->setAccessible(true);

		$authorizeByPermission = new Authorize([
			'value' => [
				'permissions' => [
					'render-default-page'
			    ]
			]
		]);

		$authorizeByRole = new Authorize([
			'value' => [
				'roles' => [
					'Download'
				]
			]
		]);

		$this->assertTrue($method->invoke($this->mvcKeeper, $authorizeByPermission));
		$this->assertTrue($method->invoke($this->mvcKeeper, $authorizeByRole));
	}

	/**
	 * Testing:
	 * isAllowed()
	 *
	 * Check if user will gain access when asking for role/permission which user haven't
	 */
	public function testIsAllowedNotMyRole()
	{
		$class  = new \ReflectionClass(get_class($this->mvcKeeper));
		$method = $class->getMethod('isAllowed');
		$method->setAccessible(true);

		$authorize = new Authorize([
			'value' => [
				'roles' => [
					'NotMyRole'
				],
				'permissions' => [
					'grant-for-all-users',
				]
			]
		]);

		$this->assertFalse($method->invoke($this->mvcKeeper, $authorize));
	}

	/**
	 * Testing:
	 * onRoute()
	 *
	 * Checks if response status code was set to 403 when user doesn't have access
	 */
	public function testOnRoute()
	{
		$mock = $this->getMock(
			get_class($this->mvcKeeper),
			[
				'hasAccess'
			],
			[
				$this->mvcKeeper->getRbac(),
				$this->mvcKeeper->getAuthenticationService(),
				$this->mvcKeeper->getAuthorizeBuilder()
			]
		);

		$mock->expects($this->any())
			->method('hasAccess')
			->will($this->returnValue(false));

		$event       = new MvcEvent();
		$application = new Application([], \Bootstrap::getServiceManager());
		$routeMatch  = new RouteMatch(['controller' => 'Flo\Controller\Test', 'action' => 'someSpecificCustom']);
		$response    = new Response();
		$event->setApplication($application);
		$event->setRouteMatch($routeMatch);
		$event->setResponse($response);

		$mock->onRoute($event);
		$this->assertEquals(403, $event->getResponse()->getStatusCode());
	}
}