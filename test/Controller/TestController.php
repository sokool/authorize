<?php
/**
 * Created by PhpStorm.
 * User: sokool
 * Date: 03/06/14
 * Time: 15:27
 */

namespace AuthorizeTest\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Authorize\Annotation\Authorize;

/**
 * @Authorize
 */
class TestController extends AbstractActionController
{
    /**
     * @Authorize({
     *      "roles"         : {"AWR"},
     *      "users"         : "can.be.anyone@flo.de",
     *      "permissions"   : "booking/create",
     * })
     *
     */
    public function someSpecificCustomAction()
    {
    }
}