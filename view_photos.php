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
 * @author      John Mordo aka jlm69 (www.jlmzone.com )
 * @author      Marcello Brandao
 * @author      XOOPS Development Team
 */

$moduleDirName = basename(__DIR__);
$main_lang     = '_' . strtoupper($moduleDirName);

/**
 * Xoops header
 */
include __DIR__ . '/../../mainfile.php';
$GLOBALS['xoopsOption']['template_main'] = 'jobs_view_photos.tpl';
require_once XOOPS_ROOT_PATH . '/header.php';

/**
 * Module classes
 */
include __DIR__ . '/class/pictures.php';
if (isset($_GET['lid'])) {
    $lid = $_GET['lid'];
} else {
    header('Location: ' . XOOPS_URL . "/modules/$moduleDirName/index.php");
}
/**
 * Is a member looking ?
 */
if (!empty($xoopsUser)) {
    /**
     * If no $_GET['uid'] then redirect to own
     */
    if (isset($_GET['uid'])) {
        $uid = $_GET['uid'];
    } else {
        header('Location: ' . XOOPS_URL . "/modules/$moduleDirName/index.php");
    }

    /**
     * Is the user the owner of the album ?
     */

    $isOwner = ($xoopsUser->getVar('uid') == $_GET['uid']) ? true : false;

    $module_id = $xoopsModule->getVar('mid');

    if (is_object($xoopsUser)) {
        $groups =& $xoopsUser->getGroups();
    } else {
        $groups = XOOPS_GROUP_ANONYMOUS;
    }

    $gpermHandler = xoops_getHandler('groupperm');

    if (isset($_POST['item_id'])) {
        $perm_itemid = (int)$_POST['item_id'];
    } else {
        $perm_itemid = 0;
    }

    //If no access
    if (!$gpermHandler->checkRight('jobs_premium', $perm_itemid, $groups, $module_id)) {
        $permit = '0';
    } else {
        $permit = '1';
    }

    /**
     * If it is an anonym
     */
} else {
    if (isset($_GET['uid'])) {
        $uid = $_GET['uid'];
    } else {
        header('Location: ' . XOOPS_URL . "/modules/$moduleDirName/index.php");
        $isOwner = false;
    }
}

/**
 * Filter for search pictures in database
 */
$criteria_lid = new criteria('lid', $lid);
$criteria_uid = new criteria('uid', $uid);
/**
 * Creating a factory of pictures
 */
$album_factory = new Xoopsjlm_picturesHandler($xoopsDB);

/**
 * Fetch pictures from the factory
 */
$pictures_object_array = $album_factory->getObjects($criteria_lid, $criteria_uid);

/**
 * How many pictures are on the user album
 */
$pictures_number = $album_factory->getCount($criteria_lid, $criteria_uid);

/**
 * If there is no pictures in the album
 */
if ($pictures_number == 0) {
    $nopicturesyet = _JOBS_NOTHINGYET;
    $xoopsTpl->assign('lang_nopicyet', $nopicturesyet);
} else {

    /**
     * Lets populate an array with the data from the pictures
     */
    $i = 0;
    foreach ($pictures_object_array as $picture) {
        $pictures_array[$i]['url']     = $picture->getVar('url', 's');
        $pictures_array[$i]['desc']    = $picture->getVar('title', 's');
        $pictures_array[$i]['cod_img'] = $picture->getVar('cod_img', 's');
        $pictures_array[$i]['lid']     = $picture->getVar('lid', 's');
        $xoopsTpl->assign('pics_array', $pictures_array);

        ++$i;
    }
}

/**
 * Show the form if it is the owner and he can still upload pictures
 */
if (!empty($xoopsUser)) {
    if ($isOwner && $xoopsModuleConfig['jobs_nb_pict'] > $pictures_number) {
        $maxfilebytes = $xoopsModuleConfig['jobs_maxfilesize'];
        $album_factory->renderFormSubmit($uid, $lid, $maxfilebytes, $xoopsTpl);
    }
}

