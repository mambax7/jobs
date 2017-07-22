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

// ------------------------------------------------------------------------- //
//   E-Xoops: Content Management for the Masses              //
//      < http://www.e-xoops.com >                       //
// ------------------------------------------------------------------------- //
// Original Author: Pascal Le Boustouller                                    //
// Author Website : pascal.e-xoops@perso-search.com                          //
// Licence Type   : GPL                                                      //
// ------------------------------------------------------------------------- //

//include("admin_header.php");
require_once __DIR__ . '/../../../include/cp_header.php';
$moduleDirName = basename(dirname(__DIR__));

require_once XOOPS_ROOT_PATH . "/modules/$moduleDirName/include/gtickets.php";
require_once XOOPS_ROOT_PATH . "/modules/$moduleDirName/include/functions.php";
require_once XOOPS_ROOT_PATH . "/modules/$moduleDirName/class/jobtree.php";
$myts = MyTextSanitizer::getInstance();
#  function NewCat
#####################################################
/**
 * @param $cat
 */
function NewCat($cat)
{
    global $xoopsDB, $xoopsConfig, $xoopsModule, $xoopsModuleConfig, $myts, $moduleDirName;

    $mytree = new JobTree($xoopsDB->prefix('jobs_categories'), 'cid', 'pid');

    require_once __DIR__ . '/admin_header.php';
    xoops_cp_header();
    //    loadModuleAdminMenu(1, "");

    echo "<fieldset><legend style='font-weight: bold; color: #900;'>" . _AM_JOBS_ADDSUBCAT . '</legend>';

    JobsShowImg();
    echo "<form method=\"post\" action=\"category.php\" name=\"imcat\"><input type=\"hidden\" name=\"op\" value=\"AddCat\">
        <table border=0><tr>
      <td width=\"12%\">" . _AM_JOBS_CATNAME . " </td><td><input type=\"text\" name=\"title\" size=\"30\" maxlength=\"100\">&nbsp; " . _AM_JOBS_IN . ' &nbsp;';

    $result = $xoopsDB->query('select pid, title, img, ordre from ' . $xoopsDB->prefix('jobs_categories') . " where cid=$cat");
    list($pid, $title, $imgs, $ordre) = $xoopsDB->fetchRow($result);
    $mytree->makeMyCatBox('title', 'title', $cat, 1);
    echo '</td>
    </tr>
    <tr>
      <td>' . _AM_JOBS_IMGCAT . "  </td><td><select name=\"img\" onChange=\"showimage()\">";

    $rep    = XOOPS_ROOT_PATH . "/modules/$moduleDirName/assets/images/cat";
    $handle = opendir($rep);
    while ($file = readdir($handle)) {
        $filelist[] = $file;
    }
    asort($filelist);
    //    while (list($key, $file) = each($filelist)) {
    foreach ($filelist as $key => $file) {
        if (!preg_match('/.gif|.jpg|.png/i', $file)) {
            if ($file === '.' || $file === '..') {
                $a = 1;
            }
        } else {
            if ($file === 'default.gif') {
                echo "<option value=$file selected>$file</option>";
            } else {
                echo "<option value=$file>$file</option>";
            }
        }
    }
    echo "</select>&nbsp;&nbsp;<img src=\"" . XOOPS_URL . "/modules/$moduleDirName/assets/images/cat/default.gif\" name=\"avatar\" align=\"absmiddle\"> </td></tr><tr><td>&nbsp;</td><td>" . _AM_JOBS_REPIMGCAT . " /modules/$moduleDirName/assets/images/cat/</td></tr>";

    echo '<tr><td>' . _AM_JOBS_DISPLPRICE2 . " </td><td><input type=\"radio\" name=\"affprice\" value=\"1\" checked>" . _AM_JOBS_OUI . "&nbsp;&nbsp; <input type=\"radio\" name=\"affprice\" value=\"0\">" . _AM_JOBS_NON . ' (' . _AM_JOBS_INTHISCAT . ')</td></tr>';

    if ($xoopsModuleConfig['jobs_cat_sortorder'] = 'ordre') {
        echo '<tr><td>' . _AM_JOBS_ORDRE . " </td><td><input type=\"text\" name=\"ordre\" size=\"4\"></td></tr><tr><td><br><input type=\"submit\" value=\"" . _AM_JOBS_ADD . "\"></td></tr>";
    } else {
        echo "<tr><td><br><input type=\"submit\" value=\"" . _AM_JOBS_ADD . "\"></td></tr>";
    }

    echo '</table>
        </form>';
    echo '<br>';
    echo '</fieldset><br>';
    xoops_cp_footer();
}

