<table width="100%" cellspacing="1" cellpadding="0" border="0" align="center">
    <tr>
        <td width="100%" align="center" valign="top" bgcolor="#FFFF00"><h2><b><{$block.sponsored}></b></h2></td>
    </tr>
</table>
<table border="0" cellpadding="0" cellspacing="0" height="20px" width="100%">
    <tr>
        <td bgcolor="#FFFF00" width="25%">&nbsp;&nbsp;<{$block.lang_company}></td>
        <td bgcolor="#FFFF00" width="20%"><b><{$block.lang_title}></b></td>

        <td bgcolor="#FFFF00" align="center" width="20%"><b><{$block.lang_date}></b></td>
        <td bgcolor="#FFFF00" align="center" width="20%"><b><{$block.lang_local}></b></td>
        <td bgcolor="#FFFF00" align="center" width="15%"><b><{$block.lang_hits}></b></td>
    </tr>
</table>
<hr>
<{foreach item=item from=$block.items}>
    <table border="0" cellpadding="0" cellspacing="0" width="100%">
        <tr bgcolor="<{cycle values="
    #ffff66,#FFFF33"}>">
            <{if $item.logo_link}>
                <td><{$item.logo_link}></td>
            <{/if}>
            <td width="20%"><b><{$item.ltitle}></b> <{$item.new}><br><{$item.type}></td>

            <td width="20%" align="center"><{$item.date}></td>
            <td width="20%" align="center"><{$item.town}></td>
            <td width="15%" align="center"><{$item.hits}></td>

        </tr>
    </table>
    <hr>
<{/foreach}>

