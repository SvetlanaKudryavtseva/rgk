<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>
<?$this->setFrameMode(true);?>
<?

use Bitrix\Main\Loader,
	Bitrix\Main\Localization\Loc,
	Bitrix\Main\ModuleManager;

Loader::includeModule("iblock");

global $arTheme, $NextSectionID, $arRegion, $bHideLeftBlock;

$arPageParams = $arSection = $section = array();

if (!$arParams["SECTION_DISPLAY_PROPERTY"]) {
	$arParams["SECTION_DISPLAY_PROPERTY"] = "UF_VIEWTYPE";
}
$bOrderViewBasket = (trim($arTheme['ORDER_VIEW']['VALUE']) === 'Y');
$_SESSION['SMART_FILTER_VAR'] = $arParams['FILTER_NAME'];?>

<?$bShowLeftBlock = ($arTheme["LEFT_BLOCK_CATALOG_SECTIONS"]["VALUE"] == "Y" && !defined("ERROR_404") && !$bHideLeftBlock);?>

<?$APPLICATION->SetPageProperty("MENU", 'N');?>
<?$APPLICATION->AddViewContent('right_block_class', 'catalog_page ');?>

<?/*if(CAllcorp3::checkAjaxRequest2()):?>
	<div>
<?endif;*/?>

<div class="top-content-block">
	<?$APPLICATION->ShowViewContent('top_content');?>
	<?/*$APPLICATION->ShowViewContent('top_content2');*/?>
</div>

<?/*if(CAllcorp3::checkAjaxRequest2()):?>
	</div>
<?endif;*/?>

<?
$arParams['SHOW_ONE_CLINK_BUY'] = $arTheme["SHOW_ONE_CLICK_BUY"]["VALUE"];
$arParams['MAX_GALLERY_ITEMS'] = $arTheme["SHOW_CATALOG_GALLERY_IN_LIST"]["DEPENDENT_PARAMS"]["MAX_GALLERY_ITEMS"]["VALUE"];
$arParams['SHOW_GALLERY'] = $arTheme["SHOW_CATALOG_GALLERY_IN_LIST"]["VALUE"];

/* --- modification S 18.11.2022 - SLIDE PROPS --- */


$arOrder = [
	"SORT"=>"ASC"
];
$arFilter = [
  'IBLOCK_ID' => $arParams["IBLOCK_ID"],
	'ID' => $arResult["VARIABLES"]["SECTION_ID"],

];

$arSelect = [
  'UF_SLIDE_PROPS',
];

$resSections = CIBlockSection::GetList(false, $arFilter, false , $arSelect);
$arSection = $resSections->GetNext();
/*if($arSection = $resSections->GetNext()){ */
	/*print_r($arSection);*/
/*if($arSection['UF_SLIDE_PROPS']['VALUE'] == 1) {
    print_r('Да');
	}
}*/
/*/ --- end modification S 18.11.2022 - SLIDE PROPS --- /*/
?>

<?// get current section ID
$arSectionFilter = [];
if ($arResult["VARIABLES"]["SECTION_ID"] > 0) {
	$arSectionFilter = array('GLOBAL_ACTIVE' => 'Y', "ID" => $arResult["VARIABLES"]["SECTION_ID"], "IBLOCK_ID" => $arParams["IBLOCK_ID"]);
} elseif (strlen(trim($arResult["VARIABLES"]["SECTION_CODE"])) > 0) {
	$arSectionFilter = array('GLOBAL_ACTIVE' => 'Y', "=CODE" => $arResult["VARIABLES"]["SECTION_CODE"], "IBLOCK_ID" => $arParams["IBLOCK_ID"]);
}
if ($arSectionFilter) {
	$section = CAllcorp3Cache::CIBlockSection_GetList(array('CACHE' => array("MULTI" =>"N", "TAG" => CAllcorp3Cache::GetIBlockCacheTag($arParams["IBLOCK_ID"]))), CAllcorp3::makeSectionFilterInRegion($arSectionFilter), false, array("ID", "IBLOCK_ID", "NAME", "DESCRIPTION", "UF_TOP_SEO", 'UF_FILTER_VIEW', "UF_TABLE_PROPS", "UF_INCLUDE_SUBSECTION", "UF_PICTURE_RATIO", $arParams["SECTION_DISPLAY_PROPERTY"], "IBLOCK_SECTION_ID", "DEPTH_LEVEL", "LEFT_MARGIN", "RIGHT_MARGIN"));
}

