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

use XoopsModules\Jobs;

include __DIR__ . '/header.php';

/** @var Jobs\Helper $helper */
$helper = Jobs\Helper::getInstance();
$moduleDirName = basename(__DIR__);
$main_lang     = '_' . strtoupper($moduleDirName);
//require_once XOOPS_ROOT_PATH . "/modules/$moduleDirName/include/gtickets.php";

global $xoopsModule;
$pathIcon16 = \Xmf\Module\Admin::iconUrl('', 16);

$myts      = \MyTextSanitizer::getInstance();
$module_id = $xoopsModule->getVar('mid');
if (is_object($xoopsUser)) {
    $groups =& $xoopsUser->getGroups();
} else {
    $groups = XOOPS_GROUP_ANONYMOUS;
}
/** @var \XoopsGroupPermHandler $grouppermHandler */
$grouppermHandler = xoops_getHandler('groupperm');
$perm_itemid = \Xmf\Request::getInt('item_id', 0, 'GET');
//If no access
if (!$grouppermHandler->checkRight('jobs_view', $perm_itemid, $groups, $module_id)) {
    redirect_header(XOOPS_URL . '/modules/profile/', 3, _NOPERM);
}
// Check submit rights â€“ added line - recommended by GreenFlatDog
$jobs_submitter = $grouppermHandler->checkRight('jobs_submit', $perm_itemid, $groups, $module_id);

include XOOPS_ROOT_PATH . "/modules/$moduleDirName/include/functions.php";

$mytree = new Jobs\JobTree($xoopsDB->prefix('jobs_categories'), 'cid', 'pid');

ExpireJob();

$default_sort = $helper->getConfig('jobs_listing_sortorder');

$default_orderby = 'dateD';

$cid     = \Xmf\Request::getInt('cid', 0, 'GET');
$min     = !isset($_REQUEST['min']) ? null : $_REQUEST['min'];
$show    = !isset($_REQUEST['show']) ? null : $_REQUEST['show'];
$orderby = !isset($_REQUEST['orderby']) ? $default_orderby : $_REQUEST['orderby'];

$GLOBALS['xoopsOption']['template_main'] = 'jobs_category.tpl';
include XOOPS_ROOT_PATH . '/header.php';

$cid  = ($cid > 0) ? $cid : 0;
$min  = ((int)$min > 0) ? (int)$min : 0;
$show = ((int)$show > 0) ? (int)$show : $helper->getConfig('' . $moduleDirName . '_perpage');
$max  = $min + $show;
if (isset($orderby)) {
    $xoopsTpl->assign('sort_active', $orderby); // added for compact sort
    $orderby = jobs_convertorderbyin($orderby);
} else {
    //   To change the red arrows in the active sort to another option
    //   you need to change dateD below to your choice, Default is 'dateD'
    //   options are -  'dateD'   'dateA'  'titleA'   'titleD'   'viewA'   'viewD'   'townA'   'townD'   'stateA'   'stateD'
    $xoopsTpl->assign('show_active', 'dateD');
    $orderby = $default_sort;
}

//  $orderbyTrans = jobs_convertorderbytrans($orderby);
$xoopsTpl->assign('jobs_submitter', $jobs_submitter); // added line
$xoopsTpl->assign('cid', $cid);
$xoopsTpl->assign('add_from', _JOBS_ADDFROM . ' ' . $xoopsConfig['sitename']);
$xoopsTpl->assign('add_from_title', _JOBS_ADDFROM);
$xoopsTpl->assign('add_from_sitename', $xoopsConfig['sitename']);
$xoopsTpl->assign('add_listing', "<a href='addlisting.php?cid=" . addslashes($cid) . "'>" . _JOBS_ADDLISTING2 . '</a>');
$banner = xoops_getbanner();
$xoopsTpl->assign('banner', $banner);
$index_code_place = $helper->getConfig('jobs_index_code_place');
$use_extra_code   = $helper->getConfig('jobs_use_index_code');
$jobs_use_banner  = $helper->getConfig('jobs_use_banner');
$index_extra_code = $helper->getConfig('jobs_index_code');
$xoopsTpl->assign('use_extra_code', $use_extra_code);
$xoopsTpl->assign('jobs_use_banner', $jobs_use_banner);
$xoopsTpl->assign('index_extra_code', $index_extra_code);
$xoopsTpl->assign('index_code_place', $index_code_place);
$xoopsTpl->assign('perpage', $helper->getConfig('' . $moduleDirName . '_perpage'));

