<?php

// Generated e107 Plugin Admin Area 

require_once('../../../class2.php');
if (!getperms('P')) 
{
	e107::redirect('admin');
	exit;
}

require_once("admin_leftmenu.php");
 
            	
class web_links_ui extends e_admin_ui
{
			
		protected $pluginTitle		= '';
		protected $pluginName		= 'web_links';		
		protected $table			= 'links_categories';
		protected $pid				= 'cid';
		protected $perPage			= 10; 
		protected $batchDelete		= true;
		protected $batchExport     = true;
		protected $batchCopy		= true;
		protected $listOrder		= 'cid DESC';
	
		protected $fields 		= array (
			'checkboxes'              => array (  'title' => '',  'type' => null,  'data' => null,  'forced' => true,  'toggle' => 'e-multiselect',  'fieldpref' => '1',),
			'parentid'                => array (  'title' => _PARENT_CATEGORY,  'type' => 'dropdown',  'data' => 'int',  'batch' => true,  'fieldpref' => '1',),
			'cid'                     => array (  'title' => 'Cid',  'data' => 'int',  'fieldpref' => '1',),
			'title'                   => array (  'title' => LAN_TITLE,  'type' => 'text',  'data' => 'str',  'inline' => true,  'filter' => true,  'fieldpref' => '1',),
			'cdescription'            => array (  'title' => LAN_DESCRIPTION,  'type' => 'textarea',  'data' => 'str',  'filter' => true,  'fieldpref' => '1',),

			'options'                 => array (  'title' => LAN_OPTIONS,  'type' => null,  'data' => null,  'forced' => true,  'fieldpref' => '1',),
		);		
		
		protected $fieldpref = array('cid', 'title', 'cdescription', 'parentid');
		
	
		public function init()
		{
        	$this->postFiliterMarkup = $this->AddButton();
			// Example Drop-down array from database.
			$rows = e107::getDb()->retrieve("links_categories", "*", "WHERE parentid = 0 ", true);
			$values[0] = _TOPLEVEL;
			foreach($rows AS $row) 
			{
				$values[$row['cid']] = $row['title'];
			}
        	$this->fields['parentid']['writeParms']['optArray'] = $values ; 
		}

  
        function AddButton()
		{
			$mode = $this->getRequest()->getMode();	
	 
			$text .= "</fieldset></form><div class='e-container'>
			<table id='.$pid.' style='".ADMIN_WIDTH."' class='table adminlist table-striped'>";
			$text .=  
			'<a href="'.e_SELF.'?mode='.$mode.'&action=create"  
			class="btn batch e-hide-if-js btn-success"><span>'._ADD_CATEGORY.'</span></a>';
			$text .= "</td></tr></table></div><form><fieldset>";
			return $text;
	    }  
}  

class web_links_form_ui extends e_admin_form_ui
{

}		
		
		
new leftmenu_adminArea();

require_once(e_ADMIN."auth.php");
e107::getAdminUI()->runPage();
 

require_once(e_ADMIN."footer.php");
exit;
