<ul>
    <{foreach item=item from=$block.items}>
        <b><{$item.cat_name}></b>
        <b><{$item.link}></b>
        <br>
    <{/foreach}>
    <br>
    <b><{$block.link}></b><br><br>
    <b><{$block.add}></b>
</ul>
