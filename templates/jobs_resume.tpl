<{if $ad_exists}>
    <table cellspacing="1" class="outer" style="width:100%;">
        <tr>
            <th align="center"><{$add_from_sitename}> <{$add_from_title}></th>
        </tr>

        <{if $not_yet_approved}>
            <tr>
                <td class="odd" align="left"><{$not_yet_approved}></td>
            </tr>
        <{/if}>

        <tr>
            <td class="odd" align="left"><{$nav_jobs}> <{$nav_main}> <{$nav_sub}> (<{$nav_subcount}>)</td>
        </tr>
        <tr>
            <td class="even"><{$admin}> <b><{$name}></b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<{$add_photos}></td>
        </tr>
    </table>
    <table cellspacing="1" class="outer" style="width:100%;">
    <tr>
        <td class="even"><b><{$title}></b></td>
    </tr>
    <tr>
        <td class="even"><b><{$res_experience_head}></b><{$exp}></td>
    </tr>
    <tr>
        <td class="odd"><b><{$price_head}></b><{$price_price}>&nbsp;&nbsp;<{$price_typeprice}></td>
    </tr>
    <tr>
        <td class="odd"><b><{$local_head}></b> <{$local_town}><{if $state}>, <{$state}><{/if}></td>
    </tr>
    <{if $resume != ""}>
        <tr>
            <td class="odd"><{$resume}></td>
        </tr>
        <{if $private !="" && $unlocked != $private}>
            <tr>
                <td class="odd"><{$show_private}></td>
            </tr>
            </table>
            <table cellspacing="1" class="outer" style="width:100%;">
                <tr>
                    <td>
                        <form action="viewresume.php?lid=<{$id}>&unlock=<{$unlocked}>" method="post" name="unlock">
                            <{securityToken}><{*//mb*}>
                            <table>
                                <input type="hidden" name="lid" value="$lid"/>
                                <{$access}><input type="text" name="unlock" size="10"/>
                                <input type="submit" name="submit" class="button" value="submit"/>
                            </table>
                        </form>
                    </td>
                </tr>
            </table>
            <table cellspacing="1" class="outer" style="width:100%;">
        <{/if}>
    <{else}>
        <tr>
            <td class="odd">&nbsp;
                <{$noresume}>
            </td>
        </tr>
    <{/if}>

<{if $xoops_isuser}>
    <tr>
        <td class="odd"><b><{$contact_head}></b> <{$contact_email}> &nbsp;&nbsp;
            <b><{$contact_tel_head}></b> <{$contact_tel}>
    </tr>
<{else}>
    <tr>
        <td class="odd"><{$job_mustlogin}></td>
    </tr>
    <tr><{/if}>
    <tr>
        <td class="head"><{$submitter}>&nbsp;&nbsp;&nbsp; (<{$read}>)&nbsp;&nbsp;<{$modify}></td>
    </tr>
    <td class="foot" align="right"><{$date}><{if $xoops_isuser}>&nbsp;&nbsp;<{$friend}><{/if}></td>
    </tr>
    </table>
    <{if $private == "" || $unlocked == $private}>
        <table cellspacing="1" class="outer" style="width:100%;">
            <{if $rphoto != "0"}>
                <td class="odd"><{$photo}> <{if $more_photos}><br><{$more_photos}><{/if}></td>
            <{/if}>
            </tr></table>
    <{/if}>

<{else}>
    <div align="center"><b><{$no_ad}></b></div>
<{/if}>
<br><br>
<div align="center"><b>[ <{$link_main}> ]</b></div>
<br><br>

