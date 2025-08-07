<?php namespace XoopsModules\Jobs;

/*
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

/**
 * @copyright    XOOPS Project https://xoops.org/
 * @license      GNU GPL 2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 * @package
 * @since
 * @author     XOOPS Development Team
 */

/**
 * Class Resume
 */
class Resume extends \XoopsObject
{
    /**
     * constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->initVar('lid', \XOBJ_DTYPE_INT, null, false);
        $this->initVar('cid', \XOBJ_DTYPE_INT, null, true);
        $this->initVar('name', \XOBJ_DTYPE_TXTBOX, null, true, 100);
        $this->initVar('title', \XOBJ_DTYPE_TXTBOX, null, true, 100);
        $this->initVar('status', \XOBJ_DTYPE_INT, 1, false);
        $this->initVar('exp', \XOBJ_DTYPE_TXTBOX, null, false, 100);
        $this->initVar('expire', \XOBJ_DTYPE_TXTBOX, null, false, 3);
        $this->initVar('private', \XOBJ_DTYPE_TXTBOX, null, false, 100);
        $this->initVar('tel', \XOBJ_DTYPE_TXTBOX, null, false, 30);
        $this->initVar('salary', \XOBJ_DTYPE_TXTBOX, null, false, 100);
        $this->initVar('typeprice', \XOBJ_DTYPE_TXTBOX, null, false, 100);
        $this->initVar('date', \XOBJ_DTYPE_INT, null, false);
        $this->initVar('email', \XOBJ_DTYPE_TXTBOX, null, false, 100);
        $this->initVar('submitter', \XOBJ_DTYPE_TXTBOX, null, false, 60);
        $this->initVar('usid', \XOBJ_DTYPE_TXTBOX, null, false, 6);
        $this->initVar('town', \XOBJ_DTYPE_TXTBOX, null, false, 100);
        $this->initVar('state', \XOBJ_DTYPE_TXTBOX, null, false, 100);
        $this->initVar('valid', \XOBJ_DTYPE_INT, 1, false);
        $this->initVar('rphoto', \XOBJ_DTYPE_TXTBOX, null, false, 100);
        $this->initVar('resume', \XOBJ_DTYPE_TXTBOX, null, false, 100);
        $this->initVar('view', \XOBJ_DTYPE_INT, 0, false);
    }
}
