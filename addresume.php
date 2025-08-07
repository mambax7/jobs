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
//require_once XOOPS_ROOT_PATH . "/modules/$moduleDirName/include/gtickets.php";
require_once XOOPS_ROOT_PATH . "/modules/$moduleDirName/include/resume_functions.php";
/** @var Jobs\Helper $helper */
$helper = Jobs\Helper::getInstance();
$myts = \MyTextSanitizer::getInstance();

$module_id = $xoopsModule->getVar('mid');
if (is_object($xoopsUser)) {
    $groups =& $xoopsUser->getGroups();
} else {
    $groups = XOOPS_GROUP_ANONYMOUS;
}

/** @var \XoopsGroupPermHandler $grouppermHandler */
$grouppermHandler = xoops_getHandler('groupperm');
$perm_itemid = \Xmf\Request::getInt('item_id', 0, 'POST');
//If no access
if (!$grouppermHandler->checkRight('resume_submit', $perm_itemid, $groups, $module_id)) {
    redirect_header(XOOPS_URL . "/modules/$moduleDirName/resumes.php", 3, _NOPERM);
}

if (\Xmf\Request::hasVar('cid', 'POST')) {
    $cid = \Xmf\Request::getInt('cid', 0, 'POST');
} else {
    if (\Xmf\Request::hasVar('cid', 'GET')) {
        $cid = \Xmf\Request::getInt('cid', 0, 'GET');
    }
}

$member_usid = $xoopsUser->getVar('uid', 'E');

