<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>
<?if($arResult['USE_BASKET'] === 'Y'):?>
	<?
	$frame = $this->createFrame()->begin();
	$frame->setAnimation(true);
	?>
	<div class="basket default">
		<?if($arResult['ITEMS']):?>
			<input type="hidden" value="<?=$APPLICATION->GetCurUri();?>" name="getPageUri">
			<div class="basket_wrap">
				<div class="line-block line-block--align-normal basket__inner">
					<div class="line-block__item cart cart--default flex-grow-1">
						<?foreach($arResult['ITEMS'] as $arItem):?>
							<?
							$arItemButtons = CIBlock::GetPanelButtons($arItem['IBLOCK_ID'], $arItem['ID'], 0, array('SESSID' => false, 'CATALOG' => true));
							$this->AddEditAction($arItem['ID'], $arItemButtons['edit']['edit_element']['ACTION_URL'], CIBlock::GetArrayByID($arItem['IBLOCK_ID'], 'ELEMENT_EDIT'));
							$this->AddDeleteAction($arItem['ID'], $arItemButtons['edit']['delete_element']['ACTION_URL'], CIBlock::GetArrayByID($arItem['IBLOCK_ID'], 'ELEMENT_DELETE'), array('CONFIRM' => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
							
							$imageSrc = (is_array($arItem['PICTURE']) && strlen($arItem['PICTURE']['IMAGE_110']['src']) ? $arItem['PICTURE']['IMAGE_110']['src'] : SITE_TEMPLATE_PATH.'/images/svg/noimage_product.svg');
							$imageTitle = (is_array($arItem['PICTURE']) && strlen($arItem['PICTURE']['DESCRIPTION']) ? $arItem['PICTURE']['DESCRIPTION'] : $arItem['NAME']);
							$quantity = (isset($arItem['QUANTITY']) && $arItem['QUANTITY'] > 0 ? $arItem['QUANTITY'] : '');
							?>
							<div class="cart__item bordered rounded-4" id="<?=$this->GetEditAreaId($arItem['ID'])?>" data-item='{"ID":"<?=$arItem['ID']?>"}'>
								<div class="line-block line-block--align-normal cart__wrapper line-block--block-t600">
									<div class="line-block__item cart__image-wrapper">
										<div class="cart__item-image">
											<a href="<?=$arItem['DETAIL_PAGE_URL']?>">
												<img class="img-responsive" src="<?=$imageSrc;?>" alt="<?=$imageTitle;?>" title="<?=$imageTitle;?>" />
											</a>
										</div>
									</div>
									<div class="line-block__item flex-1 cart__info-wrapper">
										<div class="cart__item-info">
											<div class="cart__item-title">
												<a href="<?=$arItem['DETAIL_PAGE_URL']?>" class="dark_link"><?=$arItem['NAME']?></a>
											</div>
											<?
											$bSatusProp = (isset($arItem['PROPERTY_STATUS']) && strlen($arItem['PROPERTY_STATUS']['VALUE']));
											$bArticleProp = (isset($arItem['PROPERTY_ARTICLE_VALUE']) && strlen($arItem['PROPERTY_ARTICLE_VALUE']));
											?>
											<?if ($bSatusProp || $bArticleProp):?>
												<div class="cart__item-props">
													<div class="line-block line-block--20 flexbox--wrap">
														<?if($bSatusProp):?>
															<div class="line-block__item font_13">
																<span class="status-icon <?=$arItem['PROPERTY_STATUS']['XML_ID']?>">
																	<?=$arItem['PROPERTY_STATUS']['VALUE']?>
																</span>
															</div>
														<?endif;?>
														<?if($bArticleProp):?>
															<div class="line-block__item font_13 color_999">
																<span class="article">
																	<?=GetMessage('S_ARTICLE')?>:&nbsp;<span><?=$arItem['PROPERTY_ARTICLE_VALUE']?></span>
																</span>
															</div>
														<?endif;?>
													</div>
												</div>
											<?endif;?>
											<div class="cart__item-remove remove stroke-theme-parent-all print-hide" title="<?=GetMessage('T_BUTTON_REMOVE_ITEM')?>">
												<?=CAllcorp3::showIconSvg('remove stroke-theme-target ', SITE_TEMPLATE_PATH.'/images/svg/basket_remove.svg');?>
											</div>
										</div>
										<div class="cart__item-prices">
											<div class="line-block flexbox--justify-beetwen line-block--block-t600">
												<div class="line-block__item cart__item-cost">
													<?=\Aspro\Functions\CAsproAllcorp3::showPrice([
														'TYPE' => 'basket_fly',
														'ITEM' => $arItem,
														'PARAMS' => $arParams,
														'SHOW_SCHEMA' => false,
														'TO_LINE' => true,
														'PRICE_FONT' => 16,
														'PRICEOLD_FONT' => 12,
														'PRICES' => [
															'PRICE' => $arItem['PROPERTY_PRICE_VALUE'],
															'PRICE_OLD' => $arItem['PROPERTY_PRICEOLD_VALUE'],
															'PRICE_CURRENCY' => $arItem['PROPERTY_PRICE_CURRENCY_VALUE'],
														]
													]);?>
												</div>
												<div class="line-block__item cart__item-counter buy_block">
													<div class="counter counter--basket bordered rounded-4">
														<span class="counter__action counter__action--minus print-hide"></span>
														<div class="counter__count-wrapper">
															<input type="text" value="<?=$quantity;?>" class="counter__count" maxlength="6">
														</div>
														<span class="counter__action counter__action--plus print-hide"></span>
													</div>
													<input type="hidden" name="PRICE" value="<?=$arItem['PROPERTY_FILTER_PRICE_VALUE']?>" />
												</div>
												<?if(isset($arItem['SUMM']) && $arItem['SUMM'] > 0):?>
													<div class="line-block__item cart__item-summ">
														<div class="price_new form_16 color_333 font_bold">
															<span class="price_val"><?=$arItem['SUMM'];?></span>
														</div>
													</div>
												<?endif;?>
											</div>
										</div>
									</div>
								</div>
							</div>
						<?endforeach;?>
					</div>
					<div class="line-block__item basket-side">
						<div class="sticky-block">
							<div class="basket-side__wrapper rounded-4 basket-side__wrapper--padding">
								<div class="foot">
									<div class="line-block flexbox--justify-beetwen cart-total font_18 font_bold color_333">
										<div class="line-block__item cart-total__text">
											<?=GetMessage('T_BASKET_TOTAL_TITLE');?>
										</div>
										<div class="line-block__item cart-total__value">
											<?=$arResult['ITEMS_SUMM']?>
										</div>
									</div>
								</div>
								<div class="buttons basket--column-btn">
									<div class="line-block line-block--block line-block--16">
										<div class="line-block__item flex-1">
											<a class="btn btn-default btn-lg btn-wide to-order" href="<?=$arParams['PATH_TO_ORDER']?>">
												<?=GetMessage('T_BASKET_BUTTON_ORDER');?>
											</a>
										</div>
										<?if ($arParams['SHOW_BASKET_PRINT'] != 'N'):?>
											<div class="line-block__item flex-1">
												<span class="btn btn-default btn-lg btn-wide print btn-transparent">
													<span>
														<?=CAllcorp3::showIconSvg('basket-print fill-theme ', SITE_TEMPLATE_PATH.'/images/svg/Print.svg');?>
														<?=GetMessage('T_BASKET_BUTTON_PRINT');?>
													</span>
												</span>
											</div>
										<?endif;?>
									</div>
								</div>
							</div>
							<div class="basket-side__remove text-center print-hide">
								<span class="remove all color_999 font_13 color-theme-hover basket__heading-remove" data-remove_all="Y"><span><?=GetMessage('T_BUTTON_REMOVE_ALL');?></span></span>
							</div>
						</div>
					</div>
				</div>
			</div>
		<?endif;?>
		<div class="basket_empty"<?=($arResult['ITEMS'] ? ' style="display:none;"' : '')?>>
			<div class="wrap">
				<?=CAllcorp3::showIconSvg("basket", SITE_TEMPLATE_PATH."/images/svg/basket.svg");?>
				<h4 class="font_18"><?=GetMessage('T_BASKET_EMPTY_TITLE');?></h4>
				<div class="description color_666"><?=GetMessage('T_BASKET_EMPTY_DESCRIPTION');?></div>
				<div class="button">
					<a class="btn btn-default btn-transparent-border" href="<?=$arParams['PATH_TO_CATALOG'];?>">
						<?=GetMessage('T_BASKET_BUTTON_CATALOG');?>
					</a>
				</div>
			</div>
		</div>
	</div>
	<?$frame->end();?>
<?endif;?>
<?/*  Блок рекомендуемых товаров */?>
<?

foreach($arResult['ITEMS'] as $arItem) {
	$id_of_products_in_basket[] = $arItem['ID'];
}

$id_search = $id_of_products_in_basket[array_rand($id_of_products_in_basket)];
?>

<?
	$APPLICATION->IncludeComponent(
	"bitrix:catalog.recommended.products", 
	".default", 
	array(
		"ACTION_VARIABLE" => "action_crp",
		"ADD_PROPERTIES_TO_BASKET" => "Y",
		"BASKET_URL" => "/personal/basket.php",
		"CACHE_TIME" => "86400",
		"CACHE_TYPE" => "A",
		"CODE" => $_REQUEST["PRODUCT_CODE"],
		"COMPONENT_TEMPLATE" => ".default",
		"CONVERT_CURRENCY" => "N",
		"DETAIL_URL" => "",
		"ELEMENT_SORT_FIELD" => "SORT",
		"ELEMENT_SORT_FIELD2" => "ID",
		"ELEMENT_SORT_ORDER" => "ASC",
		"ELEMENT_SORT_ORDER2" => "DESC",
		"HIDE_NOT_AVAILABLE" => "N",
		"IBLOCK_ID" => "54",
		"IBLOCK_TYPE" => "aspro_allcorp3_catalog",
		"ID" => $id_search,
		"LINE_ELEMENT_COUNT" => "6",
		"MESS_BTN_BUY" => "Купить",
		"MESS_BTN_DETAIL" => "Подробнее",
		"MESS_BTN_SUBSCRIBE" => "Подписаться",
		"MESS_NOT_AVAILABLE" => "Нет в наличии",
		"OFFERS_PROPERTY_LINK" => "LINK_GOODS",
		"PAGE_ELEMENT_COUNT" => "10",
		"PARTIAL_PRODUCT_PROPERTIES" => "N",
		"PRICE_CODE" => array(
		),
		"PRICE_VAT_INCLUDE" => "N",
		"PRODUCT_DISPLAY_MODE" => "Y",
		"PRODUCT_ID_VARIABLE" => "id",
		"PRODUCT_PROPS_VARIABLE" => "prop",
		"PRODUCT_QUANTITY_VARIABLE" => "quantity",
		"PRODUCT_SUBSCRIPTION" => "N",
		"PROPERTY_LINK" => "LINK_GOODS",
		"SHOW_DISCOUNT_PERCENT" => "N",
		"SHOW_IMAGE" => "Y",
		"SHOW_NAME" => "Y",
		"SHOW_OLD_PRICE" => "Y",
		"SHOW_PRICE_COUNT" => "1",
		"TEMPLATE_THEME" => "site",
		"USE_PRODUCT_QUANTITY" => "N"
	),
	false
);?>

<?/* end Блок рекомендуемых товаров */?>


<?/* Блок просмотренных товаров */?>

				<?$APPLICATION->IncludeComponent(
	"bitrix:catalog.products.viewed", 
	".default_seonik", 
	array(
		"ACTION_VARIABLE" => "action_cpv",
		"ADD_PROPERTIES_TO_BASKET" => "Y",
		"ADD_TO_BASKET_ACTION" => "ADD",
		"BASKET_URL" => "/personal/basket.php",
		"CACHE_GROUPS" => "Y",
		"CACHE_TIME" => "3600",
		"CACHE_TYPE" => "A",
		"CONVERT_CURRENCY" => "N",
		"DEPTH" => "2",
		"DISCOUNT_PERCENT_POSITION" => "bottom-left",
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
		"PRICE_CODE" => array(
		),
		"PRICE_VAT_INCLUDE" => "Y",
		"PRODUCT_BLOCKS_ORDER" => "price,props,sku,quantityLimit,quantity,buttons",
		"PRODUCT_ID_VARIABLE" => "id",
		"PRODUCT_PROPS_VARIABLE" => "prop",
		"PRODUCT_QUANTITY_VARIABLE" => "quantity",
		"PRODUCT_ROW_VARIANTS" => "[{'VARIANT':'6','BIG_DATA':false}]",
		"PRODUCT_SUBSCRIPTION" => "Y",
		"SECTION_CODE" => "",
		"SECTION_ELEMENT_CODE" => "",
		"SECTION_ELEMENT_ID" => $GLOBALS["CATALOG_CURRENT_ELEMENT_ID"],
		"SECTION_ID" => $GLOBALS["CATALOG_CURRENT_SECTION_ID"],
		"SHOW_CLOSE_POPUP" => "N",
		"SHOW_DISCOUNT_PERCENT" => "Y",
		"SHOW_FROM_SECTION" => "N",
		"SHOW_MAX_QUANTITY" => "N",
		"SHOW_OLD_PRICE" => "N",
		"SHOW_PRICE_COUNT" => "1",
		"SHOW_SLIDER" => "Y",
		"SLIDER_INTERVAL" => "3000",
		"SLIDER_PROGRESS" => "N",
		"TEMPLATE_THEME" => "",
		"USE_ENHANCED_ECOMMERCE" => "N",
		"USE_PRICE_COUNT" => "N",
		"USE_PRODUCT_QUANTITY" => "N",
		"COMPONENT_TEMPLATE" => ".default_seonik"
	),
	false
);?>

<?/* end Блок просмотренных товаров */?>
