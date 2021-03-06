<?php

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

/**
 * @var DateUfComponent $component
 * @var array $arResult
 */
$component = $this->getComponent();
?>

<span class="fields date field-wrap">
	<?php
	foreach($arResult['value'] as $value)
	{
		?>
		<span class="fields date field-item">
			<input
				<?= $component->getHtmlBuilder()->buildTagAttributes($value['attrList']) ?>
			>
			<i
				<?= $component->getHtmlBuilder()->buildTagAttributes([
					'class' => $component->getHtmlBuilder()->getCssClassName() . ' icon',
					'onclick' => 'BX.calendar({
						node: this.previousElementSibling, 
						field: this.previousElementSibling, 
						bTime: false, 
						bSetFocus: false
					})'
				]) ?>
			>
			</i>
		</span>
		<?php
	}

	if(
		$arResult['userField']['MULTIPLE'] === 'Y'
		&&
		$arResult['additionalParameters']['SHOW_BUTTON'] !== 'N'
	)
	{
		print $component->getHtmlBuilder()->getCloneButton($arResult['fieldName']);
	}
	?>
</span>