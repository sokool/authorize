<?php
/**
 * Created by PhpStorm.
 * User: sokool
 * Date: 04/06/14
 * Time: 09:05
 */

namespace AuthorizeTest\Asset\Controller;

use Zend\Mvc\Controller\AbstractActionController;

class TestBController extends AbstractActionController
{

    public function withoutAuthorizeAction()
    {
    }

    /**
     * @Authorize
     */
    public function authorizeWithoutParamsAction()
    {
    }
} 