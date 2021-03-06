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
 * @author      XOOPS Development Team
 */

// ------------------------------------------------------------------------- //
//               E-Xoops: Content Management for the Masses                  //
//                       < http://www.e-xoops.com >                          //
// ------------------------------------------------------------------------- //
// Original Author: Pascal Le Boustouller                                    //
// Author Website : pascal.e-xoops@perso-search.com                          //
// Licence Type   : GPL                                                      //
// ------------------------------------------------------------------------- //

$moduleDirName = basename(dirname(__DIR__));

//require_once XOOPS_ROOT_PATH . "/modules/$moduleDirName/include/gtickets.php";
require_once XOOPS_ROOT_PATH . "/modules/$moduleDirName/include/functions.php";
require_once XOOPS_ROOT_PATH . "/modules/$moduleDirName/include/resume_functions.php";
$is_resume = !isset($_REQUEST['is_resume']) ? null : $_REQUEST['is_resume'];

if (!empty($_GET['by_state'])) {
    $by_state = $_GET['by_state'];
} elseif (!empty($_POST['by_state'])) {
    $by_state = $_POST['by_state'];
} else {
    $by_state = '';
}

if (!empty($_GET['by_cat'])) {
    $by_cat = $_GET['by_cat'];
} elseif (!empty($_POST['by_cat'])) {
    $by_cat = $_POST['by_cat'];
} else {
    $by_cat = '';
}

/**
 * @param $queryarray
 * @param $andor
 * @param $limit
 * @param $offset
 * @param $userid
 *
 * @return array
 */
