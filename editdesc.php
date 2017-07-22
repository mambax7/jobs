<?php
/**
 * Jobs for XOOPS
 *
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @copyright   {@link https://xoops.org/ XOOPS Project}
 * @license     {@link http://www.gnu.org/licenses/gpl-2.0.html GNU GPL 2 or later}
 * @package     jobs
 * @author      John Mordo
 * @author      XOOPS Development Team
 */

$moduleDirName = basename(dirname(__DIR__));
$main_lang     = '_' . strtoupper($moduleDirName);

/**
 * Xoops Header
 */
include __DIR__ . '/../../mainfile.php';
require_once XOOPS_ROOT_PATH . '/header.php';
require_once XOOPS_ROOT_PATH . '/class/criteria.php';

/**
 * Include modules classes
 */
require_once __DIR__ . '/class/pictures.php';

// Check if using XoopsCube (by jlm69)
// Needed because of a difference in the way Xoops and XoopsCube handle tokens

$xCube = false;
if (preg_match('/^XOOPS Cube/', XOOPS_VERSION)) { // XOOPS Cube 2.1x
    $xCube = true;
}

// Verify Ticket for Xoops Cube (by jlm69)
// If your site is XoopsCube it uses $xoopsGTicket for the token.

if ($xCube) {
    if (!$GLOBALS['xoopsSecurity']->check(true, $_REQUEST['token'])) {
        redirect_header($_SERVER['HTTP_REFERER'], 3, $GLOBALS['xoopsSecurity']->getErrors());
    }
} else {
    // Verify TOKEN for Xoops
    // If your site is Xoops it uses xoopsSecurity for the token.

    if (!$GLOBALS['xoopsSecurity']->check()) {
        redirect_header($_SERVER['HTTP_REFERER'], 3, constant($main_lang . '_TOKENEXPIRED'));
    }
}

/**
 * Receiving info from get parameters
 */
$cod_img = $_POST['cod_img'];
$lid     = $_POST['lid'];
$marker  = $_POST['marker'];

if ($marker == 1) {
    /**
     * Creating the factory  loading the picture changing its caption
     */
    $picture_factory = new Xoopsjlm_picturesHandler($xoopsDB);
    $picture         = $picture_factory->create(false);
    $picture->load($_POST['cod_img']);
    $picture->setVar('title', $_POST['caption']);

    /**
     * Verifying who's the owner to allow changes
     */
    $uid = $xoopsUser->getVar('uid');
    if ($uid == $picture->getVar('uid_owner')) {
        if ($picture_factory->insert($picture)) {
            redirect_header('view_photos.php?lid=' . $lid . '&uid=' . $uid . '', 3, constant($main_lang . '_DESC_EDITED'));
        } else {
            redirect_header('view_photos.php', 3, constant($main_lang . '_NOCACHACA'));
        }
    }
}

/**
 * Creating the factory  and the criteria to edit the desc of the picture
 * The user must be the owner
 */
$album_factory = new Xoopsjlm_picturesHandler($xoopsDB);
$criteria_img  = new Criteria('cod_img', $cod_img);
$uid           = $xoopsUser->getVar('uid');
$criteria_uid  = new Criteria('uid_owner', $uid);
$criteria      = new CriteriaCompo($criteria_img);
$criteria->add($criteria_uid);

/**
 * Lets fetch the info of the pictures to be able to render the form
 * The user must be the owner
 */
if ($array_pict = $album_factory->getObjects($criteria)) {
    $caption = $array_pict[0]->getVar('title');
    $url     = $array_pict[0]->getVar('url');
}
$url = $xoopsModuleConfig['' . $moduleDirName . '_link_upload'] . '/thumbs/thumb_' . $url;
$album_factory->renderFormEdit($caption, $cod_img, $url);

/**
 * Close page
 */
require_once XOOPS_ROOT_PATH . '/footer.php';