/**
 * @param $cat
 */
function ModCat($cat)
{
    global $xoopsDB, $xoopsConfig, $xoopsModule, $xoopsModuleConfig, $myts, $moduleDirName;

    $mytree = new JobTree($xoopsDB->prefix('jobs_categories'), 'cid', 'pid');

    require_once __DIR__ . '/admin_header.php';
    xoops_cp_header();
    //loadModuleAdminMenu(1, "");

    echo "<fieldset><legend style='font-weight: bold; color: #900;'>" . _AM_JOBS_MODIFCAT . '</legend>';

    JobsShowImg();
    $result = $xoopsDB->query('select pid, title, img, ordre, affprice from ' . $xoopsDB->prefix('jobs_categories') . " where cid=$cat");
    list($pid, $title, $imgs, $ordre, $affprice) = $xoopsDB->fetchRow($result);

    $title = $myts->undoHtmlSpecialChars($title);

    echo "<form action=\"category.php\" method=\"post\" name=\"imcat\">";
    echo $GLOBALS['xoopsSecurity']->getTokenHTML();
    echo "<table border=\"0\"><tr>
    <td>" . _AM_JOBS_CATNAME . "   </td><td><input type=\"text\" name=\"title\" value=\"$title\" size=\"30\" maxlength=\"50\">&nbsp; " . _AM_JOBS_IN . ' &nbsp;';
    $mytree->makeMyCatBox('title', 'title', $pid, 1);
    echo '</td></tr><tr>
    <td>' . _AM_JOBS_IMGCAT . "  </td><td><select name=\"img\" onChange=\"showimage()\">";

    $rep    = XOOPS_ROOT_PATH . "/modules/$moduleDirName/assets/images/cat";
    $handle = opendir($rep);
    while ($file = readdir($handle)) {
        $filelist[] = $file;
    }
    asort($filelist);
    //    while (list($key, $file) = each($filelist)) {
    foreach ($filelist as $key => $file) {
        if (!preg_match('/.gif|.jpg|.png/i', $file)) {
            if ($file === '.' || $file === '..') {
                $a = 1;
            }
        } else {
            if ($file === $imgs) {
                echo "<option value=$file selected>$file</option>";
            } else {
                echo "<option value=$file>$file</option>";
            }
        }
    }
    echo "</select>&nbsp;&nbsp;<img src=\"" . XOOPS_URL . "/modules/$moduleDirName/assets/images/cat/$imgs\" name=\"avatar\" align=\"absmiddle\"> </td></tr><tr><td>&nbsp;</td><td>" . _AM_JOBS_REPIMGCAT . " /modules/$moduleDirName/assets/images/cat/</td></tr>";

    echo '<tr><td>' . _AM_JOBS_DISPLPRICE2 . " </td><td><input type=\"radio\" name=\"affprice\" value=\"1\"";
    if ($affprice == '1') {
        echo 'checked';
    }
    echo '>' . _AM_JOBS_OUI . "&nbsp;&nbsp; <input type=\"radio\" name=\"affprice\" value=\"0\"";
    if ($affprice == '0') {
        echo 'checked';
    }
    echo '>' . _AM_JOBS_NON . ' (' . _AM_JOBS_INTHISCAT . ')</td></tr>';

    if ($xoopsModuleConfig['jobs_cat_sortorder'] = 'ordre') {
        echo '<tr><td>' . _AM_JOBS_ORDRE . " </td><td><input type=\"text\" name=\"ordre\" size=\"4\" value=\"$ordre\"></td></tr>";
    } else {
        echo "<input type=\"hidden\" name=\"ordre\" value=\"$ordre\">";
    }

    echo '</table>';
    echo "<input type=\"hidden\" name=\"cidd\" value=\"$cat\">"
         . "<input type=\"hidden\" name=\"op\" value=\"ModCatS\">"
         . "<table border=\"0\"><tr><td><br>"
         . "<input type=\"submit\" value=\""
         . _AM_JOBS_SAVMOD
         . "\"></form></td></tr><tr><td><br>"
         . "<form action=\"category.php\" method=\"post\">"
         . "<input type=\"hidden\" name=\"cid\" value=\"$cat\">"
         . "<input type=\"hidden\" name=\"op\" value=\"DelCat\">"
         . "<input type=\"submit\" value=\""
         . _AM_JOBS_DEL
         . "\"></form></td></tr></table>";

    echo '</fieldset><br>';
    xoops_cp_footer();
}

