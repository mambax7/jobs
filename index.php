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

require_once __DIR__ . '/header.php';

/** @var Jobs\Helper $helper */
$helper = Jobs\Helper::getInstance();

$moduleDirName = basename(__DIR__);
$main_lang     = '_' . strtoupper($moduleDirName);
//require_once XOOPS_ROOT_PATH . "/modules/$moduleDirName/include/gtickets.php";

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
    redirect_header(XOOPS_URL . '/user.php', 3, _NOPERM);
}
// Check submit rights â€“ added line - recommended by GreenFlatDog
$jobs_submitter = $grouppermHandler->checkRight('jobs_submit', $perm_itemid, $groups, $module_id);

include XOOPS_ROOT_PATH . "/modules/$moduleDirName/include/functions.php";

$mytree    = new Jobs\JobTree($xoopsDB->prefix('jobs_categories'), 'cid', 'pid');
$statetree = new Jobs\JobTree($xoopsDB->prefix('jobs_region'), 'rid', 'pid');

ExpireJob();

$GLOBALS['xoopsOption']['template_main'] = 'jobs_index.tpl';
include XOOPS_ROOT_PATH . '/header.php';
$xoopsTpl->assign('xmid', $xoopsModule->getVar('mid'));
$xoopsTpl->assign('add_from', _JOBS_ADDFROM . ' ' . $xoopsConfig['sitename']);
$xoopsTpl->assign('add_from_title', _JOBS_ADDFROM);
$xoopsTpl->assign('add_from_sitename', $xoopsConfig['sitename']);
$xoopsTpl->assign('add_from_title', _JOBS_ADDFROM);
$xoopsTpl->assign('index_head', _JOBS_INDEX_HEAD);
$xoopsTpl->assign('show_res_index', _JOBS_RES_SHOW_INDEX);
$xoopsTpl->assign('employers', _JOBS_EMPLOYERS);
$newlistings = $helper->getConfig('jobs_countday');
$xoopsTpl->assign('how_new', $newlistings);
$xoopsTpl->assign('days', _JOBS_DAY);
$xoopsTpl->assign('about_new_listings', _JOBS_ABOUT_NEW_LISTINGS);
$is_resume = 0;
$xoopsTpl->assign('is_resume', $is_resume);

if ('1' == $helper->getConfig('jobs_offer_search')) { // added 'if' block: controls search section in template
    $xoopsTpl->assign('offer_search', true);
    $xoopsTpl->assign('all_words', _JOBS_ALL_WORDS);
    $xoopsTpl->assign('any_words', _JOBS_ANY_WORDS);
    $xoopsTpl->assign('exact_match', _JOBS_EXACT_MATCH);
    $xoopsTpl->assign('search_listings', _JOBS_SEARCH_LISTINGS);
    $xoopsTpl->assign('bystate', _JOBS_SEARCH_BYSTATE);
    $xoopsTpl->assign('bycategory', _JOBS_SEARCH_BYCATEGORY);
    $xoopsTpl->assign('keywords', _JOBS_SEARCH_KEYWORDS);
}

$index_banner = xoops_getbanner();
$xoopsTpl->assign('index_banner', $index_banner);
$index_code_place = $helper->getConfig('jobs_index_code_place');
$use_extra_code   = $helper->getConfig('jobs_use_index_code');
$jobs_use_banner  = $helper->getConfig('jobs_use_banner');
$index_extra_code = $helper->getConfig('jobs_index_code');
$xoopsTpl->assign('use_extra_code', $use_extra_code);
$xoopsTpl->assign('jobs_use_banner', $jobs_use_banner);
$xoopsTpl->assign('index_extra_code', '<html>' . $index_extra_code . '</html>');
$xoopsTpl->assign('index_code_place', $index_code_place);

ob_start();
$mytree->makeMySearchSelBox('title', 'title', '', 1, 'by_cat');
$by_cat = ob_get_contents();
ob_end_clean();
$xoopsTpl->assign('by_cat', $by_cat);

ob_start();
$statetree->makeMyStateSelBox('name', 'rid', '', 1, 'by_state');
$by_state = ob_get_contents();
ob_end_clean();
$xoopsTpl->assign('by_state', $by_state);