/**
 * Let's get the user name of the owner of the album
 */
$owner      = new XoopsUser();
$identifier = $owner->getUnameFromId($uid);

/**
 * Adding to the module js and css of the lightbox and new ones
 */

if ($xoopsModuleConfig['' . $moduleDirName . '_lightbox'] == 1) {
    $header_lightbox = '<script type="text/javascript" src="assets/js/lightbox/js/prototype.js"></script>
<script type="text/javascript" src="assets/js/lightbox/js/scriptaculous.js?load=effects"></script>
<script type="text/javascript" src="assets/js/lightbox/js/lightbox.js"></script>
<link rel="stylesheet" href="assets/css/yogurt.css" type="text/css" media="screen">
<link rel="stylesheet" href="assets/js/lightbox/css/lightbox.css" type="text/css" media="screen">';
} else {
    $header_lightbox = '<link rel="stylesheet" href="assets/css/yogurt.css" type="text/css" media="screen">';
}

/**
 * Assigning smarty variables
 */

$sql    = 'SELECT name FROM ' . $xoopsDB->prefix('jobs_resume') . ' WHERE lid=' . addslashes($lid) . '';
$result = $xoopsDB->query($sql);
while (list($name) = $xoopsDB->fetchRow($result)) {
    $xoopsTpl->assign('lang_gtitle', "<a href='viewresume.php?lid=" . addslashes($lid) . "'>" . $name . '</a>');
    $xoopsTpl->assign('lang_showcase', _JOBS_SHOWCASE);
}

$xoopsTpl->assign('lang_not_premium', sprintf(_JOBS_BMCANHAVE, $xoopsModuleConfig['jobs_not_premium']));

$xoopsTpl->assign('lang_no_prem_nb', sprintf(_JOBS_PREMYOUHAVE, $pictures_number));

$upgrade = '<a href="premium.php"><b> ' . _JOBS_UPGRADE_NOW . '</b></a>';
$xoopsTpl->assign('lang_upgrade_now', $upgrade);
$xoopsTpl->assign('lang_max_nb_pict', sprintf(_JOBS_YOUCANHAVE, $xoopsModuleConfig['jobs_nb_pict']));
$xoopsTpl->assign('lang_nb_pict', sprintf(_JOBS_YOUHAVE, $pictures_number));
$xoopsTpl->assign('lang_albumtitle', sprintf(_JOBS_ALBUMTITLE, '<a href=' . XOOPS_URL . '/userinfo.php?uid=' . addslashes($uid) . '>' . $identifier . '</a>'));
$xoopsTpl->assign('path_uploads', $xoopsModuleConfig['jobs_link_upload']);
$xoopsTpl->assign('xoops_pagetitle', $xoopsModule->getVar('name') . ' - ' . $identifier . "'s album");
$xoopsTpl->assign('nome_modulo', $xoopsModule->getVar('name'));
$xoopsTpl->assign('lang_delete', _JOBS_DELETE);
$xoopsTpl->assign('lang_editdesc', _JOBS_EDITDESC);
$xoopsTpl->assign('isOwner', $isOwner);
$xoopsTpl->assign('permit', $permit);
$xoopsTpl->assign('xoops_module_header', $header_lightbox);

/**
 * Check if using Xoops or XoopsCube (by jlm69)
 */

$xCube = false;
if (preg_match('/^XOOPS Cube/', XOOPS_VERSION)) { // XOOPS Cube 2.1x
    $xCube = true;
}

/**
 * Verify Ticket (by jlm69)
 * If your site is XoopsCube it uses $xoopsGTicket for the token.
 * If your site is Xoops it uses xoopsSecurity for the token.
 */

//if ($xCube) {
//    $xoopsTpl->assign('securityToken', $GLOBALS['xoopsGTicket']->getTicketHtml(__LINE__));
//} else {
//    $xoopsTpl->assign('securityToken', $GLOBALS['xoopsSecurity']->getTokenHTML());
//}

/**
 * Closing the page
 */
require_once XOOPS_ROOT_PATH . '/footer.php';