/**
 * @param $cidd
 * @param $cid
 * @param $img
 * @param $title
 * @param $ordre
 * @param $affprice
 */
function ModCatS($cidd, $cid, $img, $title, $ordre, $affprice)
{
    global $xoopsDB, $xoopsConfig, $myts, $moduleDirName;

    $title = $myts->htmlSpecialChars($title);
    $xoopsDB->query('update ' . $xoopsDB->prefix('jobs_categories') . " set title='$title', pid='$cid', img='$img', ordre='$ordre', affprice='$affprice' where cid=$cidd");
    redirect_header('map.php', 3, _AM_JOBS_CATSMOD);
}

/**
 * @param $title
 * @param $cid
 * @param $img
 * @param $ordre
 * @param $affprice
 */
function AddCat($title, $cid, $img, $ordre, $affprice)
{
    global $xoopsDB, $xoopsConfig, $myts, $moduleDirName;

    $title = $myts->htmlSpecialChars($title);
    if ($title == '') {
        $title = '! ! ? ! !';
    }
    $xoopsDB->query('insert into ' . $xoopsDB->prefix('jobs_categories') . " values (null, '$cid', '$title', '$img', '$ordre', '$affprice')");

    redirect_header('map.php', 3, _AM_JOBS_CATADD);
}

/**
 * @param     $cid
 * @param int $ok
 */
function DelCat($cid, $ok = 0)
{
    global $xoopsDB, $xoopsConfig, $xoopsModule, $moduleDirName;

    if ((int)$ok == 1) {
        $xoopsDB = XoopsDatabaseFactory::getDatabaseConnection();
        $xoopsDB->queryF('delete from ' . $xoopsDB->prefix('jobs_categories') . " where cid=$cid or pid=$cid");
        $xoopsDB->queryF('delete from ' . $xoopsDB->prefix('jobs_listing') . " where cid=$cid");
        redirect_header('map.php', 3, _AM_JOBS_CATDEL);
    } else {
        xoops_cp_header();
        echo "<table border=\"0\"><tr><td>";
        echo '<br><center><b>' . _AM_JOBS_SURDELCAT . '</b><br><br>';
        echo "[ <a href=\"category.php?op=DelCat&cid=$cid&ok=1\">" . _AM_JOBS_OUI . "</a> | <a href=\"map.php\">" . _AM_JOBS_NON . '</a> ]<br><br>';
        echo '</td></tr></table>';
        xoops_cp_footer();
    }
}

#  function NewResCat
#####################################################
/**
 * @param $cat
 */