$typeSKU = '';
$bSetElementsLineRow = false;

if ($section) {
	$arSection["ID"] = $section["ID"];
	$arSection["NAME"] = $section["NAME"];
	$arSection["IBLOCK_SECTION_ID"] = $section["IBLOCK_SECTION_ID"];
	$arSection["DEPTH_LEVEL"] = $section["DEPTH_LEVEL"];
	if ($section[$arParams["SECTION_DISPLAY_PROPERTY"]]) {
		$arDisplayRes = CUserFieldEnum::GetList(array(), array("ID" => $section[$arParams["SECTION_DISPLAY_PROPERTY"]]));
		if ($arDisplay = $arDisplayRes->GetNext()) {
			$arSection["DISPLAY"] = $arDisplay["XML_ID"];
		}
	}

	if (strlen($section["DESCRIPTION"])) {
		$arSection["DESCRIPTION"] = $section["DESCRIPTION"];
	}
	if (strlen($section["UF_TOP_SEO"])) {
		$arSection["UF_TOP_SEO"] = $section["UF_TOP_SEO"];
	}
	$posSectionDescr = COption::GetOptionString("aspro.allcorp3", "SHOW_SECTION_DESCRIPTION", "BOTTOM", SITE_ID);

	global $arSubSectionFilter;
	$arSubSectionFilter = array(
		"SECTION_ID" => $arSection["ID"],
		"IBLOCK_ID" => $arParams['IBLOCK_ID'],
		"ACTIVE" => "Y",
		"GLOBAL_ACTIVE" => "Y",
	);
	$iSectionsCount = count(CAllcorp3Cache::CIblockSection_GetList(array("CACHE" => array("TAG" => CAllcorp3Cache::GetIBlockCacheTag($arParams["IBLOCK_ID"]), "MULTI" => "Y")), CAllcorp3::makeSectionFilterInRegion($arSubSectionFilter)));

	if ($arParams['SHOW_MORE_SUBSECTIONS'] === 'N') {
		$iSectionsCount = 0;
	}

	// set smartfilter view
	$viewTmpFilter = 0;
	if ($section['UF_FILTER_VIEW']) {
		$viewTmpFilter = $section['UF_FILTER_VIEW'];
	}
	
	$viewTableProps = 0;
	if ($section['UF_TABLE_PROPS']) {
		$viewTableProps = $section['UF_TABLE_PROPS'];
	}

	$viewPictureRatio = 0;
	if ($section['UF_PICTURE_RATIO']) {
		$viewPictureRatio = $section['UF_PICTURE_RATIO'];
	}
	
	$includeSubsection = '';
	if ($section['UF_INCLUDE_SUBSECTION']) {
		$includeSubsection = $section['UF_INCLUDE_SUBSECTION'];
	}

	if (!$viewTmpFilter || !$arSection["DISPLAY"] || !$viewTableProps || !$includeSubsection) {
		if ($section['DEPTH_LEVEL'] > 1) {
			$sectionParent = CAllcorp3Cache::CIBlockSection_GetList(array('CACHE' => array("MULTI" =>"N", "TAG" => CAllcorp3Cache::GetIBlockCacheTag($arParams["IBLOCK_ID"]))), array('GLOBAL_ACTIVE' => 'Y', "ID" => $section["IBLOCK_SECTION_ID"], "IBLOCK_ID" => $arParams["IBLOCK_ID"]), false, array("ID", "IBLOCK_ID", "NAME", 'UF_FILTER_VIEW', "UF_TABLE_PROPS", "UF_PICTURE_RATIO", $arParams["SECTION_DISPLAY_PROPERTY"]));
			if ($sectionParent['UF_FILTER_VIEW'] && !$viewTmpFilter) {
				$viewTmpFilter = $sectionParent['UF_FILTER_VIEW'];
			}
			if ($sectionParent['UF_TABLE_PROPS'] && !$viewTableProps) {
				$viewTableProps = $sectionParent['UF_TABLE_PROPS'];
			}
			if ($sectionParent['UF_INCLUDE_SUBSECTION'] && !$includeSubsection) {
				$includeSubsection = $sectionParent['UF_INCLUDE_SUBSECTION'];
			}
			if ($sectionParent[$arParams["SECTION_DISPLAY_PROPERTY"]] && !$arSection["DISPLAY"]) {
				$arDisplayRes = CUserFieldEnum::GetList(array(), array("ID" => $sectionParent[$arParams["SECTION_DISPLAY_PROPERTY"]]));
				if ($arDisplay = $arDisplayRes->GetNext()) {
					$arSection["DISPLAY"] = $arDisplay["XML_ID"];
				}
			}

			if ($section['DEPTH_LEVEL'] > 2) {
				if (!$viewTmpFilter || !$arSection["DISPLAY"] || !$viewTableProps || !$includeSubsection) {
					$sectionRoot = CAllcorp3Cache::CIBlockSection_GetList(array('CACHE' => array("MULTI" =>"N", "TAG" => CAllcorp3Cache::GetIBlockCacheTag($arParams["IBLOCK_ID"]))), array('GLOBAL_ACTIVE' => 'Y', "<=LEFT_BORDER" => $section["LEFT_MARGIN"], ">=RIGHT_BORDER" => $section["RIGHT_MARGIN"], "DEPTH_LEVEL" => 1, "IBLOCK_ID" => $arParams["IBLOCK_ID"]), false, array("ID", "IBLOCK_ID", "NAME", 'UF_FILTER_VIEW', "UF_PICTURE_RATIO", "UF_TABLE_PROPS", $arParams["SECTION_DISPLAY_PROPERTY"]));
					if ($sectionRoot['UF_FILTER_VIEW'] && !$viewTmpFilter) {
						$viewTmpFilter = $sectionRoot['UF_FILTER_VIEW'];
					}
					if ($sectionRoot['UF_TABLE_PROPS'] && !$viewTableProps) {
						$viewTableProps = $sectionRoot['UF_TABLE_PROPS'];
					}
					if ($sectionRoot['UF_INCLUDE_SUBSECTION'] && !$includeSubsection) {
						$includeSubsection = $sectionRoot['UF_INCLUDE_SUBSECTION'];
					}
					if ($sectionRoot[$arParams["SECTION_DISPLAY_PROPERTY"]] && !$arSection["DISPLAY"]) {
						$arDisplayRes = CUserFieldEnum::GetList(array(), array("ID" => $sectionRoot[$arParams["SECTION_DISPLAY_PROPERTY"]]));
						if ($arDisplay = $arDisplayRes->GetNext()) {
							$arSection["DISPLAY"] = $arDisplay["XML_ID"];
						}
					}
				}
			}
		}
	}
	if ($viewTmpFilter) {
		$rsViews = CUserFieldEnum::GetList(array(), array('ID' => $viewTmpFilter));
		if ($arView = $rsViews->Fetch()) {
			$viewFilter = $arView['XML_ID'];
			$arTheme['FILTER_VIEW']['VALUE'] = strtoupper($viewFilter);
		}
	}
	if ($viewTableProps) {
		$rsViews = CUserFieldEnum::GetList(array(), array('ID' => $viewTableProps));
		if ($arView = $rsViews->Fetch()) {
			$typeTableProps = strtolower($arView['XML_ID']);
		}
	}
	if ($includeSubsection) {
		$rsViews = CUserFieldEnum::GetList(array(), array('ID' => $includeSubsection));
		if ($arView = $rsViews->Fetch()) {
			$arParams["INCLUDE_SUBSECTIONS"] = $arView['XML_ID'];
		}
	}

	if ($viewPictureRatio) {
		$rsViews = CUserFieldEnum::GetList(array(), array('ID' => $viewPictureRatio));
		if ($arView = $rsViews->Fetch()) {
			$arParams["PICTURE_RATIO"] = $arView['XML_ID'];
		}
	}

	$arParams['PICTURE_RATIO'] ?? strtolower(CAllcorp3::GetFrontParametrValue('ELEMENTS_IMG_TYPE'));

	$arElementFilter = array("SECTION_ID" => $arSection["ID"], "ACTIVE" => "Y", "INCLUDE_SUBSECTIONS" => $arParams["INCLUDE_SUBSECTIONS"], "IBLOCK_ID" => $arParams["IBLOCK_ID"]);
	if ($arParams["INCLUDE_SUBSECTIONS"] == "A") {
		$arElementFilter["INCLUDE_SUBSECTIONS"] = "Y";
		$arElementFilter["SECTION_GLOBAL_ACTIVE"] = "Y";
		$arElementFilter["SECTION_ACTIVE "] = "Y";
	}

	$itemsCnt = CAllcorp3Cache::CIBlockElement_GetList(array("CACHE" => array("TAG" => CAllcorp3Cache::GetIBlockCacheTag($arParams["IBLOCK_ID"]))), CAllcorp3::makeElementFilterInRegion($arElementFilter), array());
}

