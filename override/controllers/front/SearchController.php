<?php

/*
* 2007-2015 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class SearchController extends SearchControllerCore
{
	/**
	 * Assign template vars related to page content
	 * @see FrontController::initContent()
	 */
	public function initContent()
	{
		$query = Tools::replaceAccentedChars(urldecode(Tools::getValue('q')));
		$original_query = Tools::getValue('q');
		if ($this->ajax_search)
		{
			$searchResults = Search::find((int)(Tools::getValue('id_lang')), $query, 1, 10, 'position', 'desc', true);
			if (is_array($searchResults))
				foreach ($searchResults as &$product)
					$product['product_link'] = $this->context->link->getProductLink($product['id_product'], $product['prewrite'], $product['crewrite']);

			$this->ajaxDie(Tools::jsonEncode($searchResults));
		}

		//Only controller content initialization when the user use the normal search
		FrontController::initContent();

		if ($this->instant_search && !is_array($query))
		{
			$this->productSort();
			$this->n = abs((int)(Tools::getValue('n', Configuration::get('PS_PRODUCTS_PER_PAGE'))));
			$this->p = abs((int)(Tools::getValue('p', 1)));
			$search = Search::find($this->context->language->id, $query, 1, 10, 'position', 'desc');
			Hook::exec('actionSearch', array('expr' => $query, 'total' => $search['total']));
			$nbProducts = $search['total'];
			$this->pagination($nbProducts);

			$this->addColorsToProductList($search['result']);

			$this->context->smarty->assign(array(
				'products' => $search['result'], // DEPRECATED (since to 1.4), not use this: conflict with block_cart module
				'search_products' => $search['result'],
				'nbProducts' => $search['total'],
				'search_query' => $original_query,
				'instant_search' => $this->instant_search,
				'homeSize' => Image::getSize(ImageType::getFormatedName('home'))));
		}
		elseif (($query = Tools::getValue('search_query', Tools::getValue('ref'))) && !is_array($query))
		{
			$this->productSort();
			$this->n = abs((int)(Tools::getValue('n', Configuration::get('PS_PRODUCTS_PER_PAGE'))));
			$this->p = abs((int)(Tools::getValue('p', 1)));
			$original_query = $query;
			$query = Tools::replaceAccentedChars(urldecode($query));
			$search = Search::find($this->context->language->id, $query, $this->p, $this->n, $this->orderBy, $this->orderWay);
			if (is_array($search['result']))
				foreach ($search['result'] as &$product)
					$product['link'] .= (strpos($product['link'], '?') === false ? '?' : '&').'search_query='.urlencode($query).'&results='.(int)$search['total'];

			Hook::exec('actionSearch', array('expr' => $query, 'total' => $search['total']));
			$nbProducts = $search['total'];
			$this->pagination($nbProducts);

			$this->addColorsToProductList($search['result']);

			$this->context->smarty->assign(array(
				'products' => $search['result'], // DEPRECATED (since to 1.4), not use this: conflict with block_cart module
				'search_products' => $search['result'],
				'nbProducts' => $search['total'],
				'search_query' => $original_query,
				'homeSize' => Image::getSize(ImageType::getFormatedName('home'))));
		}
		elseif (($tag = urldecode(Tools::getValue('tag'))) && !is_array($tag))
		{
			$nbProducts = (int)(Search::searchTag($this->context->language->id, $tag, true));
			$this->pagination($nbProducts);
			$result = Search::searchTag($this->context->language->id, $tag, false, $this->p, $this->n, $this->orderBy, $this->orderWay);
			Hook::exec('actionSearch', array('expr' => $tag, 'total' => count($result)));

			$this->addColorsToProductList($result);

			$this->context->smarty->assign(array(
				'search_tag' => $tag,
				'products' => $result, // DEPRECATED (since to 1.4), not use this: conflict with block_cart module
				'search_products' => $result,
				'nbProducts' => $nbProducts,
				'homeSize' => Image::getSize(ImageType::getFormatedName('home'))));
		}
		elseif ($category_query = Tools::getValue('search_category'))
		{
			$this->productSort();
			$this->n = abs((int)(Tools::getValue('n', Configuration::get('PS_PRODUCTS_PER_PAGE'))));
			$this->p = abs((int)(Tools::getValue('p', 1)));

			$brand_query = Tools::getValue('search_brand');
			$series_query = Tools::getValue('search_series');
			$model_query = Tools::getValue('search_model');
			
			$query = array(
					'id_category' => $category_query, 
					'id_brand' => $brand_query, 
					'id_series' => $series_query, 
					'id_model' => $model_query);
			
			$cat = new Category($category_query);
			$brand = new FeatureValue($brand_query);
			$series = new FeatureValue($series_query);
			$model = new FeatureValue($model_query);

			// Save data for later use to keep search criteria intact
			$this->context->cookie->advancedsearch = true;
			$this->context->cookie->id_category = $category_query;
			$this->context->cookie->category_name = $cat->getName();
			$this->context->cookie->id_brand = $brand_query;
			$this->context->cookie->brand_name = $brand->getFieldByLang('value');
			$this->context->cookie->id_series = $series_query;
			$this->context->cookie->series_name = $series->getFieldByLang('value');
			$this->context->cookie->id_model = $model_query;
			$this->context->cookie->model_name = $model->getFieldByLang('value');

			$original_query = $brand->getFieldByLang('value') . " " . $series->getFieldByLang('value') . " " . $model->getFieldByLang('value') . " in " . $cat->getName(); 

			// $query = Tools::replaceAccentedChars(urldecode($query));
			$search = Search::find($this->context->language->id, $query, $this->p, $this->n, $this->orderBy, $this->orderWay, false, true, null, true);
			$query = $original_query;
			
			if (is_array($search['result']))
				foreach ($search['result'] as &$product)
					$product['link'] .= (strpos($product['link'], '?') === false ? '?' : '&').'search_query='.urlencode($query).'&results='.(int)$search['total'];
			Hook::exec('actionSearch', array('expr' => $query, 'total' => $search['total']));
			$nbProducts = $search['total'];
			$this->pagination($nbProducts);
			$this->addColorsToProductList($search['result']);

			$this->context->smarty->assign(array(
				'products' => $search['result'], // DEPRECATED (since to 1.4), not use this: conflict with block_cart module
				'search_products' => $search['result'],
				'nbProducts' => $search['total'],
				'search_query' => $original_query,
				'homeSize' => Image::getSize(ImageType::getFormatedName('home'))));
		}
		else
		{
			$this->context->smarty->assign(array(
				'products' => array(),
				'search_products' => array(),
				'pages_nb' => 1,
				'nbProducts' => 0));
		}
		$this->context->smarty->assign(array('add_prod_display' => Configuration::get('PS_ATTRIBUTE_CATEGORY_DISPLAY'), 'comparator_max_item' => Configuration::get('PS_COMPARATOR_MAX_ITEM')));
		$this->setTemplate(_PS_THEME_DIR_.'search.tpl');
	}
}