function NewResCat($cat)
{
    global $xoopsDB, $xoopsConfig, $xoopsModule, $xoopsModuleConfig, $myts, $moduleDirName;

    $mytree = new JobTree($xoopsDB->prefix('jobs_res_categories'), 'cid', 'pid');

    require_once __DIR__ . '/admin_header.php';
    xoops_cp_header();
    //loadModuleAdminMenu(1, "");
    echo "<fieldset><legend style='font-weight: bold; color: #900;'>" . _AM_JOBS_RES_ADDSUBCAT . '</legend>';

    JobsShowImg();
    echo "<form method=\"post\" action=\"category.php\" name=\"imcat\"><input type=\"hidden\" name=\"op\" value=\"AddResCat\">
        <table border=0><tr>
      <td width=\"12%\">" . _AM_JOBS_CATNAME . " </td><td><input type=\"text\" name=\"title\" size=\"30\" maxlength=\"100\">&nbsp; " . _AM_JOBS_IN . ' &nbsp;';

    $result = $xoopsDB->query('select pid, title, img, ordre from ' . $xoopsDB->prefix('jobs_res_categories') . " where cid=$cat");
    list($pid, $title, $imgs, $ordre) = $xoopsDB->fetchRow($result);
    $mytree->makeMyCatBox('title', 'title', $cat, 1);
    echo '</td>
    </tr>
    <tr>
      <td>' . _AM_JOBS_IMGCAT . "  </td><td><select name=\"img\" onChange=\"showimage()\">";

    $rep    = XOOPS_ROOT_PATH . "/modules/$moduleDirName/assets/images/cat";
    $handle = opendir($rep);
    while ($file = readdir($handle)) {
        $filelist[] = $file;
    }
    asort($filelist);
    //    while (list($key, $file) = each($filelist)) {
    foreach ($filelist as $key => $file) {
        if (!preg_match('/.gif|.jpg|.png/i', $file)) {
            if ($file === '.' || $file === '..') {
                $a = 1;
            }
        } else {
            if ($file === 'default.gif') {
                echo "<option value=$file selected>$file</option>";
            } else {
                echo "<option value=$file>$file</option>";
            }
        }
    }
    echo "</select>&nbsp;&nbsp;<img src=\"" . XOOPS_URL . "/modules/$moduleDirName/assets/images/cat/default.gif\" name=\"avatar\" align=\"absmiddle\"> </td></tr><tr><td>&nbsp;</td><td>" . _AM_JOBS_REPIMGCAT . " /modules/$moduleDirName/assets/images/cat/</td></tr>";

    echo '<tr><td>' . _AM_JOBS_DISPLPRICE2 . " </td><td><input type=\"radio\" name=\"affprice\" value=\"1\" checked>" . _AM_JOBS_OUI . "&nbsp;&nbsp; <input type=\"radio\" name=\"affprice\" value=\"0\">" . _AM_JOBS_NON . ' (' . _AM_JOBS_INTHISCAT . ')</td></tr>';

    if ($xoopsModuleConfig['jobs_cat_sortorder'] = 'ordre') {
        echo '<tr><td>' . _AM_JOBS_ORDRE . " </td><td><input type=\"text\" name=\"ordre\" size=\"4\"></td></tr><tr><td><br><input type=\"submit\" value=\"" . _AM_JOBS_ADD . "\"></td></tr>";
    } else {
        echo "<tr><td><br><input type=\"submit\" value=\"" . _AM_JOBS_ADD . "\"></td></tr>";
    }
    echo '</table>
        </form>';
    echo '<br>';
    echo '</fieldset><br>';
    xoops_cp_footer();
}

#  function ModResCat
#####################################################
/**
 * @param $cat
 */
