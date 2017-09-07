<br>
<div id="head"><a href="index.php"><{$nome_modulo}>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<{$lang_albumtitle}></a></div>
<hr>
<div id="Titulo">
    <h2><{$lang_gtitle}>&nbsp;&nbsp;&nbsp;<{$lang_showcase}></h2>
</div>

<h2><{$lang_nopicyet}></h2>
<ul id="album_photos">
    <{section name=i loop=$pics_array}>
        <li>
            <div class="photo_in_album">
                <p>
                    <a href="<{$path_uploads}>/<{$pics_array[i].url}>" target="_self" rel="lightbox[album]"
                       title="<{$pics_array[i].desc}>">
                        <img class="thumb" src="<{$path_uploads}>/thumbs/thumb_<{$pics_array[i].url}>" rel="lightbox"
                             title="<{$pics_array[i].desc}>">
                    </a>
                </p>

                <p id="desc"><{$pics_array[i].desc}></p><{ if ($isOwner) }>
                <form action="delpicture.php" method="post" id="deleteform" class="lado">
                    <{securityToken}><{*//mb*}>
                    <input type="hidden" value="<{$pics_array[i].cod_img}>" name="cod_img">
                    <input type="hidden" value="<{$pics_array[i].lid}>" name="lid">
                    <input name="submit" type="image" alt="<{$lang_delete}>" title="<{$lang_delete}>"
                           src="<{xoModuleIcons16 delete.png}>">

                </form>
                <form action="editdesc.php" method="post" id="editform" class="lado">
                    <{securityToken}><{*//mb*}>
                    <input type="hidden" value="<{$pics_array[i].cod_img}>" name="cod_img">
                    <input name="submit" type="image" alt="<{$lang_editdesc}>" title="<{$lang_editdesc}>"
                           src="<{xoModuleIcons16 edit.png}>">

                </form>
                <{ /if }>
            </div>
        </li>
    <{/section}>
</ul>

<{if $isOwner}>
<{if $permit}>
<p><{$lang_nb_pict}><br>
    <{$lang_max_nb_pict}></p>

<{else}>
<{$lang_no_prem_nb}><br>
<{$lang_not_premium}>
<br><{$lang_upgrade_now}>

<{ /if }>
<h3><{$form_picture.title}></h3>
<form name="<{$form_picture.name}>" action="<{$form_picture.action}>"
      method="<{$form_picture.method}>" <{$form_picture.extra}> id="submitpicture">


    <{if $xcube}>
        <{$form_picture.elements.XOOPS_G_TICKET.body}>
    <{else}>
        <{$form_picture.elements.XOOPS_TOKEN_REQUEST.body}>
    <{/if }>

    <p><strong><{$form_picture.elements.1.caption}></strong></p>
    <p><strong><{$form_picture.elements.sel_photo.caption}></strong>
        <{$form_picture.elements.sel_photo.body}></p>

    <p><strong><{$form_picture.elements.caption.caption}></strong>
        <{$form_picture.elements.caption.body}></p>
    <{$form_picture.elements.lid.body}>
    <{$form_picture.elements.submit_button.body}>
</form><{$form_picture.javascript}><{ /if }>
<div style="text-align: center; padding: 3px; margin: 3px;">
    <{$commentsnav}>
    <{$lang_notice}>
</div>

<div style="margin: 3px; padding: 3px;">
    <!-- start comments loop -->
    <{if $comment_mode == "flat"}>
        <{include file="db:system_comments_flat.tpl"}>
    <{elseif $comment_mode == "thread"}>
        <{include file="db:system_comments_thread.tpl"}>
    <{elseif $comment_mode == "nest"}>
        <{include file="db:system_comments_nest.tpl"}>
    <{/if}>
    <!-- end comments loop -->
</div>



