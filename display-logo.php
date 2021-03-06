<?php
//
// ------------------------------------------------------------------------- //
//               E-Xoops: Content Management for the Masses                  //
//                       < http://www.e-xoops.com >                          //
// ------------------------------------------------------------------------- //
// Original Author: Pascal Le Boustouller
// Author Website : pascal.e-xoops@perso-search.com
// Licence Type   : GPL
// ------------------------------------------------------------------------- //
include __DIR__ . '/header.php';
if (\Xmf\Request::hasVar('comp_id', 'GET')) {
 $comp_id = \Xmf\Request::getInt('comp_id', 0, 'GET');
} else {
    redirect_header('index.php', 3, _JOBS_VALIDATE_FAILED);
}
xoops_header();

global $xoopsUser, $xoopsConfig, $xoopsTheme, $xoopsDB, $xoops_footer, $xoopsLogger;
$currenttheme = $xoopsConfig['theme_set'];

$result      = $xoopsDB->query('SELECT comp_img FROM ' . $xoopsDB->prefix('jobs_companies') . ' WHERE comp_id = ' . $xoopsDB->escape($comp_id) . '');
$recordexist = $xoopsDB->getRowsNum($result);

if ($recordexist) {
    list($comp_img) = $xoopsDB->fetchRow($result);
    echo "<div class='center;'><img src=\"logo_images/$comp_img\" border=0></div>";
}

echo "<div class='center;'><table><tr><td><a href=#  onClick='window.close()'>" . _JOBS_CLOSEF . '</a></td></tr></table></div>';

xoops_footer();
