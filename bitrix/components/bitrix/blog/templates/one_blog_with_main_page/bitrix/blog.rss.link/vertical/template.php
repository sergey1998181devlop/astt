<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
if(!empty($arResult))
{
	?>
	<table width="0" cellpadding="0" cellspacing="5" border="0" align="center">
	<?
	foreach($arResult as $v)
	{
		?><tr><td><a href="<?=$v["url"]?>" title="<?=$v["name"]?>" class="blog-rss-<?=$v["type"]?>"></a></td></tr><?
	}
	?>
	</table>
	<?
}
?>