$categories = jobs_MygetItemIds('' . $moduleDirName . '_view');
if (is_array($categories) && count($categories) > 0) {
    if (!in_array($cid, $categories)) {
        redirect_header(XOOPS_URL . "/modules/$moduleDirName/index.php", 3, _NOPERM);
    }
} else { // User can't see any category
    redirect_header(XOOPS_URL . '/index.php', 3, _NOPERM);
}

$arrow      = '<img src="' . XOOPS_URL . "/modules/$moduleDirName/assets/images/arrow.gif\" alt=\"&raquo;\">";
$pathstring = "<a href='index.php'>" . $moduleDirName . '</a>';
$pathstring .= $mytree->getNicePathFromId($cid, 'title', 'jobscat.php?');
$xoopsTpl->assign('module_name', $xoopsModule->getVar('name'));
$xoopsTpl->assign('category_path', $pathstring);
$xoopsTpl->assign('category_id', $cid);

if ('1' == $helper->getConfig('jobs_show_company')) {
    $show_company = true;
    $xoopsTpl->assign('show_company', true);
} else {
    $show_company = false;
}
if ('0' == $helper->getConfig('jobs_show_state')) {
    $xoopsTpl->assign('show_state', '1');
} else {
    $xoopsTpl->assign('show_state', '0');
}

$cat_perms = '';
if (is_array($categories) && count($categories) > 0) {
    $cat_perms .= ' AND cid IN (' . implode(',', $categories) . ') ';
}

$countpremium = $xoopsDB->query('select COUNT(*) FROM ' . $xoopsDB->prefix('jobs_listing') . " where  premium='1' AND valid='1' AND cid=" . $xoopsDB->escape($cid) . $cat_perms);
list($premrow) = $xoopsDB->fetchRow($countpremium);
$premrows = $premrow;

if ('0' != $premrows) {
    $xoopsTpl->assign('premrows', $premrows);
    $xoopsTpl->assign('sponsored', _JOBS_SPONSORED_LISTINGS);
}

$countresult = $xoopsDB->query('select COUNT(*) FROM ' . $xoopsDB->prefix('jobs_listing') . " where valid='1' AND cid=" . $xoopsDB->escape($cid) . $cat_perms);
list($trow) = $xoopsDB->fetchRow($countresult);
$trows = $trow;

$result = $xoopsDB->query('select cid, pid, title from ' . $xoopsDB->prefix('' . $moduleDirName . '_categories') . ' where cid=' . $xoopsDB->escape($cid) . " $cat_perms");
list($cid, $pid, $title) = $xoopsDB->fetchRow($result);

$xoopsTpl->assign('xoops_pagetitle', $title);
$xoopsTpl->assign('all_listings', _JOBS_LISTINGS);
$xoopsTpl->assign('cat_title', $title);
$xoopsTpl->assign('jobs_all', _JOBS_ALL);

$arr = [];
$arr = $mytree->getFirstChild($cid, 'title');
if (count($arr) > 0) {
    $scount = 1;
    foreach ($arr as $ele) {
        $sub_arr         = [];
        $sub_arr         = $mytree->getFirstChild($ele['cid'], 'title');
        $space           = 0;
        $chcount         = 0;
        $infercategories = '';
        foreach ($sub_arr as $sub_ele) {
            $chtitle = $myts->undoHtmlSpecialChars($sub_ele['title']);
            if ($chcount > $helper->getConfig('' . $moduleDirName . '_perpage')) {
                $infercategories = '...';
                break;
            }
            if ($space > 0) {
                $infercategories = ', ';
            }
            $infercategories = '<a href="' . XOOPS_URL . '/modules/' . $xoopsModule->getVar('dirname') . '/jobscat.php?cid=' . $sub_ele['cid'] . '">' . $chtitle . '</a>';
            ++$space;
            ++$chcount;
        }
        $xoopsTpl->append('subcategories', [
            'title'           => $myts->undoHtmlSpecialChars($ele['title']),
            'id'              => $ele['cid'],
            'infercategories' => $infercategories,
            'totallistings'   => jobs_getTotalItems($ele['cid']),
            'count'           => $scount
        ]);
        ++$scount;
        $xoopsTpl->assign('lang_subcat', constant($main_lang . '_AVAILAB'));
    }
} else {
    $xoopsTpl->assign('there_are_listings', true);
}
$pagenav = '';

