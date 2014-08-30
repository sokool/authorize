<?php
/**
 * Created by PhpStorm.
 * User: sokool
 * Date: 03/06/14
 * Time: 15:27
 */

namespace AuthorizeTest\Asset\Controller;

use Zend\Mvc\Controller\AbstractActionController;

/**
 * @Authorize()
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

	/**
	 * @Authorize({
	 *      "roles" : {"Upload", "Download"}
	 * })
	 */
	public function forUploadAndDownloadAction()
	{
	}

	/**
	 * @Authorize({
	 *      "roles" : {"Grant"}
	 * })
	 */
	public function saveAction()
	{
	}
}