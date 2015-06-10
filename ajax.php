<?php
require_once(dirname(__FILE__).'../../../config/config.inc.php');
require_once(dirname(__FILE__).'../../../init.php');

switch (Tools::getValue('method')) {
  case 'getCategories' :
    $context = Context::getContext();
    $cats = getCategories($context->language->id);
    echo Tools::jsonEncode($cats);
    break;

  case 'getBrands' :
  	$id_category = Tools::getValue('category');
    $context = Context::getContext();
    $brands = getBrands($context->language->id, $id_category);
    echo Tools::jsonEncode($brands);
    break;

  case 'getSeries' :
    $id_category = Tools::getValue('category');
    $context = Context::getContext();
    $series = getSeries($context->language->id, $id_category);
    echo Tools::jsonEncode($series);
    break;

  case 'getModels' :
    $id_category = Tools::getValue('category');
    $context = Context::getContext();
    $models = getModels($context->language->id, $id_category);
    echo Tools::jsonEncode($models);
    break;

  default:
  	echo Tools::jsonEncode(['Error' => 'No Such Method']);;
    exit;
}

/* Helper functions */

function getCategories($id_lang)
{
  return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
  SELECT c.`id_category` id, cl.`name`
  FROM `'._DB_PREFIX_.'category` c
  LEFT JOIN `'._DB_PREFIX_.'category_lang` cl ON (c.`id_category` = cl.`id_category`'.Shop::addSqlRestrictionOnLang('cl').')
  '.Shop::addSqlAssociation('category', 'c').'
  WHERE cl.`id_lang` = '.(int)$id_lang.'
  AND c.`id_category` != '.Configuration::get('PS_ROOT_CATEGORY').'
  GROUP BY c.id_category
  ORDER BY c.`id_category`, category_shop.`position`');
}

function getBrands($id_lang, $id_category)
{
  /// TODO: fetch id_feature from configuration for brand
  
  $id_feature = Configuration::get('ID_FEATURE_BRAND');
  if (!$id_feature) $id_feature = 10; // TODO: remove this

  return getFeatures($id_lang, $id_category, $id_feature);
}

function getSeries($id_lang, $id_category)
{
  /// TODO: fetch id_feature from configuration for brand
  
  $id_feature = Configuration::get('ID_FEATURE_SERIES');
  if (!$id_feature) $id_feature = 8; /// TODO: remove this

  return getFeatures($id_lang, $id_category, $id_feature);
}

function getModels($id_lang, $id_category)
{
  /// TODO: fetch id_feature from configuration for brand
  
  $id_feature = Configuration::get('ID_FEATURE_MODEL');
  if (!$id_feature) $id_feature = 9; /// TODO: remove this

  return getFeatures($id_lang, $id_category, $id_feature);
}

function getFeatures($id_lang, $id_category, $id_feature)
{
  return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
    SELECT id_feature_value id, value name FROM '._DB_PREFIX_.'feature_value_lang WHERE id_feature_value IN (
      SELECT id_feature_value FROM '._DB_PREFIX_.'feature_product WHERE id_feature = '.(int)$id_feature.' AND id_product IN (
        SELECT id_product FROM '._DB_PREFIX_.'category_product WHERE id_category = '.(int)$id_category.'
        )
      )');
}

exit;