if (0 == $trows) { // the zero option added
    $xoopsTpl->assign('no_jobs_to_show', _JOBS_NOANNINCAT);
} elseif ($trows > '0') {
    $xoopsTpl->assign('last_head', _JOBS_THE . ' ' . $helper->getConfig('jobs_new_jobs_count') . ' ' . _JOBS_LASTADD);
    $xoopsTpl->assign('last_head_title', _JOBS_TITLE);
    $xoopsTpl->assign('last_head_company', _JOBS_COMPANY);
    $xoopsTpl->assign('last_head_price', _JOBS_PRICE);
    $xoopsTpl->assign('last_head_date', _JOBS_DATE);
    $xoopsTpl->assign('last_head_local', _JOBS_LOCAL2);
    $xoopsTpl->assign('last_head_state', _JOBS_STATE);
    $xoopsTpl->assign('last_head_views', _JOBS_VIEW);
    $xoopsTpl->assign('last_head_photo', _JOBS_PHOTO);

    $xoopsTpl->assign('min', $min);
    //  we need to examine $orderby and CAST alpha fields that we want to sort numerically. $orderby format is [field ASC] or [field DESC]
    $fields_tosort = ['view', 'price']; // add others if needed
    $orderby1      = $orderby;
    foreach ($fields_tosort as $f) {
        $orderby1 = str_replace($f, 'CAST(' . $f . ' AS SIGNED)', $orderby1);
    }

    $cat_perms = '';
    if (is_array($categories) && count($categories) > 0) {
        $cat_perms .= ' AND cid IN (' . implode(',', $categories) . ') ';
    }

    $sql     = 'select lid, cid, title, status, expire, type, company, price, typeprice, date, town, state, valid, premium, photo, view from ' . $xoopsDB->prefix('jobs_listing') . ' WHERE cid=' . $xoopsDB->escape($cid) . " AND valid='1' AND status!='0' $cat_perms order by $orderby1";
    $result1 = $xoopsDB->query($sql, $show, $min);

    if ($trows > '1') {
        $xoopsTpl->assign('show_nav', true);
        $xoopsTpl->assign('lang_sortby', _JOBS_SORTBY);
        $xoopsTpl->assign('lang_title', _JOBS_TITLE);
        $xoopsTpl->assign('lang_titleatoz', _JOBS_TITLEATOZ);
        $xoopsTpl->assign('lang_titleztoa', _JOBS_TITLEZTOA);
        $xoopsTpl->assign('lang_date', _JOBS_DATE);
        $xoopsTpl->assign('lang_dateold', _JOBS_DATEOLD);
        $xoopsTpl->assign('lang_datenew', _JOBS_DATENEW);
        $xoopsTpl->assign('lang_company', _JOBS_COMPANY);
        $xoopsTpl->assign('lang_companyatoz', _JOBS_COMPANYATOZ);
        $xoopsTpl->assign('lang_companyztoa', _JOBS_COMPANYZTOA);
        $xoopsTpl->assign('lang_local', _JOBS_LOCAL2);
        $xoopsTpl->assign('lang_localatoz', _JOBS_LOCALATOZ);
        $xoopsTpl->assign('lang_localztoa', _JOBS_LOCALZTOA);
        $xoopsTpl->assign('lang_state', _JOBS_STATE);
        $xoopsTpl->assign('lang_stateatoz', _JOBS_STATEATOZ);
        $xoopsTpl->assign('lang_stateztoa', _JOBS_STATEZTOA);
        $xoopsTpl->assign('lang_popularity', _JOBS_POPULARITY);
        $xoopsTpl->assign('lang_popularityleast', _JOBS_POPULARITYLTOM);
        $xoopsTpl->assign('lang_popularitymost', _JOBS_POPULARITYMTOL);
        $xoopsTpl->assign('lang_cursortedby', sprintf(_JOBS_CURSORTEDBY, jobs_convertorderbytrans($orderby)));
    }

    while (false !== (list($lid, $cid, $title, $status, $expire, $type, $company, $price, $typeprice, $date, $town, $state, $valid, $premium, $photo, $vu) = $xoopsDB->fetchRow($result1))) {
        $a_item     = [];
        $title      = $myts->undoHtmlSpecialChars($title);
        $status     = $myts->htmlSpecialChars($status);
        $expire     = $myts->htmlSpecialChars($expire);
        $type       = $myts->htmlSpecialChars($type);
        $company    = $myts->undoHtmlSpecialChars($company);
        $price      = $myts->htmlSpecialChars($price);
        $town       = $myts->htmlSpecialChars($town);
        $state      = $myts->htmlSpecialChars($state);
        $premium    = $myts->htmlSpecialChars($premium);
        $useroffset = '';

        if (!XOOPS_USE_MULTIBYTES) {
            if (strlen($title) >= 40) {
                $title = substr($title, 0, 39) . '...';
            }
        }
        if ($xoopsUser) {
            $timezone = $xoopsUser->timezone();
            if (isset($timezone)) {
                $useroffset = $xoopsUser->timezone();
            } else {
                $useroffset = $xoopsConfig['default_TZ'];
            }
        }

        $date      = ($useroffset * 3600) + $date;
        $startdate = (time() - (86400 * $helper->getConfig('jobs_countday')));
        if ($startdate < $date) {
            $newitem       = '<img src="' . XOOPS_URL . "/modules/$moduleDirName/assets/images/newred.gif\">";
            $a_item['new'] = $newitem;
        }
        $date = formatTimestamp($date, 's');
        if ($xoopsUser) {
            if ($xoopsUser->isAdmin()) {
                $a_item['admin'] = "<a href='admin/main.php?op=ModJob&amp;lid=" . addslashes($lid) . "'><img src=" . $pathIcon16 . "/edit.png alt='" . _JOBS_MODADMIN . "' title='" . _JOBS_MODADMIN . "'></a>";
            }
        }
        $a_item['type']    = $type;
        $a_item['title']   = "<a href='viewjobs.php?lid=" . addslashes($lid) . "'>$title</a>";
        $a_item['company'] = $company;
        if ($price > 0) {
            $a_item['price'] = '' . $helper->getConfig('jobs_money') . " $price";
            // Add $price_typeprice by Tom
            $a_item['price_typeprice'] = (string)$typeprice;
        } else {
            $a_item['price']           = '';
            $a_item['price_typeprice'] = (string)$typeprice;
        }
        $a_item['status'] = $status;
        $a_item['date']   = $date;

        if ('1' == $helper->getConfig('jobs_show_state')) {
            $state_name = jobs_getStateNameFromId($state);

            $a_item['state'] = $state_name;
        }
        $a_item['town'] = $town;

        if ($photo) {
            $a_item['photo'] = "<a href=\"javascript:CLA('display-image.php?lid=" . addslashes($lid) . "')\"><img src=\"assets/images/photo.gif\" border=0 width=15 height=11 alt='" . _JOBS_IMGPISP . "'></a>";
        }
        $a_item['views']   = $vu;
        $a_item['premium'] = $premium;
        $xoopsTpl->append('items', $a_item);
    }

    $cid = \Xmf\Request::getInt('cid', 0, 'GET');

    $orderby = jobs_convertorderby($orderby);

    //Calculates how many pages exist.  Which page one should be on, etc...
    $linkpages = ceil($trows / $show);
    //Page Numbering
    if (1 != $linkpages && 0 != $linkpages) {
        $prev = $min - $show;
        if ($prev >= 0) {
            $pagenav .= "<a href='jobscat.php?cid=$cid&min=$prev&orderby=$orderby&show=$show'><b><u>&laquo;</u></b></a> ";
        }
        $counter     = 1;
        $currentpage = ($max / $show);
        while ($counter <= $linkpages) {
            $mintemp = ($show * $counter) - $show;
            if ($counter == $currentpage) {
                $pagenav .= "<b>($counter)</b> ";
            } else {
                $pagenav .= "<a href='jobscat.php?cid=$cid&min=$mintemp&orderby=$orderby&show=$show'>$counter</a> ";
            }
            ++$counter;
        }
        if ($trows > $max) {
            $pagenav .= "<a href='jobscat.php?cid=$cid&min=$max&orderby=$orderby&show=$show'>";
            $pagenav .= '<b><u>&raquo;</u></b></a>';
        }
    }
    $xoopsTpl->assign('nav_page', $pagenav);
}

include XOOPS_ROOT_PATH . '/footer.php';
