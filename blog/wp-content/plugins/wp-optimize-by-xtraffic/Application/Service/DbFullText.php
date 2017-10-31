<?php 
namespace WPOptimizeByxTraffic\Application\Service;

use WPOptimizeByxTraffic\Application\Service\WpExtend
;

class DbFullText
{
	
	public function isTableSupportFullText($tableName) 
	{
		global $wpdb;
		
		$status = false;
		
		$rsOne = $wpdb->get_results('SHOW TABLE STATUS LIKE "'.$tableName.'"');
		
		if($rsOne) {
			
			foreach($rsOne as $valueOne) {
			
				if($valueOne) {
				
					if(isset($valueOne->Engine)) {
						
						$engine1 = $valueOne->Engine;
						$engine1 = (string)$engine1;
						$engine1 = trim($engine1);
						$engine1 = strtolower($engine1);
						
						if ($engine1 === 'myisam') {
							
							$status = true;
							
						} else if ($engine1 === 'innodb') {
							
							$mysqlVersion = WpExtend::getMySQLVersion();
							
							if(false !== $mysqlVersion) {
								if(version_compare($mysqlVersion, '5.6', '>=')) {
									$status = true;
								}
							}
						}
					}
				}
			}
		}
		
		return $status;
	}
	
	
	/*
		$tableName = 'wp_posts'
		
		$fields = array(
			'post_title'
			,'post_excerpt'
			,'post_name'
		)
		
		$indexType = 'FULLTEXT'
	*/
	public function addIndexToTableFields($tableName, $fields, $indexType, $maxIndexLength = 0) 
	{
		global $wpdb;
		
		$resultData = array();
		
		$fields = (array)$fields;
		
		$indexTypeLowercase = strtolower($indexType);
		
		$getIndexTypeOfTableFields = $this->getIndexTypeOfTableFields($tableName);
		
		$fieldsNeedAddIndex = array();
		
		foreach($fields as $field) {
			if(isset($getIndexTypeOfTableFields[$field][$indexTypeLowercase])) {
				$resultData[$field] = true;
			} else {
				$fieldsNeedAddIndex[] = $field;
			}
		}
		
		if(!empty($fieldsNeedAddIndex)) {
			$fieldsNeedAddIndex = array_unique($fieldsNeedAddIndex);
			
			if('fulltext' === $indexTypeLowercase) {
				$isTableSupportFullText = $this->isTableSupportFullText($tableName);
				
				if($isTableSupportFullText) {
					
					foreach($fieldsNeedAddIndex as $field) {
						$wpdb->get_results(' ALTER TABLE `'.$tableName.'` ADD FULLTEXT `wppepvn_'.$field.'_ftind` (`'.$field.'`) ');
						$resultData[$field] = true;
					}
					
				}
			} else if('btree' === $indexTypeLowercase) {
				foreach($fieldsNeedAddIndex as $field) {
					$wpdb->get_results(' CREATE INDEX `wppepvn_'.$field.'_btind` ON `'.$tableName.'` ( `'.$field.'`'.($maxIndexLength>0 ? '('.(int)$maxIndexLength.')' : '').')');
					$resultData[$field] = true;
				}
			}
		}
		
		return $resultData;
	}
	
	
	/*
	*	$tableName = 'wp_posts'
	*/
	public function getIndexTypeOfTableFields($tableName) 
	{
		global $wpdb;
		
		$resultData = array();
		
		$rsOne = $wpdb->get_results('SHOW INDEXES FROM `'.$tableName.'`');
		
		if($rsOne) {
		
			foreach($rsOne as $valueOne) {
				
				if($valueOne) {
				
					if(isset($valueOne->Column_name)) {
						
						$colName = $valueOne->Column_name;
						$colName = (string)$colName;
						$colName = trim($colName);
						
						$indexType = $valueOne->Index_type;
						$indexType = (string)$indexType;
						$indexType = trim($indexType);
						$indexType = strtolower($indexType);
						
						$resultData[$colName][$indexType] = $indexType;
						
					}
				}
			}
		}
		
		return $resultData;
	}
	
}