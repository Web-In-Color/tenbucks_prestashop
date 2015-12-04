<?php
/**
* Controller used to display iframe.
*
*  @author    Web In Color <contact@prestashop.com>
*  @copyright 2012-2015 Web In Color
*  @license   http://www.apache.org/licenses/  Apache License
*  International Registered Trademark & Property of Web In Color
*/
include_once dirname(__FILE__).'/../../classes/TenbucksController.php';

/**
 * Tenbucks admin controller.
 */
class AdminTenbucksAccountController extends TenbucksController
{
    protected $redirect_to = 'account';
}
