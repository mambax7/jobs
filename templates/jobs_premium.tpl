<{if $item.premium}>
    <br>
    <table border="0" cellpadding="0" cellspacing="0" width="100%">
        <tr>
            <{if $xoops_isadmin}>
                <td width="5%"><{$item.admin}></td>
            <{/if}>
            <td width="20%"><b><{$item.title}></b> <{$item.new}><br><{$item.type}></td>
            <{if $show_company == '1'}>
                <td width="20%"><{$item.company}></td>
            <{/if}>
            <td width="20%" align="center"><{$item.date}></td>
            <td width="20%" align="center"><{$item.town}><{if $item.state}>, <{$item.state}><{/if}></td>
            <td width="15%" align="center"><{$item.views}></td>

        </tr>
    </table>
    <br>
    <hr>
<{/if}>