if (!empty($_POST['submit'])) {
    if (!$GLOBALS['xoopsSecurity']->check(true, $_REQUEST['token'])) {
        redirect_header(XOOPS_URL . '/', 3, $GLOBALS['xoopsSecurity']->getErrors());
    }

    //  if ($xoopsModuleConfig["jobs_use_captcha"] == '1') {
    //  $x24plus = resume_isX24plus();
    //  if ($x24plus) {
    //  xoops_load("xoopscaptcha");
    //  $xoopsCaptcha = XoopsCaptcha::getInstance();
    //  if ( !$xoopsCaptcha->verify() ) {
    //        redirect_header( XOOPS_URL . "/modules/" . $xoopsModule->getVar('dirname') . "/index.php", 3, $xoopsCaptcha->getMessage() );
    //  }
    //  } else {
    //  xoops_load("captcha");
    //  $xoopsCaptcha = XoopsCaptcha::getInstance();
    //  if ( !$xoopsCaptcha->verify() ) {
    //        redirect_header( XOOPS_URL . "/modules/" . $xoopsModule->getVar('dirname') . "/index.php", 3, $xoopsCaptcha->getMessage() );
    //  }
    //  }
    //  }

    $resumesize = $helper->getConfig('jobs_resumesize');
    $photomax   = $helper->getConfig('jobs_maxfilesize');

    $_SESSION['name']        = $_POST['name'];
    $_SESSION['title']       = $_POST['title'];
    $_SESSION['status']      = $_POST['status'];
    $_SESSION['exp']         = $_POST['exp'];
    $_SESSION['expire']      = $_POST['expire'];
    $_SESSION['private']     = $_POST['private'];
    $_SESSION['tel']         = $_POST['tel'];
    $_SESSION['salary']      = $_POST['salary'];
    $_SESSION['typeprice']   = $_POST['typeprice'];
    $_SESSION['submitter']   = $_POST['submitter'];
    $_SESSION['town']        = $_POST['town'];
    $_SESSION['state']       = $_POST['state'];
    $_SESSION['make_resume'] = $_POST['make_resume'];
    $_SESSION['email']       = $_POST['email'];

    $name        = $myts->addSlashes($_POST['name']);
    $title       = $myts->addSlashes($_POST['title']);
    $status      = $myts->addSlashes($_POST['status']);
    $exp         = $myts->addSlashes($_POST['exp']);
    $expire      = $myts->addSlashes($_POST['expire']);
    $private     = $myts->addSlashes($_POST['private']);
    $tel         = $myts->addSlashes($_POST['tel']);
    $salary      = $myts->addSlashes($_POST['salary']);
    $typeprice   = $myts->addSlashes($_POST['typeprice']);
    $submitter   = $myts->addSlashes($_POST['submitter']);
    $town        = $myts->addSlashes($_POST['town']);
    $state       = $myts->addSlashes($_POST['state']);
    $make_resume = $myts->addSlashes($_POST['make_resume']);
    $valid       = $myts->addSlashes($_POST['valid']);
    $email       = $myts->addSlashes($_POST['email']);
    $usid        = $myts->addSlashes($member_usid);
    $date        = time();

    $filename = '';

    if (!empty($_FILES['resume']['name'])) {
        require_once XOOPS_ROOT_PATH . '/class/uploader.php';
        $updir             = 'resumes/';
        $allowed_mimetypes = ['application/msword', 'application/pdf'];
        $uploader          = new \XoopsMediaUploader($updir, $allowed_mimetypes, $resumesize);
        $uploader->setTargetFileName($date . '_' . $_FILES['resume']['name']);
        $uploader->fetchMedia('resume');
        if (!$uploader->upload()) {
            $errors = $uploader->getErrors();
            redirect_header('addresume.php?cid=' . addslashes($cid) . '', 3, $errors);

            return false;
            exit();
        } else {
            $filename = $uploader->getSavedFileName();
        }
    }

    $resumeHandler = $helper->getHandler('Resume');
    $resume = $resumeHandler->create();
    $resume->setVar('cid', $cid);
    $resume->setVar('name', $name);
    $resume->setVar('title', $title);
    $resume->setVar('status', $status);
    $resume->setVar('exp', $exp);
    $resume->setVar('expire', $expire);
    $resume->setVar('private', $private);
    $resume->setVar('tel', $tel);
    $resume->setVar('salary', $salary);
    $resume->setVar('typeprice', $typeprice);
    $resume->setVar('date', $date);
    $resume->setVar('email', $email);
    $resume->setVar('submitter', $submitter);
    $resume->setVar('usid', $usid);
    $resume->setVar('town', $town);
    $resume->setVar('state', ''.$state);
    $resume->setVar('valid', $valid);
    $resume->setVar('resume', $filename);

    if (!$resumeHandler->insert($resume)) {
        redirect_header('index.php', 3, _JOBS_RES_NOT_ADDED);
    }

    unset($_SESSION['name'], $_SESSION['title'], $_SESSION['status'], $_SESSION['exp'], $_SESSION['expire'], $_SESSION['private'], $_SESSION['tel'], $_SESSION['salary'], $_SESSION['typeprice'], $_SESSION['submitter'], $_SESSION['town'], $_SESSION['state'], $_SESSION['make_resume'], $_SESSION['email']);

    $lid = $resume->getVar('lid');
    if ('1' == $valid) {
        $notificationHandler = xoops_getHandler('notification');

        $tags                     = [];
        $tags['TITLE']            = $title;
        $tags['EXP']              = $exp;
        $tags['NAME']             = $name;
        $tags['HELLO']            = _JOBS_HELLO;
        $tags['WEBMASTER']        = _JOBS_WEBMASTER;
        $tags['ADDED_TO_RES_CAT'] = _JOBS_ADDED_TO_RES_CAT;
        $tags['FOLLOW_LINK']      = _JOBS_FOLLOW_LINK;
        $tags['RECIEVING_NOTIF']  = _JOBS_RECIEVING_NOTIF;
        $tags['ERROR_NOTIF']      = _JOBS_ERROR_NOTIF;
        $tags['LINK_URL']         = XOOPS_URL . '/modules/' . $moduleDirName . '/index.php?pa=viewlistings' . '&lid=' . addslashes($lid);
        $sql                      = 'SELECT title FROM ' . $xoopsDB->prefix('jobs_res_categories') . ' WHERE cid=' . $xoopsDB->escape($cid) . '';
        $result                   = $xoopsDB->query($sql);
        $row                      = $xoopsDB->fetchArray($result);
        $tags['CATEGORY_TITLE']   = $row['title'];
        $tags['CATEGORY_URL']     = XOOPS_URL . '/modules/' . $moduleDirName . '/index.php?pa=viewResume&cid="' . addslashes($cid);
        /** @var \XoopsNotificationHandler $notificationHandler */
        $notificationHandler = xoops_getHandler('notification');
        $notificationHandler->triggerEvent('res_global', 0, 'new_resume', $tags);
        $notificationHandler->triggerEvent('resume_category', $cid, 'new_resume_cat', $tags);
        $notificationHandler->triggerEvent('resume_listing', $lid, 'new_resume', $tags);
    }

    if ('0' != $make_resume) {
        redirect_header('createresume.php?lid=' . addslashes($lid) . '', 4, _JOBS_RES_ADDED_PLUS);
    } else {
        redirect_header('viewresume.php?lid=' . addslashes($lid) . '', 4, _JOBS_RES_ADDED);
    }
} else {
    $GLOBALS['xoopsOption']['template_main'] = 'jobs_addresume.tpl';
    include XOOPS_ROOT_PATH . '/header.php';
    require_once XOOPS_ROOT_PATH . '/class/xoopsformloader.php';
    $mytree = new Jobs\ResumeTree($xoopsDB->prefix('jobs_categories'), 'cid', 'pid');

    $cid    = \Xmf\Request::getInt('cid', 0);

    $member_id = $xoopsUser->getVar('uid', 'E');

    $resdays     = $helper->getConfig('jobs_res_days');
    $resumesize  = $helper->getConfig('jobs_resumesize');
    $resumesize1 = $helper->getConfig('jobs_resumesize') / 1024;
    $photomax    = $helper->getConfig('jobs_maxfilesize');
    $photomax1   = $helper->getConfig('jobs_maxfilesize') / 1024;

    list($numrows) = $xoopsDB->fetchRow($xoopsDB->query('SELECT cid, title, affprice FROM ' . $xoopsDB->prefix('jobs_res_categories') . ''));

    if ($numrows > 0) {
        $xoopsTpl->assign('private_head', _JOBS_RES_PRIVATE);
        $xoopsTpl->assign('add_head', _JOBS_RES_ADDLISTING3);
        $xoopsTpl->assign('days', $resdays);
        $xoopsTpl->assign('res_moderate2', _JOBS_RES_MODERATE2);

        if ('1' == $helper->getConfig('jobs_moderate_resume')) {
            $xoopsTpl->assign('res_moderate', _JOBS_RES_JOBMODERATE);
        } else {
            $xoopsTpl->assign('res_moderate', _JOBS_RES_JOBNOMODERATE);
        }

        if ($xoopsUser) {
            $iddd = $xoopsUser->getVar('uid', 'E');
            $idd  = $xoopsUser->getVar('name', 'E'); // Real name
            $idde = $xoopsUser->getVar('email', 'E');
            $iddn = $xoopsUser->getVar('uname', 'E'); // user name
        }
        $time = time();

        $_SESSION['name']        = !empty($_SESSION['name']) ? $_SESSION['name'] : '';
        $_SESSION['title']       = !empty($_SESSION['title']) ? $_SESSION['title'] : '';
        $_SESSION['status']      = !empty($_SESSION['status']) ? $_SESSION['status'] : '';
        $_SESSION['exp']         = !empty($_SESSION['exp']) ? $_SESSION['exp'] : '';
        $_SESSION['expire']      = !empty($_SESSION['expire']) ? $_SESSION['expire'] : '';
        $_SESSION['private']     = !empty($_SESSION['private']) ? $_SESSION['private'] : '';
        $_SESSION['tel']         = !empty($_SESSION['tel']) ? $_SESSION['tel'] : '';
        $_SESSION['salary']      = !empty($_SESSION['salary']) ? $_SESSION['salary'] : '';
        $_SESSION['typeprice']   = !empty($_SESSION['typeprice']) ? $_SESSION['typeprice'] : '';
        $_SESSION['submitter']   = !empty($_SESSION['submitter']) ? $_SESSION['submitter'] : '';
        $_SESSION['town']        = !empty($_SESSION['town']) ? $_SESSION['town'] : '';
        $_SESSION['state']       = !empty($_SESSION['state']) ? $_SESSION['state'] : '';
        $_SESSION['make_resume'] = !empty($_SESSION['make_resume']) ? $_SESSION['make_resume'] : '';
        $_SESSION['email']       = !empty($_SESSION['email']) ? $_SESSION['email'] : '';

        $result  = $xoopsDB->query('SELECT nom_type FROM ' . $xoopsDB->prefix('jobs_type') . ' ORDER BY nom_type');
        $result1 = $xoopsDB->query('SELECT nom_price FROM ' . $xoopsDB->prefix('jobs_price') . ' ORDER BY id_price');
        $result2 = $xoopsDB->query('SELECT rid, name FROM ' . $xoopsDB->prefix('jobs_region') . ' ORDER BY rid');

        ob_start();
        $form = new \XoopsThemeForm(_JOBS_ADD_LISTING, 'submitform', 'addresume.php');
        $form->setExtra('enctype="multipart/form-data"');
        //        $GLOBALS['xoopsGTicket']->addTicketXoopsFormElement($form, __LINE__, 1800, 'token');

        $form->addElement(new \XoopsFormText(_JOBS_RES_PCODE, 'private', 10, 10, '' . $_SESSION['private'] . ''), false);

        ob_start();
        $mytree->resume_makeMySelBox('title', 'title', $cid, 1, 'cid');
        $form->addElement(new \XoopsFormLabel(_JOBS_CAT3, ob_get_contents()), true);
        ob_end_clean();

        $radio        = new \XoopsFormRadio(_JOBS_STATUS, 'status', '' . $_SESSION['status'] . '1');
        $options['1'] = _JOBS_ACTIVE;
        $options['0'] = _JOBS_INACTIVE;
        $radio->addOptionArray($options);
        $form->addElement($radio, true);

        $form->addElement(new \XoopsFormText(_JOBS_RES_NAME, 'name', 40, 50, '' . $_SESSION['name'] . ''), true);
        $form->addElement(new \XoopsFormText(_JOBS_RES_HOW_LONG, 'expire', 40, 50, $resdays), true);
        $form->addElement(new \XoopsFormText(_JOBS_TITLE2, 'title', 40, 50, '' . $_SESSION['title'] . ''), true);
        $form->addElement(new \XoopsFormText(_JOBS_RES_EXP, 'exp', 40, 50, '' . $_SESSION['exp'] . ''), true);
        $form->addElement(new \XoopsFormText(_JOBS_RES_SALARY, 'salary', 40, 50, '' . $_SESSION['salary'] . ''), false);
        $sel_form = new \XoopsFormSelect(_JOBS_SALARYTYPE, 'typeprice', '' . $_SESSION['typeprice'] . '', '1', false);
        while (false !== (list($nom_price) = $xoopsDB->fetchRow($result1))) {
            $sel_form->addOption($nom_price, $nom_price);
        }
        $form->addElement($sel_form);

        if ($idd) {
            $form->addElement(new \XoopsFormLabel(_JOBS_RES_UNAME, $idd));
        } else {
            $form->addElement(new \XoopsFormLabel(_JOBS_RES_UNAME, $iddn));
        }
        $form->addElement(new \XoopsFormLabel(_JOBS_RES_UNAME, $idde));
        $form->addElement(new \XoopsFormText(_JOBS_TEL, 'tel', 40, 50, '' . $_SESSION['tel'] . ''), false);
        $form->addElement(new \XoopsFormText(_JOBS_TOWN, 'town', 40, 50, '' . $_SESSION['town'] . ''), false);

        $state_form = new \XoopsFormSelect(_JOBS_STATE, 'state', '' . $_SESSION['state'] . '', '0', false);
        while (false !== (list($rid, $name) = $xoopsDB->fetchRow($result2))) {
            $state_form->addOption('', _JOBS_SELECT_STATE);
            $state_form->addOption($rid, $name);
        }
        $form->addElement($state_form, true);

        $form->addElement(new \XoopsFormFile(_JOBS_RES_UPRESUME, 'resume', 0), false);

        $res_radio    = new \XoopsFormRadio(_JOBS_Q_NO_RESUME, 'make_resume', '' . $_SESSION['make_resume'] . '');
        $options['0'] = _JOBS_DONT_MAKE;
        $options['1'] = _JOBS_MAKE_RESUME;
        $res_radio->addOptionArray($options);
        $form->addElement($res_radio, true);

        //  if ($helper->getConfig('jobs_use_captcha') == '1') {
        //        $form->addElement(new \XoopsFormCaptcha(_JOBS_CAPTCHA, "xoopscaptcha", false), true);
        //  }

        if (0 == $helper->getConfig('jobs_moderate_resume')) {
            $form->addElement(new \XoopsFormHidden('valid', '1'), false);
        } else {
            $form->addElement(new \XoopsFormHidden('valid', '0'), false);
        }

        $form->addElement(new \XoopsFormHidden('usid', $iddd), false);
        $form->addElement(new \XoopsFormHidden('email', $idde), false);
        $form->addElement(new \XoopsFormHidden('submitter', $iddn), false);
        $form->addElement(new \XoopsFormHidden('date', $time), false);
        $form->addElement(new \XoopsFormButton('', 'submit', _JOBS_SUBMIT, 'submit'));
        $form->display();
        $xoopsTpl->assign('submit_form', ob_get_contents());
        ob_end_clean();
    }
}

include XOOPS_ROOT_PATH . '/footer.php';
