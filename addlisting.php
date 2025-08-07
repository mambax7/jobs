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

use XoopsModules\Jobs;

include __DIR__ . '/header.php';
$moduleDirName = basename(__DIR__);
$myts          = \MyTextSanitizer::getInstance(); // MyTextSanitizer object
require_once XOOPS_ROOT_PATH . "/modules/$moduleDirName/include/functions.php";
/** @var Jobs\Helper $helper */
$helper = Jobs\Helper::getInstance();

$module_id = $xoopsModule->getVar('mid');
if (is_object($xoopsUser)) {
    $groups =& $xoopsUser->getGroups();
} else {
    $groups = XOOPS_GROUP_ANONYMOUS;
}

/** @var \XoopsGroupPermHandler $grouppermHandler */
$grouppermHandler = xoops_getHandler('groupperm');
$perm_itemid = \Xmf\Request::getInt('item_id', 0, 'POST');
if (!$grouppermHandler->checkRight('jobs_submit', $perm_itemid, $groups, $module_id)) {
    redirect_header(XOOPS_URL . '/index.php', 3, _NOPERM);
}
if (!$grouppermHandler->checkRight('jobs_premium', $perm_itemid, $groups, $module_id)) {
    $premium = 0;
} else {
    $premium = 1;
}

$mytree = new Jobs\JobTree($xoopsDB->prefix('jobs_categories'), 'cid', 'pid');

if (\Xmf\Request::hasVar('cid', 'POST')) {
    $cid = \Xmf\Request::getInt('cid', 0, 'POST');
} else {
    if (\Xmf\Request::hasVar('cid', 'GET')) {
        $cid = \Xmf\Request::getInt('cid', 0, 'GET');
    }
}

if (\Xmf\Request::hasVar('comp_id', 'POST')) {
    $comp_id = \Xmf\Request::getInt('comp_id', 0, 'POST');
} else {
    if (\Xmf\Request::hasVar('comp_id', 'GET')) {
        $comp_id = \Xmf\Request::getInt('comp_id', 0, 'GET');
    }
}

if (empty($xoopsUser)) {
    redirect_header(XOOPS_URL . 'modules/profile/', 3, _JOBS_MUSTREGFIRST);
}

