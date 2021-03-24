<?php

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

/**
 * @var DoubleUfComponent $component
 * @var array $arResult
 */
?>

<span class="fields integer field-wrap">
	<?php
	foreach($arResult['value'] as $item)
	{
		?>
		<span class="fields integer field-item">
			<?php
			if(!empty($item['href']))
			{
				print "<a href=\"{$item['href']}\">{$item['value']}</a>";
			}
			else
			{
				print $item['value'];
			}
			?>
		</span>
		<?php
	}
	?>
</span>