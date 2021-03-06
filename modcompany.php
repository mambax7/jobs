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
/** @var Jobs\Helper $helper */
$helper = Jobs\Helper::getInstance();
$myts      = \MyTextSanitizer::getInstance();
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
if (!$grouppermHandler->checkRight('jobs_submit', $perm_itemid, $groups, $module_id)) {
    redirect_header(XOOPS_URL . "/modules/$moduleDirName/index.php", 3, _NOPERM);
}
if (!$grouppermHandler->checkRight('jobs_premium', $perm_itemid, $groups, $module_id)) {
    $premium = 0;
} else {
    $premium = 1;
}

require_once XOOPS_ROOT_PATH . "/modules/$moduleDirName/include/functions.php";

$comp_id     = !isset($_REQUEST['comp_id']) ? null : $_REQUEST['comp_id'];
$member_usid = $xoopsUser->uid();
$extra_users = jobs_getXtraUsers($comp_id, $member_usid);
if (!empty($extra_users)) {
    $temp_premium = '1';
} else {
    $temp_premium = '0';
}

if (!empty($_POST['del_old'])) {
    $del_old = true;
} else {
    $del_old = false;
}

if (!empty($_POST['submit'])) {
    if (!$GLOBALS['xoopsSecurity']->check(true, $_REQUEST['token'])) {
        redirect_header(XOOPS_URL . "/modules/$moduleDirName/index.php", 3, implode('<br>', $GLOBALS['xoopsSecurity']->getErrors()));
    }

    $comp_img_old = \Xmf\Request::getString('comp_img_old', '' , 'POST');


    $destination = XOOPS_ROOT_PATH . "/modules/$moduleDirName/logo_images";
    $photomax    = $helper->getConfig('jobs_maxfilesize');
    $maxwide     = $helper->getConfig('jobs_resized_width');
    $maxhigh     = $helper->getConfig('jobs_resized_height');
    $date        = time();

    if (true === $del_old) {
        if (file_exists("$destination/$comp_img_old")) {
            unlink("$destination/$comp_img_old");
        }
        $comp_img_old = '';
    }
    $comp_user1 = $_POST['comp_user1'];
    $comp_user2 = $_POST['comp_user2'];
    if (('1' == $premium) || ('1' == $temp_premium)) {

        // START - check new entries for company users are OK - contributed by GreenFlatDog
        //  $comp_userid1 = jobs_getIdFromUname($comp_user1);
        //  $comp_userid2 = jobs_getIdFromUname($comp_user2);
        $comp_users = [];
        // get user id for the name entered for company user 1
        if (empty($_POST['comp_user1'])) {
            $comp_userid1 = '';
        } else {
            $comp_userid1 = jobs_getIdFromUname($comp_user1);
            // put name, id, what's entered and problem into an array
            $comp_users[$comp_user1]['name']  = $_POST['comp_user1'];
            $comp_users[$comp_user1]['id']    = $comp_userid1;
            $comp_users[$comp_user1]['entry'] = '&cuser1=';
            $comp_users[$comp_user1]['prob']  = '&prob1=';
        }
        // get user id for the name entered for company user 2
        if (empty($_POST['comp_user2'])) {
            $comp_userid2 = '';
        } else {
            $comp_userid2 = jobs_getIdFromUname($_POST['comp_user2']);
            // put name, id, what's entered and problem into an array
            $comp_users[$comp_user2]['name']  = $comp_user2;
            $comp_users[$comp_user2]['id']    = $comp_userid2;
            $comp_users[$comp_user2]['entry'] = '&cuser2=';
            $comp_users[$comp_user2]['prob']  = '&prob2=';
        }
        if (!empty($comp_users)) {
            // we have checks to make
            $grouppermHandler = xoops_getHandler('groupperm');
            $errs         = '';
            foreach ($comp_users as $u) {
                if ($u['id']) {
                    // we have user id for name entered
                    $xu   = new \XoopsUser($u['id']);
                    $grps =& $xu->getGroups();
                    if (!$grouppermHandler->checkRight('jobs_submit', 0, $grps, $module_id)) {
                        // no submit permission
                        $errs .= $u['entry'] . $u['name'] . $u['prob'] . 'p';
                    }
                } else {
                    // no user id for name entered
                    $errs .= $u['entry'] . $u['name'] . $u['prob'] . 'n';
                }
            }
            if ($errs) {
                // we are going to re-open the form and request corrections
                // add to the query string the comp user(s) with their usernames and what problems they have e.g.
                // name1=xyz and prob1=p (no submit permission) or prob1=n (not there)
                redirect_header('modcompany.php?comp_id=' . addslashes($comp_id . $errs) . '', 5, 'Correction required');
            }
        }
        // END - check new entries for company users are OK - GreenFlatDog
    } else {
        $comp_userid1 = '';
        $comp_userid2 = '';
    }
    $comp_name          = $myts->addSlashes($_POST['comp_name']);
    $comp_address       = $myts->addSlashes($_POST['comp_address']);
    $comp_address2      = $myts->addSlashes($_POST['comp_address2']);
    $comp_city          = $myts->addSlashes($_POST['comp_city']);
    $comp_state         = $myts->addSlashes($_POST['comp_state']);
    $comp_zip           = $myts->addSlashes($_POST['comp_zip']);
    $comp_phone         = $myts->addSlashes($_POST['comp_phone']);
    $comp_fax           = $myts->addSlashes($_POST['comp_fax']);
    $comp_url           = $myts->addSlashes($_POST['comp_url']);
    $comp_usid          = $myts->addSlashes($_POST['comp_usid']);
    $comp_contact       = $myts->addSlashes($_POST['comp_contact']);
    $comp_user1_contact = $myts->addSlashes($_POST['comp_user1_contact']);
    $comp_user2_contact = $myts->addSlashes($_POST['comp_user2_contact']);
    $comp_date_added    = $myts->addSlashes($_POST['comp_date_added']);

    if (!empty($_FILES['comp_img']['name'])) {
        require_once XOOPS_ROOT_PATH . '/class/uploader.php';
        $updir             = $destination;
        $allowed_mimetypes = ['image/gif', 'image/jpg', 'image/jpeg', 'image/pjpeg', 'image/x-png'];
        $uploader          = new \XoopsMediaUploader($updir, $allowed_mimetypes, $photomax, $maxwide, $maxhigh);
        $uploader->setTargetFileName($date . '_' . $_FILES['comp_img']['name']);
        $uploader->fetchMedia('comp_img');
        if (!$uploader->upload()) {
            $errors = $uploader->getErrors();
            redirect_header('modcompany.php?comp_id=' . addslashes($comp_id) . '', 5, $errors);
        } else {
            if ($comp_img_old) {
                if (@file_exists("$destination/$comp_img_old")) {
                    unlink("$destination/$comp_img_old");
                }
            }
            $comp_img_old = $uploader->getSavedFileName();
        }
    }

    $xoopsDB->query('update '
                    . $xoopsDB->prefix('jobs_companies')
                    . " set comp_id='$comp_id', comp_name='$comp_name', comp_address='$comp_address', comp_address2='$comp_address2', comp_city='$comp_city', comp_state='$comp_state', comp_zip='$comp_zip', comp_phone='$comp_phone', comp_fax='$comp_fax', comp_img='$comp_img_old',  comp_url='$comp_url', comp_usid='$comp_usid', comp_user1='$comp_userid1', comp_user2='$comp_userid2', comp_contact='$comp_contact', comp_user1_contact='$comp_user1_contact', comp_user2_contact='$comp_user2_contact', comp_date_added='$comp_date_added' where comp_id="
                    . $xoopsDB->escape($comp_id)
                    . '');

    redirect_header('members.php?comp_id=' . addslashes($comp_id) . '', 4, _JOBS_COMP_MOD);
} else {
    $GLOBALS['xoopsOption']['template_main'] = 'jobs_modcompany.tpl';
    include XOOPS_ROOT_PATH . '/header.php';
    require_once XOOPS_ROOT_PATH . '/class/xoopsformloader.php';
    $mytree = new Jobs\JobTree($xoopsDB->prefix('jobs_categories'), 'cid', 'pid');

    $token   = $GLOBALS['xoopsSecurity']->createToken();
    $comp_id = (\Xmf\Request::getInt('comp_id', 0, 'GET') > 0) ? \Xmf\Request::getInt('comp_id', 0, 'GET') : 0;

    echo "<script language=\"javascript\">\nfunction CLA(CLA) { var MainWindow = window.open (CLA, \"_blank\",\"width=500,height=300,toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,copyhistory=no\");}\n</script>";

    $photomax  = $helper->getConfig('jobs_maxfilesize');
    $maxwide   = $helper->getConfig('jobs_resized_width');
    $maxhigh   = $helper->getConfig('jobs_resized_height');
    $photomax1 = $helper->getConfig('jobs_maxfilesize') / 1024;

    $result = $xoopsDB->query('SELECT comp_id, comp_name, comp_address, comp_address2, comp_city, comp_state, comp_zip, comp_phone, comp_fax, comp_url, comp_img, comp_usid, comp_user1, comp_user2, comp_contact, comp_user1_contact, comp_user2_contact, comp_date_added FROM '
                              . $xoopsDB->prefix('jobs_companies')
                              . ' WHERE comp_id='
                              . $xoopsDB->escape($comp_id)
                              . ' ');
    list($comp_id, $comp_name, $comp_address, $comp_address2, $comp_city, $comp_state, $comp_zip, $comp_phone, $comp_fax, $comp_url, $comp_img_old, $comp_usid, $comp_user1, $comp_user2, $comp_contact, $comp_user1_contact, $comp_user2_contact, $comp_date_added) = $xoopsDB->fetchRow($result);

    if ($xoopsUser) {
        $member_id  = $xoopsUser->uid();
        $comp_users = [$comp_usid, $comp_user1, $comp_user2];
        if (in_array($member_id, $comp_users)) {
            $comp_name     = $myts->addSlashes($comp_name);
            $comp_address  = $myts->addSlashes($comp_address);
            $comp_address2 = $myts->addSlashes($comp_address2);
            $comp_city     = $myts->addSlashes($comp_city);
            $comp_state    = $myts->addSlashes($comp_state);
            $comp_zip      = $myts->addSlashes($comp_zip);
            $comp_phone    = $myts->addSlashes($comp_phone);
            $comp_fax      = $myts->addSlashes($comp_fax);
            $comp_url      = $myts->addSlashes($comp_url);
            $comp_usid     = $myts->addSlashes($comp_usid);

            $comp_state = stripslashes($comp_state);

            if (0 != $comp_user1) {
                xoops_load('xoopsuserutility');
                $comp_username1 = XoopsUser::getUnameFromId($comp_user1);
            } else {
                $comp_username1 = '';
            }

            if (0 != $comp_user2) {
                xoops_load('xoopsuserutility');
                $comp_username2 = XoopsUser::getUnameFromId($comp_user2);
            } else {
                $comp_username2 = '';
            }
            $comp_contact       = $myts->addSlashes($comp_contact);
            $comp_user1_contact = $myts->addSlashes($comp_user1_contact);
            $comp_user2_contact = $myts->addSlashes($comp_user2_contact);
            xoops_load('xoopsuserutility');
            $added_by   = XoopsUser::getUnameFromId($comp_usid);
            $useroffset = '';
            if ($xoopsUser) {
                $timezone = $xoopsUser->timezone();
                if (isset($timezone)) {
                    $useroffset = $xoopsUser->timezone();
                } else {
                    $useroffset = $xoopsConfig['default_TZ'];
                }
            }
            $dates = ($useroffset * 3600) + $comp_date_added;
            $dates = formatTimestamp($comp_date_added, 's');
            $date  = time();

            $alert_message = '';
            $alert1        = '';
            $alert2        = '';
            // START - check new entries for company users are OK - contributed by GreenFlatDog
            if (isset($_GET['cuser1']) && '' != $_GET['cuser1']) {
                $cuser1        = $_GET['cuser1'];
                $prob1         = $_GET['prob1'];
                $alert_message = _JOBS_PLS_CORRECT;
            } else {
                $cuser1 = '';
            }
            if (isset($_GET['cuser2']) && '' != $_GET['cuser2']) {
                $cuser2        = $_GET['cuser2'];
                $prob2         = $_GET['prob2'];
                $alert_message = _JOBS_PLS_CORRECT;
            } else {
                $cuser2 = '';
            }

            // END - check new entries for company users are OK - contributed by GreenFlatDog
            $result = $xoopsDB->query('SELECT rid, name FROM ' . $xoopsDB->prefix('jobs_region') . ' ORDER BY rid');
            ob_start();
            $form = new \XoopsThemeForm(_JOBS_MOD_COMPANY, 'modify_form', 'modcompany.php');
            $form->setExtra('enctype="multipart/form-data"');

            //            $GLOBALS['xoopsGTicket']->addTicketXoopsFormElement($form, __LINE__, 1800, 'token');

            echo "<span style='display:block; text-align:center; color:#f00;'>" . $alert_message . '</span>';
            $form->addElement(new \XoopsFormLabel(_JOBS_NUMANNN, $comp_id . ' ' . _JOBS_ADDED . ' ' . $dates));
            $form->addElement(new \XoopsFormLabel(_JOBS_SENDBY, $added_by));
            $form->addElement(new \XoopsFormLabel(_JOBS_COMPANY2, $comp_name));
            $form->addElement(new \XoopsFormHidden('comp_name', $comp_name));
            $form->addElement(new \XoopsFormText(_JOBS_COMPANY_ADDRESS, 'comp_address', 30, 100, $comp_address), false);
            $form->addElement(new \XoopsFormText(_JOBS_COMPANY_ADDRESS2, 'comp_address2', 30, 100, $comp_address2), false);
            $form->addElement(new \XoopsFormText(_JOBS_TOWN, 'comp_city', 30, 50, $comp_city), false);

            if ('1' == $helper->getConfig('jobs_show_state')) {
                $state_form = new \XoopsFormSelect(_JOBS_STATE, 'comp_state', $comp_state, '0', false);
                while (false !== (list($rid, $name) = $xoopsDB->fetchRow($result))) {
                    $state_form->addOption('', _JOBS_SELECT_STATE);
                    $state_form->addOption($rid, $name);
                }
                $form->addElement($state_form, true);
            } else {
                $form->addElement(new \XoopsFormHidden('comp_state', ''));
            }

            $form->addElement(new \XoopsFormText(_JOBS_COMPANY_ZIP, 'comp_zip', 30, 30, $comp_zip), false);
            $form->addElement(new \XoopsFormText(_JOBS_COMPANY_PHONE, 'comp_phone', 30, 30, $comp_phone), false);
            $form->addElement(new \XoopsFormText(_JOBS_COMPANY_FAX, 'comp_fax', 30, 30, $comp_fax), false);
            $form->addElement(new \XoopsFormText(_JOBS_COMPANY_SITEURL, 'comp_url', 30, 30, $comp_url), false);
            $form->addElement(new \XoopsFormTextArea(_JOBS_CONTACTINFO, 'comp_contact', $comp_contact, 6, 35), false);

            if ('1' == $premium || '1' == $temp_premium) {

                // START - check new entries for company users are OK - contributed by GreenFlatDog
                $alert = "<br><span style='color:#f00;'>%s%s</span>";
                if ($cuser1) {
                    $prob   = ('n' === $prob1) ? _JOBS_COMP_USER_NOTTHERE : _JOBS_COMP_USER_NOPERM;
                    $alert1 = sprintf($alert, $cuser1, $prob);
                    unset($prob);
                }
                $form->addElement(new \XoopsFormText(_JOBS_COMPANY_USER1 . $alert1, 'comp_user1', 30, 30, $comp_username1), false);
                $form->addElement(new \XoopsFormTextArea(_JOBS_USER1_CONTACT, 'comp_user1_contact', $comp_user1_contact, 6, 35), false);

                if ($cuser2) {
                    $prob   = ('n' === $prob2) ? _JOBS_COMP_USER_NOTTHERE : _JOBS_COMP_USER_NOPERM;
                    $alert2 = sprintf($alert, $cuser2, $prob);
                    unset($prob);
                }

                $form->addElement(new \XoopsFormText(_JOBS_COMPANY_USER2 . $alert2, 'comp_user2', 30, 30, $comp_username2), false);
                $form->addElement(new \XoopsFormTextArea(_JOBS_USER2_CONTACT, 'comp_user2_contact', $comp_user2_contact, 6, 35), false);

            // END - check new entries for company users are OK - contributed by GreenFlatDog
            } else {
                $form->addElement(new \XoopsFormHidden('comp_user1', ''));
                $form->addElement(new \XoopsFormHidden('comp_user1_contact', ''));
                $form->addElement(new \XoopsFormHidden('comp_user2', ''));
                $form->addElement(new \XoopsFormHidden('comp_user2_contact', ''));
            }

            if ($comp_img_old) {
                $comp_logo_link = "<a href=\"javascript:CLA('display-logo.php?comp_id=" . addslashes($comp_id) . "')\">$comp_img_old</a>";

                $form->addElement(new \XoopsFormLabel(_JOBS_ACTUALPICT, $comp_logo_link));

                $del_checkbox = new \XoopsFormCheckBox(_JOBS_DELPICT, 'del_old', $del_old);
                $del_checkbox->addOption(1, 'Yes');
                $form->addElement($del_checkbox);

                $form->addElement(new \XoopsFormFile(_JOBS_NEWPICT, 'comp_img', $helper->getConfig('jobs_maxfilesize')), false);

                $form->addElement(new \XoopsFormHidden('comp_img_old', $comp_img_old));
            } else {
                $form->addElement(new \XoopsFormFile(_JOBS_IMG, 'comp_img', $helper->getConfig('jobs_maxfilesize')), false);
            }

            $form->addElement(new \XoopsFormHidden('token', $token));
            $form->addElement(new \XoopsFormHidden('submit', '1'));
            $form->addElement(new \XoopsFormHidden('comp_name', $comp_name));
            $form->addElement(new \XoopsFormHidden('comp_usid', $comp_usid));
            $form->addElement(new \XoopsFormHidden('comp_id', $comp_id));
            $form->addElement(new \XoopsFormHidden('comp_date_added', $date));
            $form->addElement(new \XoopsFormButton('', 'submit', _JOBS_SUBMIT, 'submit'));
            $form->display();
            $xoopsTpl->assign('modify_form', ob_get_contents());
            ob_end_clean();
        }
    }
}
include XOOPS_ROOT_PATH . '/footer.php';
