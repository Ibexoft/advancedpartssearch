<?php
class advancedpartssearchsearchresultsModuleFrontController extends ModuleFrontController
{
  public function initContent()
  {
    parent::initContent();
    $this->context->smarty->assign(
		  array(
		  		'search_results' => 'Yo mama'
		  		));
    $this->setTemplate('searchresults.tpl');
  }
}