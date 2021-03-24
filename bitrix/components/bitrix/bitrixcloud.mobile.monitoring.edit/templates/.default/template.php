<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arData = array(
	array(
		"TYPE" => "BLOCK",
		"TITLE" => GetMessage("BCLMME_DOMAIN_TITLE"),
		"DATA" => array(
			array(
				"TYPE" => "TEXT_RO",
				"VALUE" => $arResult["DOMAIN_CONVERTED"]
			)
		)
	),
	array(
		"TYPE" => "BLOCK",
		"TITLE" => GetMessage("BCLMME_HEAD"),
		"DATA" => array(
			array(
				"TITLE" => GetMessage("BCLMME_HTTP_RESPONSE_TIME_TITLE"),
				"CHECKED" => in_array("test_http_response_time", $arResult["DOMAIN_PARAMS"]["TESTS"]) ? true : false,
				"VALUE" => "test_http_response_time",
				"NAME" => "TESTS[]",
				"TYPE" => "CHECKBOX"
				),
			array(
				"TITLE" => GetMessage("BCLMME_TEST_DOMAIN_REGISTRATION_TITLE"),
				"CHECKED" => in_array("test_domain_registration", $arResult["DOMAIN_PARAMS"]["TESTS"]) ? true : false,
				"VALUE" => "test_domain_registration",
				"NAME" => "TESTS[]",
				"TYPE" => "CHECKBOX"
				),
			array(
				"TITLE" => GetMessage("BCLMME_TEST_LICENSE_TITLE"),
				"CHECKED" => in_array("test_lic", $arResult["DOMAIN_PARAMS"]["TESTS"]) ? true : false,
				"VALUE" => "test_lic",
				"NAME" => "TESTS[]",
				"TYPE" => "CHECKBOX"
				),
			array(
				"TITLE" => GetMessage("BCLMME_IS_HTTPS_TITLE"),
				"CHECKED" => $arResult["DOMAIN_PARAMS"]["IS_HTTPS"] == "Y" ? true : false,
				"VALUE" => "Y",
				"NAME" => "IS_HTTPS",
				"TYPE" => "CHECKBOX"
				)
			)
		),
	array(
		"TYPE" => "HIDDEN",
		"VALUE" => $arResult["LANG"],
		"NAME" => "LANG"
		)
	);

	$arData[] = array(
		"TYPE" => "BLOCK",
		"TITLE" => GetMessage("BCLMME_EMAILS_TITLE"),
		"DATA" => array(
			array(
				"TYPE" => "TEXT",
				"VALUES" => $arResult["DOMAIN_PARAMS"]["EMAILS"],
				"NAME" => "EMAILS[]"
			)

		)
	);

$APPLICATION->IncludeComponent(
	'bitrix:mobileapp.edit',
	'.default',
	array(
		"TITLE" => GetMessage("BCLMME_TITLE"),
		"DATA" => $arData,
		"ON_JS_CLICK_SUBMIT_BUTTON" => "OnBCMMSiteSubmit",
		"BUTTONS" => array("SAVE")
		),
	false
);
?>

<script type="text/javascript">
	var listParams  = {
		ajaxUrl: "<?=$arResult["AJAX_PATH"]?>"
	};

	var bcmme = new __BitrixCloudMobMonEdt;
	var bcmm = new __BitrixCloudMobMon (listParams);

	function OnBCMMSiteSubmit(form)
	{
		var fields = bcmme.getFields(form);

		if(fields)
			bcmm.updateSite("<?=$arResult["DOMAIN_PARAMS"]["DOMAIN"]?>", fields);
	}

	BX.addCustomEvent('onAfterBCMMSiteDelete', function (params){
								if(params.domain == "<?=$arResult["DOMAIN_PARAMS"]["DOMAIN"]?>")
								{
										app.checkOpenStatus({callback:function(response)
											{
												if(response)
												{
													if(response.status == "visible")
													{
														app.closeController({drop:true});
													}
													else
													{
														BX.addCustomEvent( "onOpenPageAfter", function() {
																app.closeController({drop:true});
															}
														);
													}
												}
											}
										});
								}
		});

	BX.addCustomEvent('onAfterBCMMSiteUpdate', function (params){
								if(params.domain == "<?=$arResult["DOMAIN_PARAMS"]["DOMAIN"]?>")
								{
									app.checkOpenStatus({callback:function(response)
										{
											if(response)
											{
												if(response.status == "visible")
												{
													app.closeController({drop:true});
												}
												else
												{
													bcmm.showRefreshing();
													location.reload(true);
												}
											}
										}
									});
								}
		});

app.hidePopupLoader();
</script>
