<?php
/**
 * Created by PhpStorm.
 * User: sokool
 * Date: 03/06/14
 * Time: 15:27
 */

namespace AuthorizeTest\Asset\Controller;

use MintSoft\Authorize\Annotation as Auth;

/**
 * @Auth\Role("Manager", {"ManageTransport", "ManageEmployee"})
 * @Auth\Role("Dispatcher")
 */
class TestController
{
    /**
     * @Auth\Role("Manager")
     */
    public function someSpecificCustomAction()
    {
    }

    public function forUploadAndDownloadAction()
    {
    }

    public function saveAction()
    {
    }
}