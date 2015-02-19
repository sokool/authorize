<?php
/**
 * Created by PhpStorm.
 * User: sokool
 * Date: 04/06/14
 * Time: 09:05
 */

namespace AuthorizeTest\Asset\Controller;
use MintSoft\Authorize\Annotation as Auth;

/**
 * @Auth\Authenticated()
 * @Auth\Role("ASD")
 */
class TestBController
{

    /**
     * @Auth\Role("Dispatcher")
     */
    public function withoutAuthorizeAction()
    {
    }

    /**
     * @Auth\Role("Manager", {"manageEmployees", "manageOrders"})
     */
    public function authorizeWithoutParamsAction()
    {
    }
} 