function ModResCat($cat)
{
    global $xoopsDB, $xoopsConfig, $xoopsModule, $xoopsModuleConfig, $myts, $moduleDirName;

    $mytree = new JobTree($xoopsDB->prefix('jobs_res_categories'), 'cid', 'pid');

    require_once __DIR__ . '/admin_header.php';
    xoops_cp_header();
    //loadModuleAdminMenu(1, "");
    echo "<fieldset><legend style='font-weight: bold; color: #900;'>" . _AM_JOBS_MODIFCAT . '</legend>';

    JobsShowImg();
    $result = $xoopsDB->query('select pid, title, img, ordre, affprice from ' . $xoopsDB->prefix('jobs_res_categories') . " where cid=$cat");
    list($pid, $title, $imgs, $ordre, $affprice) = $xoopsDB->fetchRow($result);

    $title = $myts->htmlSpecialChars($title);
    echo "<table border=\"0\"><tr>";
    echo "<form action=\"category.php\" method=\"post\" name=\"imcat\">";
    echo $GLOBALS['xoopsSecurity']->getTokenHTML();

    echo "<td>" . _AM_JOBS_CATNAME . "   </td><td><input type=\"text\" name=\"title\" value=\"$title\" size=\"30\" maxlength=\"50\">&nbsp; " . _AM_JOBS_IN . ' &nbsp;';
    $mytree->makeMyCatBox('title', 'title', $pid, 1);
    echo '</td></tr>
    <tr>
    <td>' . _AM_JOBS_IMGCAT . "  </td><td><select name=\"img\" onChange=\"showimage()\">";

    $rep    = XOOPS_ROOT_PATH . "/modules/$moduleDirName/assets/images/cat";
    $handle = opendir($rep);
    while ($file = readdir($handle)) {
        $filelist[] = $file;
    }
    asort($filelist);
    //    while (list($key, $file) = each($filelist)) {
    foreach ($filelist as $key => $file) {
        if (!preg_match('/.gif|.jpg|.png/i', $file)) {
            if ($file === '.' || $file === '..') {
                $a = 1;
            }
        } else {
            if ($file == $imgs) {
                echo "<option value=$file selected>$file</option>";
            } else {
                echo "<option value=$file>$file</option>";
            }
        }
    }
    echo "</select>&nbsp;&nbsp;<img src=\"" . XOOPS_URL . "/modules/$moduleDirName/assets/images/cat/$imgs\" name=\"avatar\" align=\"absmiddle\"> </td></tr><tr><td>&nbsp;</td><td>" . _AM_JOBS_REPIMGCAT . " /modules/$moduleDirName/assets/images/cat/</td></tr>";

    echo '<tr><td>' . _AM_JOBS_DISPLPRICE2 . " </td><td colspan=2>
    <input type=\"radio\"name=\"affprice\"value=\"1\"";
    if ($affprice == '1') {
        echo 'checked';
    }
    echo '>' . _AM_JOBS_OUI . "&nbsp;&nbsp; <input type=\"radio\" name=\"affprice\" value=\"0\"";
    if ($affprice == '0') {
        echo 'checked';
    }
    echo '>' . _AM_JOBS_NON . ' (' . _AM_JOBS_INTHISCAT . ')</td></tr>';

    if ($xoopsModuleConfig['jobs_cat_sortorder'] = 'ordre') {
        echo '<tr><td>' . _AM_JOBS_ORDRE . " </td><td><input type=\"text\" name=\"ordre\" size=\"4\" value=\"$ordre\"></td></tr>";
    } else {
        echo "<input type=\"hidden\" name=\"ordre\" value=\"$ordre\">";
    }

    echo '</table>';
    echo "<input type=\"hidden\" name=\"cidd\" value=\"$cat\">"
         . "<input type=\"hidden\" name=\"op\" value=\"ModResCatS\">"
         . "<table border=\"0\"><tr><td>"
         . "<input type=\"submit\" value=\""
         . _AM_JOBS_SAVMOD
         . "\"></form></td><td>"
         . "<form action=\"category.php\" method=\"post\">"
         . "<input type=\"hidden\" name=\"cid\" value=\"$cat\">"
         . "<input type=\"hidden\" name=\"op\" value=\"DelResCat\">"
         . "<input type=\"submit\" value=\""
         . _AM_JOBS_DEL
         . "\"></form></td></tr></table>";

    echo '</fieldset><br>';
    xoops_cp_footer();
}

