<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
$this->setFrameMode(true);

global $arTheme, $APPLICATION;
$APPLICATION->AddViewContent('right_block_class', 'catalog_page ');

$bShowLeftBlock = ($arTheme['LEFT_BLOCK_CATALOG_ROOT']['VALUE'] === 'Y' && !defined('ERROR_404'));
$bMobileSectionsCompact = $arTheme['MOBILE_LIST_SECTIONS_COMPACT_IN_SECTIONS']['VALUE'] === 'Y';
$APPLICATION->SetPageProperty('MENU', 'N');
?>
<div class="main-wrapper flexbox flexbox--direction-row">
	<div class="section-content-wrapper <?=($bShowLeftBlock ? 'with-leftblock' : '');?> flex-1">
		<?// intro text?>
		<?ob_start();?>
		<?$APPLICATION->IncludeComponent(
			"bitrix:main.include",
			"",
			Array(
				"AREA_FILE_SHOW" => "page",
				"AREA_FILE_SUFFIX" => "inc",
				"EDIT_TEMPLATE" => ""
			)
		);?>
		<?$html = ob_get_contents();?>
		<?ob_end_clean();?>
		<?if(trim($html)):?>
			<div class="text_before_items">
				<?= $html; ?>
			</div>
		<?endif;?>
		<?unset($html);?>
		<?
		// get section items count and subsections
		$arParams['CHECK_DATES'] = 'Y';
		$arItemFilter = CAllcorp3::GetCurrentSectionElementFilter($arResult["VARIABLES"], $arParams, false);
		$arSubSectionFilter = CAllcorp3::GetCurrentSectionSubSectionFilter($arResult["VARIABLES"], $arParams, false);
		$itemsCnt = CAllcorp3Cache::CIBlockElement_GetList(array("CACHE" => array("TAG" => CAllcorp3Cache::GetIBlockCacheTag($arParams["IBLOCK_ID"]))), $arItemFilter, array());
		$arSubSections = CAllcorp3Cache::CIBlockSection_GetList(array("CACHE" => array("TAG" => CAllcorp3Cache::GetIBlockCacheTag($arParams["IBLOCK_ID"]), "MULTI" => "Y")), $arSubSectionFilter, false, array("ID"));
		?>
		<?if(!$itemsCnt && !$arSubSections):?>
			<div class="alert alert-warning"><?=GetMessage("SECTION_EMPTY")?></div>
		<?else:?>
			<?CAllcorp3::CheckComponentTemplatePageBlocksParams($arParams, __DIR__);?>

			<?$sViewElementTemplate = ($arParams["SECTIONS_TYPE_VIEW"] == "FROM_MODULE" ? $arTheme["SECTIONS_TYPE_VIEW_CATALOG"]["VALUE"] : $arParams["SECTIONS_TYPE_VIEW"]);?>
			<?@include_once('page_blocks/'.$sViewElementTemplate.'.php');?>

			<?if(!$arSubSections):?>
				<?// section elements?>
				<?if(strlen($arParams["FILTER_NAME"])):?>
					<?$GLOBALS[$arParams["FILTER_NAME"]] = array_merge((array)$GLOBALS[$arParams["FILTER_NAME"]], $arItemFilter);?>
				<?else:?>
					<?$arParams["FILTER_NAME"] = "arrFilter";?>
					<?$GLOBALS[$arParams["FILTER_NAME"]] = $arItemFilter;?>
				<?endif;?>
				
				<?$sViewElementTemplate = ($arParams["SECTION_ELEMENTS_TYPE_VIEW"] == "FROM_MODULE" ? $arTheme["ELEMENTS_CATALOG_PAGE"]["VALUE"] : $arParams["SECTION_ELEMENTS_TYPE_VIEW"]);?>
				<?@include_once('page_blocks/'.$sViewElementTemplate.'.php');?>
			<?endif;?>
		<?endif;?>
		<?// outro text?>
		<?ob_start();?>
		<?$APPLICATION->IncludeComponent(
			"bitrix:main.include",
			"",
			Array(
				"AREA_FILE_SHOW" => "page",
				"AREA_FILE_SUFFIX" => "bottom",
				"EDIT_TEMPLATE" => ""
			)
		);?>
		<?$html = ob_get_contents();?>
		<?ob_end_clean();?>
		<?if(trim($html)):?>
			<div class="text_after_items">
				<?$APPLICATION->IncludeComponent(
					"bitrix:main.include",
					"",
					Array(
						"AREA_FILE_SHOW" => "page",
						"AREA_FILE_SUFFIX" => "bottom",
						"EDIT_TEMPLATE" => ""
					)
				);?>
			</div>
		<?endif;?>
		<?unset($html);?>
		<?/* --- Блок "Вам будет интересно" --- */?>
		<div class="catalog__block">
			<?
			// id инфоблока, в которых будем выводить статьи
			$IBLOCK_ID = 54;

			// Получаем массив id-шников элементов, выбранных в пользовательском поле "Статьи в каталоге" в конкретном разделе
			$rsSelectedItems = CIBlockSection::GetList(
			["SORT"=>"ASC"],
			["IBLOCK_ID"=>$IBLOCK_ID,"ID" =>$arResult["VARIABLES"]["ELEMENT_ID"]],
			false,
			["UF_ARTICLES_IN_CATALOG"],
			);

			$arSelectedIDs = $arSelectedItems["UF_ARTICLES_IN_CATALOG"];

			if(!empty($arSelectedIDs)){
				$GLOBALS["arArticlesByFilter"] = array("ID"=>$arSelectedIDs);
			} /*end if*/
			?>

			<?$APPLICATION->IncludeComponent(
				"bitrix:news.list", 
				"blog-list-seonik", 
				array(
					"ACTIVE_DATE_FORMAT" => "j F Y",
					"ADD_SECTIONS_CHAIN" => "Y",
					"AJAX_MODE" => "N",
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
					"DISPLAY_BOTTOM_PAGER" => "Y",
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
					"FILTER_NAME" => "arArticlesByFilter",
					"HIDE_LINK_WHEN_NO_DETAIL" => "N",
					"IBLOCK_ID" => "48",
					"IBLOCK_TYPE" => "aspro_allcorp3_content",
					"INCLUDE_IBLOCK_INTO_CHAIN" => "N",
					"INCLUDE_SUBSECTIONS" => "Y",
					"MESSAGE_404" => "",
					"NEWS_COUNT" => "3",
					"PAGER_BASE_LINK_ENABLE" => "N",
					"PAGER_DESC_NUMBERING" => "N",
					"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
					"PAGER_SHOW_ALL" => "N",
					"PAGER_SHOW_ALWAYS" => "N",
					"PAGER_TEMPLATE" => "ajax",
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
	</div> <!-- end catalog__block -->
	<?/*/ --- end Блок "Вам будет интересно" --- /*/?>

	</div>
	<?if($bShowLeftBlock):?>
		<?CAllcorp3::ShowPageType('left_block');?>
	<?endif;?>
</div>
