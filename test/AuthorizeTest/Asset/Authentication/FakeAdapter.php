<?php
/**
 * Created by PhpStorm.
 * User: sokool
 * Date: 05/06/14
 * Time: 09:51
 */

namespace AuthorizeTest\Asset\Authentication;

use Zend\Authentication\Adapter\AdapterInterface as AuthenticationAdapterInterface;
use Zend\Authentication\Result as AuthenticationResult;

class FakeAdapter implements AuthenticationAdapterInterface
{
    static public $userIdentity = null;

    public function authenticate()
    {
        return new AuthenticationResult(AuthenticationResult::SUCCESS, self::$userIdentity);
    }
}