//   $istheirs = false;
if ($xoopsUser) {
    $member_usid = $xoopsUser->uid();
    $xoopsTpl->assign('jobs_submitter', $jobs_submitter); //added line

    $result = $xoopsDB->query('SELECT comp_id, comp_name, comp_usid, comp_user1, comp_user2  FROM ' . $xoopsDB->prefix('jobs_companies') . ' WHERE ' . $member_usid . ' IN (comp_usid,comp_user1,comp_user2)');
    while (false !== ($myrow = $xoopsDB->fetchArray($result))) {
        $a_comp   = [];
        $istheirs = true;
        $xoopsTpl->assign('istheirs', $istheirs);

        $xoopsTpl->assign('comp_listurl', 'members.php?comp_id=');
        $a_comp['comp_id']   = $myrow['comp_id'];
        $a_comp['comp_name'] = $myrow['comp_name'];
        $xoopsTpl->append('companies', $a_comp);
        $xoopsTpl->assign('member_intro', _JOBS_VIEW_YOUR_LISTINGS);
    }
}

if ('1' == $helper->getConfig('jobs_show_resume')) {
    $xoopsTpl->assign('use_resumes', '1');
} else {
    $xoopsTpl->assign('use_resumes', '0');
}

if ('1' == $helper->getConfig('jobs_moderated')) {
    $result = $xoopsDB->query('SELECT  COUNT(*)  FROM ' . $xoopsDB->prefix('jobs_listing') . " WHERE valid='0'");
    list($propo) = $xoopsDB->fetchRow($result);
    $xoopsTpl->assign('moderated', true);

    if ($xoopsUser) {
        if ($xoopsUser->isAdmin()) {
            $xoopsTpl->assign('admin_block', _JOBS_ADMINCADRE);
            if (0 == $propo) {
                $xoopsTpl->assign('confirm_ads', _JOBS_NO_JOBS);
            } else {
                $xoopsTpl->assign('confirm_ads', _JOBS_THEREIS . " $propo  " . _JOBS_WAIT . '<br><a href="admin/jobs.php">' . _JOBS_SEEIT . '</a>');
            }
        }
    }
}

if ('1' == $helper->getConfig('jobs_moderate_resume')) {
    $result1 = $xoopsDB->query('SELECT  COUNT(*)  FROM ' . $xoopsDB->prefix('jobs_resume') . " WHERE valid='0'");
    list($res_propo) = $xoopsDB->fetchRow($result1);
    $xoopsTpl->assign('moderated', true);
    if ($xoopsUser) {
        if ($xoopsUser->isAdmin()) {
            $xoopsTpl->assign('admin_block', _JOBS_ADMINCADRE);
            if (0 == $res_propo) {
                $xoopsTpl->assign('confirm_resume', _JOBS_RES_NO_JOBS);
            } else {
                $xoopsTpl->assign('confirm_resume', _JOBS_RES_THEREIS . " $res_propo  " . _JOBS_RES_WAIT . '<br><a href="admin/resumes.php">' . _JOBS_RES_SEEIT . '</a>');
            }
        }
    }
}

$sql = 'SELECT cid, title, img FROM ' . $xoopsDB->prefix('' . $moduleDirName . '_categories') . ' WHERE pid = 0 ';

$categories = jobs_MygetItemIds('jobs_view');
if (is_array($categories) && count($categories) > 0) {
    $sql .= ' AND cid IN (' . implode(',', $categories) . ') ';
} else { // User can't see any category
    redirect_header(XOOPS_URL . '/index.php', 3, _NOPERM);
}
$sql    .= 'ORDER BY title';
$result = $xoopsDB->query($sql);