$linerow = $arParams["LINE_ELEMENT_COUNT"];

$bHideSideSectionBlock = ($arParams["SHOW_SIDE_BLOCK_LAST_LEVEL"] == "Y" && $iSectionsCount && $arParams["INCLUDE_SUBSECTIONS"] == "N");
if ($bHideSideSectionBlock) {
	$APPLICATION->SetPageProperty("MENU", "N");
}

$arParams['FILTER_VIEW'] = 'VERTICAL';
if($arTheme['SHOW_SMARTFILTER']['VALUE'] !== 'N' && $itemsCnt){
	if (
		$arTheme['SHOW_SMARTFILTER']['DEPENDENT_PARAMS']['FILTER_VIEW']['VALUE'] == 'COMPACT' || !$bShowLeftBlock
	) {
		$arParams['FILTER_VIEW'] = 'COMPACT';
	}
}

$bMobileSectionsCompact = $arTheme['MOBILE_LIST_SECTIONS_COMPACT_IN_SECTIONS']['VALUE'] === 'Y';
$bMobileItemsCompact = $arTheme['MOBILE_LIST_ELEMENTS_COMPACT_IN_SECTIONS']['VALUE'] === 'Y';
?>

<div class="main-wrapper flexbox flexbox--direction-row">
	<div class="section-content-wrapper <?=($bShowLeftBlock ? 'with-leftblock' : '');?> flex-1">
		<?if (!$section):?>
			<?\Bitrix\Iblock\Component\Tools::process404(
				""
				,($arParams["SET_STATUS_404"] === "Y")
				,($arParams["SET_STATUS_404"] === "Y")
				,($arParams["SHOW_404"] === "Y")
				,$arParams["FILE_404"]
			);?>
		<?endif;?>

		<?if ($section):?>
			<?
			//seo
			$catalogInfoIblockId = $arParams["LANDING_IBLOCK_ID"];
			if ($catalogInfoIblockId && !$bSimpleSectionTemplate) {
				$arSeoItems = CAllcorp3Cache::CIBLockElement_GetList(array('SORT' => 'ASC', 'CACHE' => array("MULTI" => "Y", "TAG" => CAllcorp3Cache::GetIBlockCacheTag($catalogInfoIblockId))), array("IBLOCK_ID" => $catalogInfoIblockId, "ACTIVE" => "Y"), false, false, array("ID", "IBLOCK_ID", "PROPERTY_FILTER_URL", "PROPERTY_LINK_REGION"));
				$arSeoItem = $arTmpRegionsLanding = array();
				if ($arSeoItems) {
					$iLandingItemID = 0;
					$current_url =  $APPLICATION->GetCurDir();
					$url = urldecode(str_replace(' ', '+', $current_url)); 
					foreach ($arSeoItems as $arItem) {
						if (!is_array($arItem['PROPERTY_LINK_REGION_VALUE'])) {
							$arItem['PROPERTY_LINK_REGION_VALUE'] = (array)$arItem['PROPERTY_LINK_REGION_VALUE'];
						}

						if (!$arSeoItem) {
							$urldecoded = urldecode($arItem["PROPERTY_FILTER_URL_VALUE"]);
							$urldecodedCP = iconv("utf-8", "windows-1251//IGNORE", $urldecoded);
							if ($urldecoded == $url || $urldecoded == $current_url || $urldecodedCP == $current_url) {
								if ($arItem['PROPERTY_LINK_REGION_VALUE']) {
									if ($arRegion && in_array($arRegion['ID'], $arItem['PROPERTY_LINK_REGION_VALUE'])) {
										$arSeoItem = $arItem;
									}
								} else {
									$arSeoItem = $arItem;
								}

								if ($arSeoItem) {
									$iLandingItemID = $arSeoItem['ID'];
									$arSeoItem = CAllcorp3Cache::CIBLockElement_GetList(array('SORT' => 'ASC', 'CACHE' => array("MULTI" => "N", "TAG" => CAllcorp3Cache::GetIBlockCacheTag($catalogInfoIblockId))), array("IBLOCK_ID" => $catalogInfoIblockId, "ID" => $iLandingItemID), false, false, array("ID", "IBLOCK_ID", "NAME", "PREVIEW_TEXT", "DETAIL_PICTURE", "PREVIEW_PICTURE", "PROPERTY_FILTER_URL", "PROPERTY_LINK_REGION", "PROPERTY_FORM_QUESTION", "PROPERTY_SECTION_SERVICES", "PROPERTY_TIZERS", "PROPERTY_SECTION", "DETAIL_TEXT", "PROPERTY_I_ELEMENT_PAGE_TITLE", "PROPERTY_I_ELEMENT_PREVIEW_PICTURE_FILE_ALT", "PROPERTY_I_ELEMENT_PREVIEW_PICTURE_FILE_TITLE", "PROPERTY_I_SKU_PAGE_TITLE", "PROPERTY_I_SKU_PREVIEW_PICTURE_FILE_ALT", "PROPERTY_I_SKU_PREVIEW_PICTURE_FILE_TITLE", "ElementValues"));

									$arIBInheritTemplates = array(
										"ELEMENT_PAGE_TITLE" => $arSeoItem["PROPERTY_I_ELEMENT_PAGE_TITLE_VALUE"],
										"ELEMENT_PREVIEW_PICTURE_FILE_ALT" => $arSeoItem["PROPERTY_I_ELEMENT_PREVIEW_PICTURE_FILE_ALT_VALUE"],
										"ELEMENT_PREVIEW_PICTURE_FILE_TITLE" => $arSeoItem["PROPERTY_I_ELEMENT_PREVIEW_PICTURE_FILE_TITLE_VALUE"],
										"SKU_PAGE_TITLE" => $arSeoItem["PROPERTY_I_SKU_PAGE_TITLE_VALUE"],
										"SKU_PREVIEW_PICTURE_FILE_ALT" => $arSeoItem["PROPERTY_I_SKU_PREVIEW_PICTURE_FILE_ALT_VALUE"],
										"SKU_PREVIEW_PICTURE_FILE_TITLE" => $arSeoItem["PROPERTY_I_SKU_PREVIEW_PICTURE_FILE_TITLE_VALUE"],
									);
								}
							}
						}

						if ($arItem['PROPERTY_LINK_REGION_VALUE']) {
							if (!$arRegion || !in_array($arRegion['ID'], $arItem['PROPERTY_LINK_REGION_VALUE'])) {
								$arTmpRegionsLanding[] = $arItem['ID'];
							}
						}
					}
				}

				if ($arSeoItems && $bHideSideSectionBlock) {
					$arSeoItems = [];
				}
			}

			if ($arRegion) {
				$arParams["USE_REGION"] = "Y";

				$GLOBALS[$arParams['FILTER_NAME']]['IBLOCK_ID'] = $arParams['IBLOCK_ID'];
				CAllcorp3::makeElementFilterInRegion($GLOBALS[$arParams['FILTER_NAME']]);
			}

			/* hide compare link from module options */
			if (CAllcorp3::GetFrontParametrValue('CATALOG_COMPARE') == 'N') {
				$arParams["USE_COMPARE"] = 'N';
			}
			
			$bContolAjax = (isset($_SERVER["HTTP_X_REQUESTED_WITH"]) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == "xmlhttprequest" && isset($_GET["control_ajax"]) && $_GET["control_ajax"] == "Y" );
			$sViewElementTemplate = ($arParams["SECTION_ELEMENTS_TYPE_VIEW"] == "FROM_MODULE" ? $arTheme["ELEMENTS_CATALOG_PAGE"]["VALUE"] : $arParams["SECTION_ELEMENTS_TYPE_VIEW"]);
			?>

			<? /* --- Блок тегов "Часто ищут" --- */ ?> 
			<div class="top-content-block">
				<div class="often-tags bordered rounded-4">
					<p>Часто ищут:</p>
						<div class="often-tags__items">
							<?$APPLICATION->ShowViewContent('top_content2');
								
							?>
						</div>
				</div>
			</div><!--- end top-content-block --->
			<? /*/ --- end Блок тегов "Часто ищут" --- /*/ ?> 

			<?// section elements?>
			<div class="js_wrapper_items<?=($arTheme["LAZYLOAD_BLOCK_CATALOG"]["VALUE"] == "Y" ? ' with-load-block' : '')?>" >
				<div class="js-load-wrapper">
					<?if($bContolAjax):?>
						<?$APPLICATION->RestartBuffer();?>
					<?endif;?>
					
					<?@include_once('page_blocks/'.$sViewElementTemplate.'.php');?>
					<?\Aspro\Allcorp3\Functions\Extensions::init('images_detail');?>

					<?if($bContolAjax):?>
						<?die();?>
					<?endif;?>
				</div>
			</div>

			<div class="js_wrapper_items<?=($arTheme["LAZYLOAD_BLOCK_CATALOG"]["VALUE"] == "Y" ? ' with-load-block' : '')?>" >
				<div class="js-load-wrapper">
					<?if($bContolAjax):?>
						<?$GLOBALS['APPLICATION']->RestartBuffer();?>
					<?endif;?>

					<? /* --- Блок "Вам будет интересно" --- */ ?> 
					<?
					// id инфоблока, в котором будем выводить статьи
					$IBLOCK_ID = $arParams['IBLOCK_ID'];
					/*print_r($IBLOCK_ID);*/
			
			
					// Получаем массив id-шников элементов, выбранных в пользовательском поле "Статьи в каталоге" в конкретном разделе
					$rsSelectedItems = CIBlockSection::GetList(
						["SORT"=>"ASC"],
						["IBLOCK_ID"=>$IBLOCK_ID,"ID" =>$arSection["ID"]],
						false,
						["UF_ARTICLES_IN_CATALOG"],
					);
					?>
					<?while($arSelectedArticles = $rsSelectedItems->GetNext()):?>
						<?$arSelectedIDs = $arSelectedArticles["UF_ARTICLES_IN_CATALOG"];?>
			
						<?if(!empty($arSelectedIDs)):?>
							<?$GLOBALS["arFilterArticlesInCatalog"] = array("ID"=>$arSelectedIDs);?>
						<?endif;?>
					<?endwhile;?>
			
					<?if(!empty($arSelectedIDs)):?>
						<?$APPLICATION->IncludeComponent(
							"bitrix:news.list", 
							"blog-list-seonik", 
							array(
								"ACTIVE_DATE_FORMAT" => "j F Y",
								"ADD_SECTIONS_CHAIN" => "Y",
								"AJAX_MODE" => "Y",
								"AJAX_OPTION_ADDITIONAL" => "",
								"AJAX_OPTION_HISTORY" => "N",
								"AJAX_OPTION_JUMP" => "N",
								"AJAX_OPTION_STYLE" => "Y",
								"CACHE_FILTER" => "Y",
								"CACHE_GROUPS" => "Y",
								"CACHE_TIME" => "36000000",
								"CACHE_TYPE" => "A",
								"CHECK_DATES" => "Y",
								"COMPONENT_TEMPLATE" => "blog-list-seonik",
								"DETAIL_URL" => "/articles/#SECTION_CODE_PATH#/#ELEMENT_CODE#/",
								"DISPLAY_BOTTOM_PAGER" => "N",
								"DISPLAY_DATE" => "Y",
								"DISPLAY_NAME" => "Y",
								"DISPLAY_PICTURE" => "Y",
								"DISPLAY_PREVIEW_TEXT" => "Y",
								"DISPLAY_TOP_PAGER" => "N",
								"FIELD_CODE" => array(
									0 => "NAME",
									1 => "PREVIEW_TEXT",
									2 => "PREVIEW_PICTURE",
									3 => "DATE_ACTIVE_FROM",
									4 => "",
								),
								"FILTER_NAME" => "arFilterArticlesInCatalog",
								"HIDE_LINK_WHEN_NO_DETAIL" => "N",
								"IBLOCK_ID" => "48",
								"IBLOCK_TYPE" => "aspro_allcorp3_content",
								"INCLUDE_IBLOCK_INTO_CHAIN" => "N",
								"INCLUDE_SUBSECTIONS" => "Y",
								"MESSAGE_404" => "",
								"NEWS_COUNT" => "1",
								"PAGER_BASE_LINK_ENABLE" => "N",
								"PAGER_DESC_NUMBERING" => "N",
								"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
								"PAGER_SHOW_ALL" => "N",
								"PAGER_SHOW_ALWAYS" => "N",
								"PAGER_TEMPLATE" => "main",
								"PAGER_TITLE" => "",
								"PARENT_SECTION" => "",
								"PARENT_SECTION_CODE" => "",
								"PREVIEW_TRUNCATE_LEN" => "",
								"PROPERTY_CODE" => array(
									0 => "REDIRECT",
									1 => "PERIOD",
									2 => "SALE_NUMBER",
									3 => "",
								),
								"RIGHT_LINK" => "articles/",
								"RIGHT_TITLE" => "Все статьи",
								"SET_BROWSER_TITLE" => "N",
								"SET_LAST_MODIFIED" => "N",
								"SET_META_DESCRIPTION" => "N",
								"SET_META_KEYWORDS" => "N",
								"SET_STATUS_404" => "N",
								"SET_TITLE" => "N",
								"SHOW_404" => "N",
								"SHOW_PREVIEW_TEXT" => "Y",
								"SORT_BY1" => "ACTIVE_FROM",
								"SORT_BY2" => "SORT",
								"SORT_ORDER1" => "DESC",
								"SORT_ORDER2" => "ASC",
								"STRICT_SECTION_CHECK" => "N",
								"SUBTITLE" => "",
								"TITLE" => "Вам будет интересно",
								"USE_FILTER" => "Y"
							),
							false
						);?>
					<?endif;?>
					<? /*/ --- end Блок "Вам будет интересно" --- /*/ ?>
					<?if($bContolAjax):?>
						<?die();?>
					<?endif;?>
				</div>
			</div>
			
		<?// Блок "Вы также смотрели" ?>
		<?$APPLICATION->IncludeComponent(
			"bitrix:catalog.products.viewed",
			".default",
			Array(
				"ACTION_VARIABLE" => "action_cpv",
				"ADD_PROPERTIES_TO_BASKET" => "Y",
				"ADD_TO_BASKET_ACTION" => "ADD",
				"BASKET_URL" => "/personal/basket.php",
				"CACHE_GROUPS" => "Y",
				"CACHE_TIME" => "3600",
				"CACHE_TYPE" => "A",
				"COMPONENT_TEMPLATE" => ".default",
				"CONVERT_CURRENCY" => "N",
				"DEPTH" => "",
				"DISPLAY_COMPARE" => "N",
				"ENLARGE_PRODUCT" => "STRICT",
				"HIDE_NOT_AVAILABLE" => "N",
				"HIDE_NOT_AVAILABLE_OFFERS" => "N",
				"IBLOCK_ID" => "54",
				"IBLOCK_MODE" => "single",
				"IBLOCK_TYPE" => "aspro_allcorp3_catalog",
				"LABEL_PROP_POSITION" => "top-left",
				"MESS_BTN_ADD_TO_BASKET" => "В корзину",
				"MESS_BTN_BUY" => "Купить",
				"MESS_BTN_DETAIL" => "Подробнее",
				"MESS_BTN_SUBSCRIBE" => "Подписаться",
				"MESS_NOT_AVAILABLE" => "Нет в наличии",
				"PAGE_ELEMENT_COUNT" => "6",
				"PARTIAL_PRODUCT_PROPERTIES" => "N",
				"PRICE_CODE" => array(),
				"PRICE_VAT_INCLUDE" => "Y",
				"PRODUCT_BLOCKS_ORDER" => "price,props,sku,quantityLimit,quantity,buttons",
				"PRODUCT_ID_VARIABLE" => "id",
				"PRODUCT_PROPS_VARIABLE" => "prop",
				"PRODUCT_QUANTITY_VARIABLE" => "quantity",
				"PRODUCT_ROW_VARIANTS" => "[{'VARIANT':'6','BIG_DATA':false}]",
				"PRODUCT_SUBSCRIPTION" => "N",
				"SECTION_CODE" => "",
				"SECTION_ELEMENT_CODE" => "",
				"SECTION_ELEMENT_ID" => $GLOBALS["CATALOG_CURRENT_ELEMENT_ID"],
				"SECTION_ID" => $GLOBALS["CATALOG_CURRENT_SECTION_ID"],
				"SHOW_CLOSE_POPUP" => "N",
				"SHOW_DISCOUNT_PERCENT" => "N",
				"SHOW_FROM_SECTION" => "N",
				"SHOW_MAX_QUANTITY" => "N",
				"SHOW_OLD_PRICE" => "N",
				"SHOW_PRICE_COUNT" => "1",
				"SHOW_SLIDER" => "Y",
				"SLIDER_INTERVAL" => "3000",
				"SLIDER_PROGRESS" => "Y",
				"TEMPLATE_THEME" => "green",
				"USE_ENHANCED_ECOMMERCE" => "N",
				"USE_PRICE_COUNT" => "N",
				"USE_PRODUCT_QUANTITY" => "N"
			)
		);?>
		<?// end Блок "Вы также смотрели"?>
		<?// Вывод блока "Вы также смотрели" по-другому?>
		<?
		$arViewed = [];
		$basketUserId = (int) CSaleBasket::GetBasketUserID(false);
		if ($basketUserId > 0) {
		$viewedIterator = \Bitrix\Catalog\CatalogViewedProductTable::getList([
		'select' => ['PRODUCT_ID', 'ELEMENT_ID'],
		'filter' => ['=FUSER_ID' => $basketUserId, '=SITE_ID' => SITE_ID],
		'order' => ['DATE_VISIT' => 'DESC'],
		'limit' => 10
		]);
		while ($arFields = $viewedIterator->fetch()) {
		$arViewed[] = $arFields['ELEMENT_ID'];
		}
		}
		$cnt = count($arViewed);

		/*echo '<pre>';
		print_r($arViewed);
		echo '</pre>';*/
		$arParams["T_GOODS"] = 'Вы также смотрели';
		?>
		<?if(!isset($html_goods)):?>
			<?$GLOBALS['arrGoodsFilter'] = array('ID' => $arViewed);?>
			<?
			$bCheckAjaxBlock = CAllcorp3::checkRequestBlock("goods-list-inner");
			$isAjax = (CAllcorp3::checkAjaxRequest() && $bCheckAjaxBlock ) ? 'Y' : 'N';
			?>
			<?ob_start();?>
					<?\Aspro\Functions\CAsproAllcorp3::showBlockHtml([
							'FILE' => '/detail_linked_goods.php',
							'PARAMS' => array_merge(
									$arParams,
									array(
											'ORDER_VIEW' => $bOrderViewBasket,
											'ITEM_1200' => '6',
											'CHECK_REQUEST_BLOCK' => $bCheckAjaxBlock,
						'IS_AJAX' => $isAjax,
									)
							)
					]);?>
			<?$html_goods = trim(ob_get_clean());?>
	<?endif;?>

				<?if($html_goods && strpos($html_goods, 'error') === false):?>
						<?if($bTab):?>
								<?if(!isset($bShow_goods)):?>
										<?$bShow_goods = true;?>
								<?else:?>
										<div class="tab-pane <?=(!($iTab++) ? 'active' : '')?>" id="goods">
												<div class="ordered-block__title switcher-title font_22"><?=$arParams["T_GOODS"]?></div>
												<div class="ajax-pagination-wrapper" data-class="goods-list-inner">
														<?if ($isAjax === 'Y'):?>
																<?$APPLICATION->RestartBuffer();?>
														<?endif;?>
																<?=$html_goods?>
														<?if ($isAjax === 'Y'):?>
																<?die();?>
														<?endif;?>
												</div>
										</div>
								<?endif;?>
						<?else:?>
								<div class="detail-block ordered-block goods">
										<div class="ordered-block__title switcher-title font_22"><?=$arParams["T_GOODS"]?></div>
										<div class="ajax-pagination-wrapper" data-class="goods-list-inner">
												<?if ($isAjax === 'Y'):?>
														<?$APPLICATION->RestartBuffer();?>
												<?endif;?>
														<?=$html_goods?>
												<?if ($isAjax === 'Y'):?>
														<?die();?>
												<?endif;?>
										</div>
								</div>
						<?endif;?>
				<?endif;?>

					<?// end?>
		<?// end Вывод блока "Вы также смотрели" по-другому?>

			<?$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH.'/js/jquery.history.js');?>
		<?else:?>
			<div class="alert alert-danger">
				<?=($arParams['MESSAGE_404'] ?:Loc::getMessage("NOT_FOUNDED_SECTION"));?>
			</div>
		<?endif;?>


	</div>
	<?if($bShowLeftBlock):?>
		<?CAllcorp3::ShowPageType('left_block');?>
	<?endif;?>
</div>

<?
CAllcorp3::setCatalogSectionDescription(
	array(
		'FILTER_NAME' => $arParams['FILTER_NAME'],
		'CACHE_TYPE' => $arParams['CACHE_TYPE'],
		'CACHE_TIME' => $arParams['CACHE_TIME'],
		'SECTION_ID' => $arSection['ID'],
		'SHOW_SECTION_DESC' => $arParams['SHOW_SECTION_DESC'],
		'SEO_ITEM' => $arSeoItem,
	)
);
?>