$member_usid = $xoopsUser->uid();
if ('1' == $helper->getConfig('jobs_show_company')) {
    $all_comp = jobs_getCompany($member_usid);
    if (!$all_comp) {
        redirect_header(XOOPS_URL . "/modules/$moduleDirName/addcompany.php", 2, _JOBS_MUSTADD_COMPANY);
    }

    if (empty($comp_id)) {
        $count = jobs_getCompCount($member_usid);
        if ($count > 1) {
            redirect_header(XOOPS_URL . "/modules/$moduleDirName/whatcompany.php", 1, _JOBS_WHAT_COMPANY);
        }
    }
}
if (!empty($_POST['submit'])) {
    $jobsdays = $helper->getConfig('jobs_days');

    if (!$GLOBALS['xoopsSecurity']->check(true, $_REQUEST['token'])) {
        redirect_header(XOOPS_URL . '/', 3, $GLOBALS['xoopsSecurity']->getErrors());
    }

    //  if ($xoopsModuleConfig["jobs_use_captcha"] == '1') {
    //  $x24plus = jobs_isX24plus();
    //  if ($x24plus) {
    //  xoops_load("xoopscaptcha");
    //  $xoopsCaptcha = XoopsCaptcha::getInstance();
    //  if ( !$xoopsCaptcha->verify() ) {
    //       redirect_header( XOOPS_URL . "/modules/" . $xoopsModule->getVar('dirname') . "/index.php", 3, $xoopsCaptcha->getMessage() );
    //  }
    //  } else {
    //  xoops_load("captcha");
    //  $xoopsCaptcha = XoopsCaptcha::getInstance();
    //  if ( !$xoopsCaptcha->verify() ) {
    //        redirect_header( XOOPS_URL . "/modules/" . $xoopsModule->getVar('dirname') . "/index.php", 3, $xoopsCaptcha->getMessage() );
    //  }
    //  }
    //  }

    $title   = $myts->addSlashes($_POST['title']);
    $status  = $myts->addSlashes($_POST['status']);
    $expire  = $myts->addSlashes($_POST['expire']);
    $type    = $myts->addSlashes($_POST['type']);
    $company = $myts->addSlashes($_POST['company']);
    if ('dhtmltextarea' === $helper->getConfig('jobs_form_options')
        || 'dhtml' === $helper->getConfig('jobs_form_options')) {
        $desctext = $myts->displayTarea($_POST['desctext'], 0, 0, 0, 0, 0);
    } else {
        $desctext = $myts->displayTarea($_POST['desctext'], 1, 1, 1, 1, 1);
    }
    if ('dhtmltextarea' === $helper->getConfig('jobs_form_options')
        || 'dhtml' === $helper->getConfig('jobs_form_options')) {
        $requirements = $myts->displayTarea($_POST['requirements'], 0, 0, 1, 0, 0);
    } else {
        $requirements = $myts->displayTarea($_POST['requirements'], 1, 1, 1, 1, 1);
    }

    $tel          = $myts->addSlashes($_POST['tel']);
    $price        = $myts->addSlashes($_POST['price']);
    $typeprice    = $myts->addSlashes($_POST['typeprice']);
    $contactinfo  = $myts->displayTarea($_POST['contactinfo'], 0, 0, 0, 0, 0);
    $contactinfo1 = $myts->displayTarea($_POST['contactinfo1'], 0, 0, 0, 0, 0);
    $contactinfo2 = $myts->displayTarea($_POST['contactinfo2'], 0, 0, 0, 0, 0);
    $submitter    = $myts->addSlashes($_POST['submitter']);
    $usid         = $myts->addSlashes($member_usid);
    $town         = $myts->addSlashes($_POST['town']);
    $state        = $myts->addSlashes($_POST['state']);
    $valid        = $myts->addSlashes($_POST['valid']);
    $email        = $myts->addSlashes($_POST['email']);
    $view         = 0;
    $photo        = '';
    $date         = time();

    $newid = $xoopsDB->genId($xoopsDB->prefix('jobs_listing') . '_lid_seq');

    $listingHandler = $helper->getHandler('Listing');
    $listing = $listingHandler->create();
    $listing->setVar('cid', $cid);
    $listing->setVar('title', $title);
    $listing->setVar('status', $status);
    $listing->setVar('expire', $expire);
    $listing->setVar('type', $type);
    $listing->setVar('company', $company);
    $listing->setVar('desctext', $desctext);
    $listing->setVar('requirements', ''.$requirements);
    $listing->setVar('tel', $tel);
    $listing->setVar('price', $price);
    $listing->setVar('typeprice', $typeprice);
    $listing->setVar('contactinfo', $contactinfo);
    $listing->setVar('contactinfo1', $contactinfo1);
    $listing->setVar('contactinfo2', $contactinfo2);
    $listing->setVar('date', $date);
    $listing->setVar('email', $email);
    $listing->setVar('submitter', $submitter);
    $listing->setVar('usid', $usid);
    $listing->setVar('town', $town);
    $listing->setVar('state', $state);
    $listing->setVar('valid', $valid);
    $listing->setVar('photo', $photo);
    $listing->setVar('view', $view);

    if (!$listingHandler->insert($listing)) {
        redirect_header('index.php', 3, _JOBS_JOB_NOT_ADDED);
    }

    if ('1' == $valid) {
        $notificationHandler      = xoops_getHandler('notification');
        $lid                      = $listing->getVar('lid');
        $tags                     = [];
        $tags['LID']              = $lid;
        $tags['TITLE']            = $title;
        $tags['TYPE']             = $type;
        $tags['DESCTEXT']         = $desctext;
        $tags['COMPANY_TITLE']    = stripslashes($company);
        $tags['HELLO']            = _JOBS_HELLO;
        $tags['ADDED_TO_CAT']     = _JOBS_ADDED_TO_CAT;
        $tags['ADDED_BY_COMPANY'] = _JOBS_ADDED_BY_COMPANY;
        $tags['FOLLOW_LINK']      = _JOBS_FOLLOW_LINK;
        $tags['RECIEVING_NOTIF']  = _JOBS_RECIEVING_NOTIF;
        $tags['ERROR_NOTIF']      = _JOBS_ERROR_NOTIF;
        $tags['WEBMASTER']        = _JOBS_WEBMASTER;
        $tags['LINK_URL']         = XOOPS_URL . '/modules/' . $moduleDirName . '/viewjobs.php' . '?lid=' . addslashes($lid);
        $sql                      = 'SELECT title FROM ' . $xoopsDB->prefix('jobs_categories') . ' WHERE cid=' . addslashes($cid);
        $result                   = $xoopsDB->query($sql);
        $row                      = $xoopsDB->fetchArray($result);
        $tags['CATEGORY_TITLE']   = $row['title'];
        $tags['CATEGORY_URL']     = XOOPS_URL . '/modules/' . $moduleDirName . '/jobscat.php?cid="' . addslashes($cid);
        $notificationHandler      = xoops_getHandler('notification');
        $notificationHandler->triggerEvent('global', 0, 'new_job', $tags);
        $notificationHandler->triggerEvent('category', $cid, 'new_jobs_cat', $tags);
        $notificationHandler->triggerEvent('company_listing', $comp_id, 'new_jobs_comp', $tags);
        $notificationHandler->triggerEvent('job_listing', $lid, 'new_job', $tags);
    }
    redirect_header('index.php', 3, _JOBS_JOBADDED);
} else {
    $GLOBALS['xoopsOption']['template_main'] = 'jobs_addlisting.tpl';
    include XOOPS_ROOT_PATH . '/header.php';

    require_once XOOPS_ROOT_PATH . '/class/xoopsformloader.php';

    $cid    = \Xmf\Request::getInt('cid', 0);

    $member_usid  = $xoopsUser->uid();
    $member_email = $xoopsUser->getVar('email', 'E');
    $member_uname = $xoopsUser->getVar('uname', 'E');
    $email        = $member_email;
    $temp_premium = '0';

    if (empty($comp_id)) {
        $thiscompany = jobs_getCompany($member_usid);
    } else {
        $extra_user = jobs_getXtraUsers($comp_id, $member_usid);
        if (!empty($extra_user)) {
            $temp_premium = '1';
        }

        $thiscompany = jobs_getThisCompany($comp_id, $member_usid);
    }

    $result  = $xoopsDB->query('SELECT nom_type FROM ' . $xoopsDB->prefix('jobs_type') . ' ORDER BY nom_type');
    $result1 = $xoopsDB->query('SELECT nom_price FROM ' . $xoopsDB->prefix('jobs_price') . ' ORDER BY id_price');
    $result2 = $xoopsDB->query('SELECT rid,name FROM ' . $xoopsDB->prefix('jobs_region') . ' ORDER BY rid');

    ob_start();
    $form = new \XoopsThemeForm(_JOBS_ADD_LISTING, 'submit_form', 'addlisting.php');
    $form->setExtra('enctype="multipart/form-data"');
    //    $GLOBALS['xoopsGTicket']->addTicketXoopsFormElement($form, __LINE__, 1800, 'token');

    if (('1' == $premium) || ('1' == $temp_premium)) {
        echo '' . _JOBS_PREMIUM_MEMBER . ' ' . $helper->getConfig('jobs_days') . ' ' . _JOBS_PREMIUM2 . '';
    } else {
        echo '';
    }

    $form->addElement(new \XoopsFormLabel(_JOBS_SUBMITTER, $member_uname));
    $form->addElement(new \XoopsFormHidden('submitter', $member_uname));

    if ('1' == $helper->getConfig('jobs_show_company')) {
        $form->addElement(new \XoopsFormLabel(_JOBS_COMPANY, $thiscompany['comp_name']));
        $form->addElement(new \XoopsFormHidden('company', $thiscompany['comp_name']));
    } else {
        $form->addElement(new \XoopsFormHidden('company', ''));
    }

    $form->addElement(new \XoopsFormText(_JOBS_EMAIL, 'email', 50, 100, $email), true);
    if ('1' == $helper->getConfig('jobs_show_company')) {
        $form->addElement(new \XoopsFormText(_JOBS_TOWN, 'town', 50, 50, $thiscompany['comp_city']), false);
    } else {
        $form->addElement(new \XoopsFormText(_JOBS_TOWN, 'town', 50, 50, ''), false);
    }
    if ('1' == $helper->getConfig('jobs_show_state')) {
        $state_form = new \XoopsFormSelect(_JOBS_STATE, 'state', $thiscompany['comp_state'], '0', false);
        while (false !== (list($rid, $name) = $xoopsDB->fetchRow($result2))) {
            $state_form->addOption('', _JOBS_SELECT_STATE);
            $state_form->addOption($rid, $name);
        }
        $form->addElement($state_form, true);
    } else {
        $form->addElement(new \XoopsFormHidden('state', ''));
    }

    if ('1' == $helper->getConfig('jobs_show_company')) {
        $form->addElement(new \XoopsFormText(_JOBS_TEL, 'tel', 30, 30, $thiscompany['comp_phone']), false);
    } else {
        $form->addElement(new \XoopsFormText(_JOBS_TEL, 'tel', 30, 30, ''), false);
    }
    $cat_form = new \XoopsFormSelect(_JOBS_CAT, 'cid', '');
    $cattree  = $mytree->getChildTreeArray(0, 'title ASC');
    $cat_form->addOption('', _JOBS_SELECTCAT);
    foreach ($cattree as $branch) {
        $branch['prefix'] = substr($branch['prefix'], 0, -1);
        $branch['prefix'] = str_replace('.', '--', $branch['prefix']);
        $cat_form->addOption($branch['cid'], $branch['prefix'] . $branch['title']);
    }
    $form->addElement($cat_form, true);

    if (('1' == $premium) || ('1' == $temp_premium)) {
        $form->addElement(new \XoopsFormText(_JOBS_HOW_LONG, 'expire', 3, 3, $helper->getConfig('jobs_days')), true);
    } else {
        $form->addElement(new \XoopsFormLabel(_JOBS_NON_HOW_LONG, $helper->getConfig('jobs_days')));
        $form->addElement(new \XoopsFormHidden('expire', $helper->getConfig('jobs_days')));
    }

    $type_form = new \XoopsFormSelect(_JOBS_JOB_TYPE, 'type', '', '0', false);
    while (false !== (list($nom_type) = $xoopsDB->fetchRow($result))) {
        $type_form->addOption($nom_type, $nom_type);
    }
    $form->addElement($type_form);

    if (('1' == $premium) || ('1' == $temp_premium)) {
        $radio        = new \XoopsFormRadio(_JOBS_STATUS, 'status', '1');
        $options['1'] = _JOBS_ACTIVE;
        $options['0'] = _JOBS_INACTIVE;
        $radio->addOptionArray($options);
        $form->addElement($radio, true);
    } else {
        $form->addElement(new \XoopsFormHidden('status', '1'));
    }
    $form->addElement(new \XoopsFormText(_JOBS_TITLE, 'title', 40, 50, ''), true);
    $form->addElement(jobs_getEditor(_JOBS_DESC, 'desctext', '', '100%', '300px', ''), true);
    $form->addElement(jobs_getEditor(_JOBS_REQUIRE, 'requirements', '', '100%', '300px', ''), true);
    $form->addElement(new \XoopsFormText(_JOBS_PRICE2, 'price', 40, 50, ''), false);
    $sel_form = new \XoopsFormSelect(_JOBS_SALARYTYPE, 'typeprice', '', '1', false);
    while (false !== (list($nom_price) = $xoopsDB->fetchRow($result1))) {
        $sel_form->addOption($nom_price, $nom_price);
    }
    $form->addElement($sel_form);
    if ('1' == $helper->getConfig('jobs_show_company')) {
        $form->addElement(new \XoopsFormTextArea(_JOBS_CONTACTINFO, 'contactinfo', '' . $myts->undoHtmlSpecialChars($myts->displayTarea($thiscompany['comp_contact'], 0, 0, 0, 0, 0)) . '', 6, 40), true);
    } else {
        $form->addElement(new \XoopsFormTextArea(_JOBS_CONTACTINFO, 'contactinfo', '', 6, 40), true);
    }

    if (('1' == $premium) || ('1' == $temp_premium)) {
        if (('1' == $helper->getConfig('jobs_show_company')) && $thiscompany['comp_user1_contact']) {
            $form->addElement(new \XoopsFormTextArea(_JOBS_CONTACTINFO1, 'contactinfo1', '' . $thiscompany['comp_user1_contact'] . '', 6, 40), false);
        } else {
            $form->addElement(new \XoopsFormTextArea(_JOBS_CONTACTINFO1, 'contactinfo1', '', 6, 40), false);
        }
        if (('1' == $helper->getConfig('jobs_show_company')) && $thiscompany['comp_user2_contact']) {
            $form->addElement(new \XoopsFormTextArea(_JOBS_CONTACTINFO2, 'contactinfo2', '' . $thiscompany['comp_user2_contact'] . '', 6, 40), false);
        } else {
            $form->addElement(new \XoopsFormTextArea(_JOBS_CONTACTINFO2, 'contactinfo2', '', 6, 40), false);
        }
    } else {
        $form->addElement(new \XoopsFormHidden('contactinfo1', ''));
        $form->addElement(new \XoopsFormHidden('contactinfo2', ''));
    }
    //  if ($helper->getConfig('jobs_use_captcha') == '1') {
    //        $form->addElement(new \XoopsFormCaptcha(_JOBS_CAPTCHA, "xoopscaptcha", false), true);
    //  }
    if (0 == $helper->getConfig('jobs_moderated')) {
        $form->addElement(new \XoopsFormHidden('valid', '1'), false);
    } else {
        $form->addElement(new \XoopsFormHidden('valid', '0'), false);
    }
    if ('1' == $helper->getConfig('jobs_show_company')) {
        $form->addElement(new \XoopsFormHidden('comp_id', $thiscompany['comp_id']), false);
    }
    $form->addElement(new \XoopsFormButton('', 'submit', _JOBS_SUBMIT, 'submit'));
    $form->display();
    $xoopsTpl->assign('submit_form', ob_get_contents());
    ob_end_clean();
    $xoopsTpl->assign('lang_comp_city', _JOBS_COMPANY_CITY);
}
include XOOPS_ROOT_PATH . '/footer.php';
