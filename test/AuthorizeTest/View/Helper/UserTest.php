<?php
/**
 * Created by PhpStorm.
 * User: sokool
 * Date: 20/06/14
 * Time: 14:00
 */

namespace AuthorizeTest\View\Helper;

use AuthorizeTest\AnnotationBuilderServiceTest;
use AuthorizeTest\Asset\Authentication\FakeAdapter;
use AuthorizeTest\Asset\Provider\Permission;
use AuthorizeTest\Asset\Provider\Role;
use MintSoft\Authorize\Annotation\AnnotationBuilder;
use MintSoft\Authorize\Factory\UserFactory;
use MintSoft\Authorize\Service\MvcKeeper;
use MintSoft\Authorize\Service\RbacService;
use MintSoft\Authorize\View\Helper\User as UserHelper;
use Zend\Authentication\AuthenticationService;
use Zend\Authentication\Storage\NonPersistent;
use Zend\Mvc\Controller\ControllerManager;
use Zend\Mvc\Router\Http\Literal;
use Zend\Mvc\Router\SimpleRouteStack;

//
class UserTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var UserHelper
     */
    protected $userHelper;

    public function setUp()
    {
        $controllerManager     = (new ControllerManager())
            ->setInvokableClass('Test', AnnotationBuilderServiceTest::ANNOTATION_A_CLASS)
            ->setServiceLocator(Bootstrap::getServiceManager());
		$testRoute             = new Literal('/some/test/route', [
			'controller' => 'Test',
			'action'     => 'forUploadAndDownload'
		]);
		$saveRoute             = new Literal('/save/test/route', [
			'controller' => 'Test',
			'action'     => 'save'
		]);
        $routeStack            = new SimpleRouteStack();
        $rbacService           = new RbacService(new Role(), new Permission());
        $authenticationService = new AuthenticationService();
        $annotationBuilder     = new AnnotationBuilder($controllerManager);
        $mvcKeeperService      = new MvcKeeper($rbacService, $authenticationService, $annotationBuilder);

        $this->userHelper = new UserHelper();
        $this->userHelper->setMvcKeeper($mvcKeeperService);
        $this->userHelper->setControllerManager($controllerManager);
		$this->userHelper->setRouteStack($routeStack->addRoute('test', $testRoute));
		$this->userHelper->setRouteStack($routeStack->addRoute('save', $saveRoute));
    }

    protected function authenticateUser($username)
    {
        FakeAdapter::$userIdentity = $username;

        return $this->userHelper->getMvcKeeper()->getAuthenticationService()
            ->setStorage(new NonPersistent())
            ->authenticate(new FakeAdapter());
    }

    public function testFactory()
    {
        $userFactory = new UserFactory();
        $this->assertInstanceOf('Zend\ServiceManager\FactoryInterface', $userFactory);
    }

    public function testInstanceViewHelper()
    {
        $this->assertInstanceOf('Zend\View\Helper\AbstractHelper', new UserHelper());
    }

    public function testIdentity()
    {
        $userName = 'some-user@some-domain.here';

        $this->authenticateUser($userName);

        $this->assertSame($this->userHelper->identity(), $userName);
    }

	public function testIsInRole()
	{
		$userName = 'some-user@some-domain.here';
		$this->authenticateUser($userName);

		$this->assertTrue($this->userHelper->isInRole('Download'));
		$this->assertFalse($this->userHelper->isInRole('NotMyRole'));
	}

	public function testHasPermission()
	{
		$userName = 'some-user@some-domain.here';
		$this->authenticateUser($userName);

		$this->assertTrue($this->userHelper->hasPermission('render-form'));
		$this->assertFalse($this->userHelper->hasPermission('grant-for-all-users'));
	}

    public function testAccessToRoute()
    {
        $this->authenticateUser('some-user@some-domain.here');
		$this->assertTrue($this->userHelper->hasAccess('test'));
		$this->assertFalse($this->userHelper->hasAccess('save'));
    }

    /**
     * @expectedException Exception
     */
    public function testOnAccessException()
    {
        $this->authenticateUser('some-user@some-domain.here');
        $this->userHelper->hasAccess('not-exist-and-throw-exception');
    }
}