<?php
/**
 * Created by PhpStorm.
 * User: sokool
 * Date: 04/06/14
 * Time: 09:26
 */

namespace AuthorizeTest;

use MintSoft\Authorize\Annotation\Authorize;

class AuthorizeAnnotationTest extends \PHPUnit_Framework_TestCase
{

    public function testInstance()
    {
        $this->assertInstanceOf('Zend\Form\Annotation\AbstractArrayOrStringAnnotation', new Authorize([]));
    }

    public function testEmpty()
    {
        $authorizeAnnotation = new Authorize([]);

        $this->assertTrue($authorizeAnnotation->isEmpty());
        $this->assertEmpty($authorizeAnnotation->getPermissions());
        $this->assertEmpty($authorizeAnnotation->getRoles());
        $this->assertEmpty($authorizeAnnotation->getUsers());
    }

    public function testFilled()
    {

        $authorizeAnnotation = new Authorize(['value' => [
            'roles'       => 'Download',
            'permissions' => 'profile-page',
            'users'       => 'dood@domain.tld',
        ]]);

        $this->assertTrue(is_array($authorizeAnnotation->getPermissions()));
        $this->assertTrue(in_array('profile-page', $authorizeAnnotation->getPermissions()));

        $this->assertTrue(is_array($authorizeAnnotation->getRoles()));
        $this->assertTrue(in_array('Download', $authorizeAnnotation->getRoles()));

        $this->assertTrue(is_array($authorizeAnnotation->getUsers()));
        $this->assertTrue(in_array('dood@domain.tld', $authorizeAnnotation->getUsers()));

        $authorizeAnnotation = new Authorize(['value' => [
            'roles'       => $roles = ['Download', 'Upload'],
            'permissions' => $permissions = ['profile-page', 'render-form', 'edit-user'],
            'users'       => $users = ['dood@domain.tld', 'man@domain.tld'],
        ]]);

        $this->assertSame($authorizeAnnotation->getRoles(), $roles);
        $this->assertSame($authorizeAnnotation->getPermissions(), $permissions);
        $this->assertSame($authorizeAnnotation->getUsers(), $users);
    }
} 