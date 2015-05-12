<?php
require_once(dirname(__FILE__).'../../../config/config.inc.php');
require_once(dirname(__FILE__).'../../../init.php');
switch (Tools::getValue('method')) {
  case 'getCategories' :
    echo Tools::jsonEncode( 
    	array(
	    	array('Id'=>'0' , 'Name'=>'-- Please select --'),
	    	array('Id'=>'1' , 'Name'=>'Laptop'),
	    	array('Id'=>'2' , 'Name'=>'Desktop') 
    	)
    	);
    break;
  case 'getBrands' :
  		//. Tools::getValue('category')
    echo Tools::jsonEncode( 
    	array(
	    	array('Id'=>'0' , 'Name'=>'-- Please select --'),
	    	array('Id'=>'1' , 'Name'=>'HP'),
	    	array('Id'=>'2' , 'Name'=>'Dell') 
    	)
    	);
    break;
  case 'getSeries' :
  		//. Tools::getValue('brand')
    echo Tools::jsonEncode( 
    	array(
	    	array('Id'=>'0' , 'Name'=>'-- Please select --'),
	    	array('Id'=>'1' , 'Name'=>'ProBook'),
	    	array('Id'=>'2' , 'Name'=>'Presario'),
	    	array('Id'=>'3' , 'Name'=>'Latitude'),
	    	array('Id'=>'4' , 'Name'=>'XPS'),
	    	array('Id'=>'5' , 'Name'=>'Inspiron') 
    	)
    	);
    break;
  case 'getModels' :
  		//. Tools::getValue('series')
    echo Tools::jsonEncode( 
    	array(
	    	array('Id'=>'0' , 'Name'=>'-- Please select --'),
	    	array('Id'=>'1' , 'Name'=>'4540s'),
	    	array('Id'=>'2' , 'Name'=>'D800') 
    	)
    	);
    break;
  default:
  	echo '-';
    exit;
}
exit;