$count   = 1;
$content = '';
while (false !== ($myrow = $xoopsDB->fetchArray($result))) {
    $title = $myts->undoHtmlSpecialChars($myrow['title']);
    $cid   = (int)$myrow['cid'];
    $xoopsTpl->assign('search_cat', $main_lang . '_BYCATEGORY');

    if ($myrow['img'] && 'http://' !== $myrow['img']) {
        $cat_img = $myts->htmlSpecialChars($myrow['img']);
        $img     = '<a href="jobscat.php?cid=' . $myrow['cid'] . "\"><img src='" . XOOPS_URL . "/modules/$moduleDirName/assets/images/cat/" . $cat_img . "' align='middle' alt=''></a>";
    } else {
        $img = '';
    }
    $totallisting = jobs_getTotalItems($myrow['cid'], 1);
    $content      .= $title . ' ';

    // get child category objects
    $arr = [];
    if (in_array($myrow['cid'], $categories)) {
        $arr           = $mytree->getFirstChild($myrow['cid'], '' . $helper->getConfig('' . $moduleDirName . '_cat_sortorder') . '');
        $space         = 0;
        $chcount       = 0;
        $subcategories = '';
        if ('1' == $helper->getConfig('' . $moduleDirName . '_display_subcat')) {
            foreach ($arr as $ele) {
                $newsubcat = jobs_subcatnew($ele['cid']);

                if (in_array($ele['cid'], $categories)) {
                    $chtitle = $myts->undoHtmlSpecialChars($ele['title']);
                    if ($chcount > $helper->getConfig('' . $moduleDirName . '_subcat_num')) {
                        $subcategories .= ', ...';
                        break;
                    }
                    if ($space > 0) {
                        $subcategories .= '<br>';
                    }

                    if (true === $newsubcat) {
                        $subcategories .= '<a style="color:red;" href="jobscat.php?cid=' . $ele['cid'] . '">' . $chtitle . '</a>';
                    } else {
                        $subcategories .= '<a href="jobscat.php?cid=' . $ele['cid'] . '">' . $chtitle . '</a>';
                    }

                    //                  $subcategories
                    //                      .= "<a href=\"" . XOOPS_URL . "/modules/$moduleDirName/jobscat.php?cid=" . $ele['cid'] . "\">"
                    //                      . $chtitle . "</a>";
                    ++$space;
                    ++$chcount;
                    $content .= $ele['title'] . ' ';
                }
            }
        }
        $xoopsTpl->append('categories', [
            'image'         => $img,
            'id'            => $myrow['cid'],
            'title'         => $myts->undoHtmlSpecialChars($myrow['title']),
            'new'           => jobs_categorynewgraphic($myrow['cid']),
            'subcategories' => $subcategories,
            'totallisting'  => $totallisting,
            'count'         => $count
        ]);
        ++$count;
    }
}
$cat_perms = '';
if (is_array($categories) && count($categories) > 0) {
    $cat_perms .= ' AND cid IN (' . implode(',', $categories) . ') ';
}
$xoopsTpl->assign('cat_count', $count - 1);

$usesubcats = $helper->getConfig('' . $moduleDirName . '_display_subcat');
$xoopsTpl->assign('usesubcats', $usesubcats);

list($ann) = $xoopsDB->fetchRow($xoopsDB->query('select  COUNT(*)  FROM ' . $xoopsDB->prefix('jobs_listing') . " WHERE valid='1' $cat_perms"));
list($catt) = $xoopsDB->fetchRow($xoopsDB->query('SELECT  COUNT(*)  FROM ' . $xoopsDB->prefix('jobs_categories') . ''));

$xoopsTpl->assign('clickbelow', _JOBS_CLICKBELOW);
$xoopsTpl->assign('add_listing', "<a href='addlisting.php?cid=" . addslashes($cid) . "'>" . _JOBS_ADDLISTING2 . '</a>');
$xoopsTpl->assign('show_resume', "<a href='resumes.php'>" . _JOBS_RESUME2 . '</a>');
$xoopsTpl->assign('total_listings', _JOBS_ACTUALY . " $ann " . _JOBS_LISTINGS . ' ' . _JOBS_DATABASE);
if ('1' == $helper->getConfig('jobs_moderated')) {
    $xoopsTpl->assign('total_confirm', _JOBS_AND . " $propo " . _JOBS_WAIT3);
}

