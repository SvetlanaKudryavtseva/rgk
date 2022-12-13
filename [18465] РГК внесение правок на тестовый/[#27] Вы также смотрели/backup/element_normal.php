<?
use Bitrix\Main\Loader,
	Bitrix\Main\ModuleManager;

global $arTheme, $APPLICATION;

$APPLICATION->AddViewContent('right_block_class', 'catalog_page ');
$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH.'/css/animation/animate.min.css');
$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH.'/js/jquery.history.js');

// cart
$bOrderViewBasket = (trim($arTheme['ORDER_VIEW']['VALUE']) === 'Y');

if($arSection){
	$arInherite = CAllcorp3::getSectionInheritedUF(array(
		'sectionId' => $arSection['ID'],
		'iblockId' => $arSection['IBLOCK_ID'],
		'select' => array(
			'UF_ELEMENT_DETAIL',
			'UF_OFFERS_TYPE',
			'UF_GALLERY_SIZE',
			'UF_PICTURE_RATIO',
		),
		'filter' => array(
			'GLOBAL_ACTIVE' => 'Y', 
		),
		'enums' => array(
			'UF_ELEMENT_DETAIL',
			'UF_OFFERS_TYPE',
			'UF_GALLERY_SIZE',
			'UF_PICTURE_RATIO',
		),
	));
}

CAllcorp3::CheckComponentTemplatePageBlocksParams($arParams, __DIR__);

$arParams['OID'] = 0;
if ($oidParam = CAllcorp3::GetFrontParametrValue('CATALOG_OID')) {
	$context=\Bitrix\Main\Context::getCurrent();
	$request=$context->getRequest();
	if ($oid = $request->getQuery($oidParam)) {
		$arParams['OID'] = $oid;
	}
}

$sViewElementTemplate = \Aspro\Functions\CAsproAllcorp3::getValueWithSection([
	'CODE' => 'CATALOG_PAGE_DETAIL',
	'SECTION_VALUE' => $arInherite['UF_ELEMENT_DETAIL'],
	'CUSTOM_VALUE' => ($arParams['ELEMENT_TYPE_VIEW'] === 'FROM_MODULE' ? $arTheme['CATALOG_PAGE_DETAIL']['VALUE'] : $arParams['ELEMENT_TYPE_VIEW']),
]);
$typeSKU = \Aspro\Functions\CAsproAllcorp3::getValueWithSection([
	'CODE' => 'CATALOG_PAGE_DETAIL_SKU',
	'SECTION_VALUE' => $arInherite['UF_OFFERS_TYPE']
]);
$gallerySize = \Aspro\Functions\CAsproAllcorp3::getValueWithSection([
	'CODE' => 'CATALOG_PAGE_DETAIL_GALLERY_SIZE',
	'SECTION_VALUE' => $arInherite['UF_GALLERY_SIZE']
]);
$pictureRatioTmp = \Aspro\Functions\CAsproAllcorp3::getValueWithSection([
	'CODE' => 'CATALOG_PAGE_DETAIL_PICTURE_RATIO',
	'SECTION_VALUE' => $arInherite['UF_PICTURE_RATIO']
]);

$pictureRatio = $pictureRatioTmp ? $pictureRatioTmp : CAllcorp3::GetFrontParametrValue('ELEMENTS_IMG_TYPE');


// is need left block or sticky panel?
$APPLICATION->SetPageProperty('MENU', 'N');
$bWithStickyBlock = false;
if(strpos($sViewElementTemplate, 'element_1') !== false){
	$bShowLeftBlock = false;
	$bWithStickyBlock = true;
} else {
	$bShowLeftBlock = $arTheme['LEFT_BLOCK_CATALOG_DETAIL']['VALUE'] === 'Y';	
}
$bShowLeftBlock &= !defined('ERROR_404');
?>
<div class="main-wrapper flexbox flexbox--direction-row <?= $bShowLeftBlock || $bWithStickyBlock ? '' : 'catalog-maxwidth'?> <?=$pictureRatio !== "normal" ? "ratio--".$pictureRatio : ""?>">
	<div class="section-content-wrapper flex-1 <?=($bShowLeftBlock ? 'with-leftblock' : '')?>">
		<?CAllcorp3::AddMeta(
			array(
				'og:description' => $arElement['PREVIEW_TEXT'],
				'og:image' => (($arElement['PREVIEW_PICTURE'] || $arElement['DETAIL_PICTURE']) ? CFile::GetPath(($arElement['PREVIEW_PICTURE'] ? $arElement['PREVIEW_PICTURE'] : $arElement['DETAIL_PICTURE'])) : false),
			)
		);?>

		<?if($arParams['AJAX_MODE'] == 'Y' && strpos($_SERVER['REQUEST_URI'], 'bxajaxid') !== false):?>
			<script type="text/javascript">
				setStatusButton();
			</script>
		<?endif;?>

		<div class="product-container detail <?=$sViewElementTemplate;?> clearfix" itemscope itemtype="http://schema.org/Product">
			<?// share top?>
			<?if($arParams['USE_SHARE'] === 'Y' && $arElement):?>
				<?$this->SetViewTarget('cowl_buttons');?>
				<?Aspro\Functions\CAsproAllcorp3::showShareBlock(
					array(
						'CLASS' => 'top',
					)
				);?>
				<?$this->EndViewTarget();?>
			<?endif;?>

			<?@include_once('page_blocks/'.$sViewElementTemplate.'.php');?>
		</div>

		<div class="bottom-links-block">
			<?// back url?>
			<?Aspro\Functions\CAsproAllcorp3::showBackUrl(
				array(
					'URL' => ((isset($arSection) && $arSection) ? $arSection['SECTION_PAGE_URL'] : $arResult['FOLDER'].$arResult['URL_TEMPLATES']['news']),
					'TEXT' => ($arParams['T_PREV_LINK'] ? $arParams['T_PREV_LINK'] : GetMessage('BACK_LINK')),
				)
			);?>

			<?// share bottom?>
			<?if($arParams['USE_SHARE'] === 'Y' && $arElement):?>
				<?Aspro\Functions\CAsproAllcorp3::showShareBlock(
					array(
						'CLASS' => 'bottom',
					)
				);?>
			<?endif;?>
		</div>
	</div>
	<?if($bShowLeftBlock):?>
		<?CAllcorp3::ShowPageType('left_block');?>
	<?endif;?>
</div>