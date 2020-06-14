<?php

// Generated e107 Plugin Admin Area 

require_once('../../../class2.php');
if (!getperms('P')) 
{
	e107::redirect('admin');
	exit;
}
 
require_once("admin_leftmenu.php");
            	
class web_links_ui extends plugin_admin_ui
{
 
		protected $table			= UN_TABLENAME_LINKS_LINKS;
		protected $pid				= 'lid';
		protected $perPage			= 30; 
		protected $batchDelete		= true;
		protected $batchExport     = true;
		protected $batchCopy		= true;
		protected $listOrder		= 'lid DESC';
	    //   'date'  'hits' 'submitter'   'linkratingsummary'  'totalvotes'    'totalcomments'
		protected $fields 		= array (
			'checkboxes'              => array (  'title' => '',  'type' => null,  'data' => null,  'forced' => true,  'toggle' => 'e-multiselect' ),
			'lid'                     => array ( 'title' => LAN_ID, 'data' => 'int', 'type'=>'number', 'forced'=> TRUE, 'readonly'=>TRUE),
			'title'                   => array (  'title' => _PAGETITLE,  'type' => 'text',  'data' => 'str',  
            'writeParms' => 'size=block-level', 'filter' => true ),
			'url'                     => array (  'title' => _PAGEURL,  'type' => 'url',  'data' => 'str',   'readParms' => 'target=blank', 
            'writeParms' => 'size=block-level',),
            'cid'                     => array (  'title' => LAN_CATEGORY,  'type' => 'dropdown',  'data' => 'int', 'filter' => true,    'batch' => true,  'inline' => true ),
            'sid'                     => array (  'title' => _SUBCATEGORY,  'type' => 'dropdown',  'data' => 'int',  'batch' => true,  'inline' => true ),               
			'description'             => array (  'title' => _DESCRIPTION255,  'type' => 'textarea',  'data' => 'str',  
            'readParms' => 'expand=...&truncate=150&bb=1',  'writeParms' => 'size=block-level',),
			'name'                    => array (  'title' => LAN_NAME,  'type' => 'text',  'data' => 'str',  'inline' => true,   'nosort' => true,),
			'email'                   => array (  'title' => LAN_EMAIL,  'type' => 'email',  'data' => 'str',  'inline' => true,),  
            'date'                    => array (  'title' => LAN_DATE,  'type' => false,  'data' => 'str'   ),        
			'options'                 => array (  'title' => LAN_OPTIONS,  'type' => null,  'data' => null,  'forced' => true, ),
		);		
		
		protected $fieldpref = array('lid', 'cid', 'title', 'url', 'date', 'name');
 
        
		public function init()
		{	
 
			$rows = e107::getDb()->retrieve(UN_TABLENAME_LINKS_CATEGORIES, "*", "WHERE parentid = 0 ", true);
			$values[0] = '_NONE';
			foreach($rows AS $row) 
			{
				$values[$row['cid']] = $row['title'];
			}
			$this->fields['cid']['writeParms']['optArray'] = $values ;   
            
            $values = array();
            
            $rows = e107::getDb()->retrieve(UN_TABLENAME_LINKS_CATEGORIES, "*", "WHERE parentid != 0 ", true);
			$values[0] = '_NONE';
			foreach($rows AS $row) 
			{
				$values[$row['cid']] = $row['title'];
			}
        	$this->fields['sid']['writeParms']['optArray'] = $values ; 
		}
 
       
       public function beforeCreate($new_data)
	   {
 
         $sql = e107::getDb();
         //check if link exists
         $url = $new_data['url'];    
         $numrows = $sql->retrieve("SELECT COUNT(*) AS numrows FROM #".UN_TABLENAME_LINKS_LINKS." WHERE url='".addslashes($url)."'");
         if ($numrows > 0) {
            e107::getMessage()->addError(_ERRORURLEXISTWL);
            return false;
         }
         /* Check if Title exist */
         if ($new_data['title']=="") {
            e107::getMessage()->addError(_ERRORNOTITLEWL);
            return false;
         }
         /* Check if URL exist */
         if ($new_data['url']=="") {
            e107::getMessage()->addError(_ERRORNOURLWL);
            return false;
         }         
         /* Check if Description exist */
         if ($new_data['description']=="") {
            e107::getMessage()->addError(_ERRORNODESCRIPTIONWL);
            return false;
         }   
         
        
		$new_data['date'] = date("Y-m-d H:i:s");
		return $new_data;
              
       }
		public function beforeUpdate($new_data, $old_data, $id)
		{
		    $new_data['date'] = $old_data['date'];
            if($new_data['date'] == "0000-00-00 00:00:00" OR $new_data['date'] == '' ) {
               $new_data['date'] = date("Y-m-d H:i:s");
            }
 
            return $new_data;
 
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

