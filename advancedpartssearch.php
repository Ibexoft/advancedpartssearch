<?php   
if (!defined('_PS_VERSION_'))
  exit;

class AdvancedPartsSearch extends Module
{
	public function __construct()
	{
		$this->name = 'advancedpartssearch';	// Module name = folder name
		$this->tab = 'front_office_features';			// Module section
		$this->version = '1.0.0';
		$this->author = 'Muhammad Jawaid Shamshad | Ibexoft';
		$this->need_instance = 0;
		$this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_); 
		$this->bootstrap = true;

		parent::__construct();

		$this->displayName = $this->l('Advanced Parts Search');
		$this->description = $this->l('Advance Search and filter your parts the easy way.');

		$this->confirmUninstall = $this->l('Are you sure you want to uninstall?');

		if (!Configuration::get('MYMODULE_NAME'))      
		  $this->warning = $this->l('No name provided');
	}

	public function install()
	{
		// See if multistore is active and set context to all
		if (Shop::isFeatureActive())
			Shop::setContext(Shop::CONTEXT_ALL);

		return parent::install() &&
			// $this->registerHook('leftColumn') &&
			// $this->registerHook('home') &&
			$this->registerHook('top') &&
			$this->registerHook('header') &&
			Configuration::updateValue('MYMODULE_NAME', 'Ibex');
	}

	/*
	Display a config link in back office
	*/
	public function getContent()
	{
	    $output = null;
	 
	    if (Tools::isSubmit('submit'.$this->name))
	    {
	        $my_module_name = strval(Tools::getValue('MYMODULE_NAME'));
	        if (!$my_module_name
	          || empty($my_module_name)
	          || !Validate::isGenericName($my_module_name))
	            $output .= $this->displayError($this->l('Invalid Configuration value'));
	        else
	        {
	            Configuration::updateValue('MYMODULE_NAME', $my_module_name);
	            $output .= $this->displayConfirmation($this->l('Settings updated'));
	        }
	    }
	    return $output.$this->displayForm();
	}

	// display the configuration form called in getContent
	public function displayForm()
	{
	    // Get default language
	    $default_lang = (int)Configuration::get('PS_LANG_DEFAULT');
	     
	    // Init Fields form array
	    $fields_form[0]['form'] = array(
	        'legend' => array(
	            'title' => $this->l('Settings'),
	        ),
	        'input' => array(
	            array(
	                'type' => 'text',
	                'label' => $this->l('Configuration value'),
	                'name' => 'MYMODULE_NAME',
	                'size' => 20,
	                'required' => true
	            )
	        ),
	        'submit' => array(
	            'title' => $this->l('Save'),
	            'class' => 'button'
	        )
	    );
	     
	    $helper = new HelperForm();
	     
	    // Module, token and currentIndex
	    $helper->module = $this;
	    $helper->name_controller = $this->name;
	    $helper->token = Tools::getAdminTokenLite('AdminModules');
	    $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;
	     
	    // Language
	    $helper->default_form_language = $default_lang;
	    $helper->allow_employee_form_lang = $default_lang;
	     
	    // Title and toolbar
	    $helper->title = $this->displayName;
	    $helper->show_toolbar = true;        // false -> remove toolbar
	    $helper->toolbar_scroll = true;      // yes - > Toolbar is always visible on the top of the screen.
	    $helper->submit_action = 'submit'.$this->name;
	    $helper->toolbar_btn = array(
	        'save' =>
	        array(
	            'desc' => $this->l('Save'),
	            'href' => AdminController::$currentIndex.'&configure='.$this->name.'&save'.$this->name.
	            '&token='.Tools::getAdminTokenLite('AdminModules'),
	        ),
	        'back' => array(
	            'href' => AdminController::$currentIndex.'&token='.Tools::getAdminTokenLite('AdminModules'),
	            'desc' => $this->l('Back to list')
	        )
	    );
	     
	    // Load current value
	    $helper->fields_value['MYMODULE_NAME'] = Configuration::get('MYMODULE_NAME');
	     
	    return $helper->generateForm($fields_form);
	}

	public function hookDisplayLeftColumn($params)
	{
		$this->context->smarty->assign(
		  array(
		  	'base_url' => __PS_BASE_URI__,
		      'my_module_name' => Configuration::get('MYMODULE_NAME'),
		      'my_module_link' => $this->context->link->getModuleLink('advancedpartssearch', 'searchresults'),
		      'my_module_message' => $this->l('Your search results..') // Do not forget to enclose your strings in the l() translation method
		  )
		);
	
		return $this->display(__FILE__, 'advancedpartssearch.tpl');
	}

	// public function hookDisplayRightColumn($params)
	// {
	// 	return $this->hookDisplayLeftColumn($params);
	// }

	public function hookDisplayHome($params)
	{
		return $this->hookDisplayLeftColumn($params);
	}

	public function hookDisplayTop($params)
	{
		return $this->hookDisplayLeftColumn($params);
	}

	public function hookDisplayHeader()
	{
		$this->context->controller->addCSS($this->_path.'css/advancedpartssearch.css', 'all');
	} 
}