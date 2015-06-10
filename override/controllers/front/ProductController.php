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
*  @author Muhammad Jawaid Shamshad <mjawaid@gmail.com>
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/
class ProductController extends ProductControllerCore
{
	/**
	 * Initialize product controller
	 * @see FrontController::init()
	 */
	public function init()
	{
		FrontController::init();

		if ($id_product = (int)Tools::getValue('id_product'))
			$this->product = new Product($id_product, true, $this->context->language->id, $this->context->shop->id);

		if (!Validate::isLoadedObject($this->product))
		{
			header('HTTP/1.1 404 Not Found');
			header('Status: 404 Not Found');
			$this->errors[] = Tools::displayError('Product not found');
		}
		else
		{
			$this->canonicalRedirection();
			/*
			 * If the product is associated to the shop
			 * and is active or not active but preview mode (need token + file_exists)
			 * allow showing the product
			 * In all the others cases => 404 "Product is no longer available"
			 */
			if (!$this->product->isAssociatedToShop() || !$this->product->active)
			{
				if (Tools::getValue('adtoken') == Tools::getAdminToken('AdminProducts'.(int)Tab::getIdFromClassName('AdminProducts').(int)Tools::getValue('id_employee')) && $this->product->isAssociatedToShop())
				{
					// If the product is not active, it's the admin preview mode
					$this->context->smarty->assign('adminActionDisplay', true);
				}
				else
				{
					$this->context->smarty->assign('adminActionDisplay', false);
					if (!$this->product->id_product_redirected || $this->product->id_product_redirected == $this->product->id)
						$this->product->redirect_type = '404';

					switch ($this->product->redirect_type)
					{
						case '301':
							header('HTTP/1.1 301 Moved Permanently');
							header('Location: '.$this->context->link->getProductLink($this->product->id_product_redirected));
							exit;
						break;
						case '302':
							header('HTTP/1.1 302 Moved Temporarily');
							header('Cache-Control: no-cache');
							header('Location: '.$this->context->link->getProductLink($this->product->id_product_redirected));
							exit;
						break;
						case '404':
						default:
							header('HTTP/1.1 404 Not Found');
							header('Status: 404 Not Found');
							$this->errors[] = Tools::displayError('This product is no longer available.');
						break;
					}
				}
			}
			elseif (!$this->product->checkAccess(isset($this->context->customer->id) && $this->context->customer->id ? (int)$this->context->customer->id : 0))
			{
				header('HTTP/1.1 403 Forbidden');
				header('Status: 403 Forbidden');
				$this->errors[] = Tools::displayError('You do not have access to this product.');
			}
			else
			{
				// Load category
				$id_category = false;
				if (isset($_SERVER['HTTP_REFERER']) && $_SERVER['HTTP_REFERER'] == Tools::secureReferrer($_SERVER['HTTP_REFERER']) // Assure us the previous page was one of the shop
					&& preg_match('~^.*(?<!\/content)\/([0-9]+)\-(.*[^\.])|(.*)id_(category|product)=([0-9]+)(.*)$~', $_SERVER['HTTP_REFERER'], $regs))
				{
					// If the previous page was a category and is a parent category of the product use this category as parent category
					$id_object = false;
					if (isset($regs[1]) && is_numeric($regs[1]))
						$id_object = (int)$regs[1];
					elseif (isset($regs[5]) && is_numeric($regs[5]))
						$id_object = (int)$regs[5];
					if ($id_object)
					{
						$referers = array($_SERVER['HTTP_REFERER'],urldecode($_SERVER['HTTP_REFERER']));
						if (in_array($this->context->link->getCategoryLink($id_object), $referers))
							$id_category = (int)$id_object;
						elseif (isset($this->context->cookie->last_visited_category) && (int)$this->context->cookie->last_visited_category && in_array($this->context->link->getProductLink($id_object), $referers))
							$id_category = (int)$this->context->cookie->last_visited_category;
					}
				}
				if (!$id_category || !Category::inShopStatic($id_category, $this->context->shop) || !Product::idIsOnCategoryId((int)$this->product->id, array('0' => array('id_category' => $id_category))))
					$id_category = (int)$this->product->id_category_default;
				$this->category = new Category((int)$id_category, (int)$this->context->cookie->id_lang);
				if (isset($this->context->cookie) && isset($this->category->id_category) && !(Module::isInstalled('blockcategories') && Module::isEnabled('blockcategories')))
					$this->context->cookie->last_visited_category = (int)$this->category->id_category;

				// Advanced Parts Search Module customization
				if($this->context->cookie->advancedsearch)
				{
					$brand = $this->context->cookie->brand_name;
					$series = $this->context->cookie->series_name;
					$model = $this->context->cookie->model_name;

					$this->product->name .= " compatible with $brand $series $model";

					$this->context->cookie->advancedsearch = false;
				}
			}
		}
	}
}