if ('1' == $helper->getConfig('jobs_new_jobs')) {
    $cat_perms = '';
    if (is_array($categories) && count($categories) > 0) {
        $cat_perms .= ' AND cid IN (' . implode(',', $categories) . ') ';
    }
    $result = $xoopsDB->query('select lid, title, status, expire, type, company, price, typeprice, date, town, state, valid, photo, view FROM '
                              . $xoopsDB->prefix('jobs_listing')
                              . " WHERE valid='1' AND status!='0' $cat_perms ORDER BY date DESC LIMIT "
                              . $helper->getConfig('jobs_new_jobs_count')
                              . '');
    if ($result) {
        $xoopsTpl->assign('last_head', _JOBS_THE . ' ' . $helper->getConfig('jobs_new_jobs_count') . ' ' . _JOBS_LASTADD);
        $xoopsTpl->assign('last_head_title', _JOBS_TITLE);
        $xoopsTpl->assign('last_head_company', _JOBS_COMPANY);
        $xoopsTpl->assign('last_head_price', _JOBS_PRICE);
        $xoopsTpl->assign('last_head_date', _JOBS_DATE);
        $xoopsTpl->assign('last_head_local', _JOBS_LOCAL2);
        $xoopsTpl->assign('last_head_views', _JOBS_VIEW);
        $xoopsTpl->assign('last_head_photo', _JOBS_PHOTO);

        if ('1' == $helper->getConfig('jobs_show_company')) {
            $show_company = true;
            $xoopsTpl->assign('show_company', true);
        } else {
            $show_company = false;
        }
        $rank = 1;
        while (false !== (list($lid, $title, $status, $expire, $type, $company, $price, $typeprice, $date, $town, $state, $valid, $photo, $vu) = $xoopsDB->fetchRow($result))) {
            $title     = $myts->undoHtmlSpecialChars($title);
            $status    = $myts->htmlSpecialChars($status);
            $expire    = $myts->htmlSpecialChars($expire);
            $type      = $myts->htmlSpecialChars($type);
            $company   = $myts->undoHtmlSpecialChars($company);
            $price     = $myts->htmlSpecialChars($price);
            $typeprice = $myts->htmlSpecialChars($typeprice);
            $town      = $myts->htmlSpecialChars($town);
            $state     = $myts->htmlSpecialChars($state);
            if (!XOOPS_USE_MULTIBYTES) {
                if (strlen($title) >= 40) {
                    $title = substr($title, 0, 39) . '...';
                }
            }
            $a_item     = [];
            $useroffset = '';
            if ($xoopsUser) {
                $timezone = $xoopsUser->timezone();
                if (isset($timezone)) {
                    $useroffset = $xoopsUser->timezone();
                } else {
                    $useroffset = $xoopsConfig['default_TZ'];
                }
            }
            $date          = ($useroffset * 3600) + $date;
            $a_item['new'] = jobs_listingnewgraphic($date);
            $date          = formatTimestamp($date, 's');
            if ($xoopsUser) {
                if ($xoopsUser->isAdmin()) {
                    $a_item['admin'] = "<a href='admin/modjobs.php?lid=" . addslashes($lid) . "'><img src=" . $pathIcon16 . "/edit.png alt='" . _JOBS_MODADMIN . "' title='" . _JOBS_MODADMIN . "'></a>";
                }
            }

            $a_item['title']   = "<a href='viewjobs.php?lid=" . addslashes($lid) . "'>$title</a>";
            $a_item['company'] = $company;
            $a_item['type']    = $type;
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
            $a_item['town']   = '';
            if ($town) {
                $a_item['town'] = $town;
            }
            if ('1' == $helper->getConfig('jobs_show_state')) {
                if ($state) {
                    $state_name      = jobs_getStateNameFromId($state);
                    $a_item['state'] = $state_name;
                }
            }

            if ($photo) {
                $a_item['photo'] = "<a href=\"javascript:CLA('display-image.php?lid=" . addslashes($lid) . "')\"><img src=\"assets/images/photo.gif\" border=0 width=15 height=11 alt='" . _JOBS_IMGPISP . "'></a>";
            }
            $a_item['views'] = $vu;
            ++$rank;
            $xoopsTpl->append('items', $a_item);
        }
    }
}

include XOOPS_ROOT_PATH . '/footer.php';