#  function ModResCatS
#####################################################
/**
 * @param $cidd
 * @param $cid
 * @param $img
 * @param $title
 * @param $ordre
 * @param $affprice
 */
function ModResCatS($cidd, $cid, $img, $title, $ordre, $affprice)
{
    global $xoopsDB, $xoopsConfig, $myts, $moduleDirName;

    $title = $myts->htmlSpecialChars($title);
    $xoopsDB->query('update ' . $xoopsDB->prefix('jobs_res_categories') . " set title='$title', pid='$cid', img='$img', ordre='$ordre', affprice='$affprice' where cid=$cidd");
    redirect_header('map.php', 3, _AM_JOBS_CATSMOD);
}

#  function AddResCat
#####################################################
/**
 * @param $title
 * @param $cid
 * @param $img
 * @param $ordre
 * @param $affprice
 */
function AddResCat($title, $cid, $img, $ordre, $affprice)
{
    global $xoopsDB, $xoopsConfig, $myts, $moduleDirName, $mydirnumber;

    $title = $myts->htmlSpecialChars($title);
    if ($title == '') {
        $title = '! ! ? ! !';
    }
    $xoopsDB->query('insert into ' . $xoopsDB->prefix('jobs_res_categories') . " values (null, '$cid', '$title', '$img', '$ordre', '$affprice')");

    redirect_header('map.php', 3, _AM_JOBS_CATADD);
}

#  function DelResCat
#####################################################
/**
 * @param     $cid
 * @param int $ok
 */
function DelResCat($cid, $ok = 0)
{
    global $xoopsDB, $xoopsConfig, $xoopsModule, $moduleDirName;

    if ((int)$ok == 1) {
        $xoopsDB = XoopsDatabaseFactory::getDatabaseConnection();
        $xoopsDB->queryF('delete from ' . $xoopsDB->prefix('jobs_res_categories') . " where cid=$cid or pid=$cid");
        $xoopsDB->queryF('delete from ' . $xoopsDB->prefix('jobs_resume') . " where cid=$cid");

        redirect_header('map.php', 3, _AM_JOBS_CATDEL);
    } else {
        xoops_cp_header();
        OpenTable();
        echo '<br><center><b>' . _AM_JOBS_SURDELCAT . '</b><br><br>';
        echo "[ <a href=\"category.php?op=DelResCat&cid=$cid&ok=1\">" . _AM_JOBS_OUI . "</a> | <a href=\"map.php\">" . _AM_JOBS_NON . '</a> ]<br><br>';
        CloseTable();
        xoops_cp_footer();
    }
}

#####################################################
foreach ($_POST as $k => $v) {
    ${$k} = $v;
}

$ok = isset($_GET['ok']) ? $_GET['ok'] : '';

if (!isset($_POST['cid']) && isset($_GET['cid'])) {
    $cid = $_GET['cid'];
}
if (!isset($_POST['op']) && isset($_GET['op'])) {
    $op = $_GET['op'];
}

switch ($op) {
    case 'NewCat':
        NewCat($cid);
        break;

    case 'AddCat':
        AddCat($title, $cid, $img, $ordre, $affprice);
        break;

    case 'DelCat':
        DelCat($cid, $ok);
        break;

    case 'ModCat':
        ModCat($cid);
        break;

    case 'ModCatS':
        ModCatS($cidd, $cid, $img, $title, $ordre, $affprice);
        break;
    case 'NewResCat':
        NewResCat($cid);
        break;

    case 'AddResCat':
        AddResCat($title, $cid, $img, $ordre, $affprice);
        break;

    case 'DelResCat':
        DelResCat($cid, $ok);
        break;

    case 'ModResCat':
        ModResCat($cid);
        break;

    case 'ModResCatS':
        ModResCatS($cidd, $cid, $img, $title, $ordre, $affprice);
        break;

    default:
        Index();
        break;
}