function jobs_search($queryarray, $andor, $limit, $offset, $userid)
{
    global $xoopsDB, $xoopsUser, $is_resume, $by_state, $by_cat, $state_name;

    if (1 != $is_resume) {
        $cat_perms  = '';
        $categories = jobs_MygetItemIds('jobs_view');
        if (is_array($categories) && count($categories) > 0) {
            $cat_perms = ' AND cid IN (' . implode(',', $categories) . ') ';
        }

        $sql = 'SELECT  lid,cid,title,type,company,desctext,requirements,tel,price,contactinfo,town,state,usid,valid,date FROM ' . $xoopsDB->prefix('jobs_listing') . " WHERE valid='1'  AND date<=" . time() . $cat_perms;

        if (0 != $userid) {
            $sql .= ' AND usid=' . $userid . ' ';
        }

        if (('' != $by_state) && ('' != $by_cat)) {

            // because count() returns 1 even if a supplied variable
            // is not an array, we must check if $querryarray is really an array
            if (is_array($queryarray) && $count = count($queryarray)) {
                $sql .= " AND ((cid LIKE '$by_cat' AND state LIKE '$by_state')";
                for ($i = 1; $i < $count; ++$i) {
                    $sql .= " $andor ";
                    $sql .= "(cid LIKE '$by_cat' AND state LIKE '$by_state')";
                }
                $sql .= ') ';
            }
        } elseif ('' != $by_state) {

            // because count() returns 1 even if a supplied variable
            // is not an array, we must check if $querryarray is really an array
            if (is_array($queryarray) && $count = count($queryarray)) {
                $sql .= " AND ((state LIKE '$by_state')";
                for ($i = 1; $i < $count; ++$i) {
                    $sql .= " $andor ";
                    $sql .= "(state LIKE '$by_state')";
                }
                $sql .= ') ';
            }
        } elseif ('' != $by_cat) {

            // because count() returns 1 even if a supplied variable
            // is not an array, we must check if $querryarray is really an array
            if (is_array($queryarray) && $count = count($queryarray)) {
                $sql .= " AND ((cid LIKE '$by_cat')";
                for ($i = 1; $i < $count; ++$i) {
                    $sql .= " $andor ";
                    $sql .= "(cid LIKE '$by_cat')";
                }
                $sql .= ') ';
            }
        } else {

            // because count() returns 1 even if a supplied variable
            // is not an array, we must check if $querryarray is really an array
            if (is_array($queryarray) && $count = count($queryarray)) {
                $sql .= " AND ((title LIKE '%$queryarray[0]%' OR type LIKE '%$queryarray[0]%' OR company LIKE '%$queryarray[0]%' OR desctext LIKE '%$queryarray[0]%' OR requirements LIKE '%$queryarray[0]%' OR tel LIKE '%$queryarray[0]%' OR price LIKE '%$queryarray[0]%' OR contactinfo LIKE '%$queryarray[0]%' OR town LIKE '%$queryarray[0]%' OR state LIKE '%$queryarray[0]%')";
                for ($i = 1; $i < $count; ++$i) {
                    $sql .= " $andor ";
                    $sql .= "(title LIKE '%$queryarray[i]%' OR type LIKE '%$queryarray[i]%' OR company LIKE '%$queryarray[i]%' OR desctext LIKE '%$queryarray[i]%' OR requirements LIKE '%$queryarray[i]%' OR tel LIKE '%$queryarray[i]%' OR price LIKE '%$queryarray[i]%' OR contactinfo LIKE '%$queryarray[i]%' OR town LIKE '%$queryarray[i]%' OR state LIKE '%$queryarray[i]%')";
                }
                $sql .= ') ';
            }
        }
        $sql    .= 'ORDER BY date DESC';
        $result = $xoopsDB->query($sql, $limit, $offset);
        $ret    = [];
        $i      = 0;
        while (false !== ($myrow = $xoopsDB->fetchArray($result))) {
            $statename = jobs_getStateNameFromId($myrow['state']);

            $ret[$i]['image']   = 'assets/images/cat/default.gif';
            $ret[$i]['link']    = 'viewjobs.php?lid=' . $myrow['lid'] . '';
            $ret[$i]['title']   = $myrow['title'];
            $ret[$i]['company'] = $myrow['company'];
            $ret[$i]['type']    = $myrow['type'];
            $ret[$i]['town']    = $myrow['town'];
            $ret[$i]['state']   = $statename;
            $ret[$i]['time']    = $myrow['date'];
            $ret[$i]['uid']     = $myrow['usid'];
            ++$i;
        }
    } else {
        $rescat_perms  = '';
        $rescategories = resume_MygetItemIds('resume_view');
        if (is_array($rescategories) && count($rescategories) > 0) {
            $rescat_perms = ' AND cid IN (' . implode(',', $rescategories) . ') ';
        }

        $sql = 'SELECT lid, cid, name, title, exp, expire, private, salary, typeprice, date, usid, town, state, valid FROM ' . $xoopsDB->prefix('jobs_resume') . " WHERE valid='1' and date<=" . time() . " $rescat_perms";

        if (0 != $userid) {
            $sql .= ' AND usid=' . $userid . ' ';
        }

        if (('' != $by_state) && ('' != $by_cat)) {

            // because count() returns 1 even if a supplied variable
            // is not an array, we must check if $querryarray is really an array
            if (is_array($queryarray) && $count = count($queryarray)) {
                $sql .= " AND ((cid LIKE '$by_cat' AND state LIKE '$by_state')";
                for ($i = 1; $i < $count; ++$i) {
                    $sql .= " $andor ";
                    $sql .= "(cid LIKE '$by_cat' AND state LIKE '$by_state')";
                }
                $sql .= ') ';
            }
        } elseif ('' != $by_state) {

            // because count() returns 1 even if a supplied variable
            // is not an array, we must check if $querryarray is really an array
            if (is_array($queryarray) && $count = count($queryarray)) {
                $sql .= " AND ((state LIKE '$by_state')";
                for ($i = 1; $i < $count; ++$i) {
                    $sql .= " $andor ";
                    $sql .= "(state LIKE '$by_state')";
                }
                $sql .= ') ';
            }
        } elseif ('' != $by_cat) {

            // because count() returns 1 even if a supplied variable
            // is not an array, we must check if $querryarray is really an array
            if (is_array($queryarray) && $count = count($queryarray)) {
                $sql .= " AND ((cid LIKE '$by_cat')";
                for ($i = 1; $i < $count; ++$i) {
                    $sql .= " $andor ";
                    $sql .= "(cid LIKE '$by_cat')";
                }
                $sql .= ') ';
            }
        } else {

            // because count() returns 1 even if a supplied variable
            // is not an array, we must check if $querryarray is really an array
            if (is_array($queryarray) && $count = count($queryarray)) {
                $sql .= " AND ((title LIKE '%$queryarray[0]%' OR town LIKE '%$queryarray[0]%' OR state LIKE '%$queryarray[0]%')";
                for ($i = 1; $i < $count; ++$i) {
                    $sql .= " $andor ";
                    $sql .= "(title LIKE '%$queryarray[i]%' OR town LIKE '%$queryarray[i]%' OR state LIKE '%$queryarray[i]%')";
                }
                $sql .= ') ';
            }
        }
        $sql    .= 'ORDER BY date DESC';
        $result = $xoopsDB->query($sql, $limit, $offset);
        $ret    = [];
        $i      = 0;
        while (false !== ($myrow = $xoopsDB->fetchArray($result))) {
            $statename = resume_getStateNameFromId($myrow['state']);

            $ret[$i]['image'] = 'assets/images/cat/default.gif';
            $ret[$i]['link']  = 'viewresume.php?lid=' . $myrow['lid'] . '';
            $ret[$i]['title'] = $myrow['title'];
            $ret[$i]['town']  = $myrow['town'];
            $ret[$i]['state'] = $statename;
            $ret[$i]['time']  = $myrow['date'];
            $ret[$i]['uid']   = $myrow['usid'];
            ++$i;
        }
    }

    return $ret;
}
