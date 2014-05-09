<?php 
namespace App\Models;
class Model
{
	var $regex = array('email' =>  "/[a-szA-Z0-9_-.+]+@[a-zA-Z0-9-]+.[a-zA-Z]+/",
			   'data' => '/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/');
	public $tableName;
	
	var $validations = array();
	var $validation_errors = array();
	
	public $translated_fields;
	public $primaryKey = 'id';
	
	public $force_translation = false;
	public $sql_esconder = '';
	
	function __construct() {
        	
        	$this->f3 = \Base::instance();
        	$this->db = new \App\Plugins\db();
        	// print_r($this->translated_fields);
        	// $this->translated_fields = array();
        	// print_r($this->translated_fields);
        }
        
        public function validate(){
        	
        	$valid = true;
        	$this->validation_errors = array();
        	$failed_validated_fields = array();
        	$failed_fields = array();
        	foreach($this->validations as $validations_list){
        		
        		foreach($validations_list as $field => $validations){
        			
        			foreach($validations as $type_v => $val_v){
        				$valid_field = true;
        				
        				if(isset($validations['name']))
        				{
        					$field_name = $validations['name'];
        				}else{
        					$field_name = $field;
        				}
        					
        				
        				if(isset($failed_validated_fields[$field]))
        				{
        					continue;
        				}
        				
        				if($type_v == 'required')
        				{
        					// print_r($this->f3->get('POST.'.$field));
        					// die(1);
        					if(strlen($val_v) > 1){
        						$message = $val_v;
        					}else{
        						$message = 'este campo &eacute; obrigat&oacute;rio';
        					}
        					if($this->f3->exists('POST.'.$field)){
        						$val = $this->f3->get('POST.'.$field);
        						if(empty($val))
        						{
        							$msg = $message;
        							$valid_field = false;
        						}
        					}else{
        						// echo $field." nao existe ";
        						$msg = $message;
        						$valid_field = false;
        					}
        				}
        				if($type_v == 'minlength')
        				{
        					
        					if($this->f3->exists('POST.'.$field)){
        						$val = $this->f3->get('POST.'.$field);
        						
        						if(strlen($val) < $val_v)
        						{
        							$msg = 'este campo tem de ter no m&iacute;nimo '.$val_v.' caracteres!';
        							$valid_field = false;
        						}
        					}else{
        						$msg = 'este campo &eacute; obrigat&oacute;rio';
        						$valid_field = false;
        					}
        				}
        				if($type_v == 'regex')
        				{
        					
        					$val = $this->f3->get('POST.'.$field);
        					
        					if($val_v == 'data' && !preg_match('/^([0-9]{4})-([0-9]{2})-([0-9]{2})$/', $val))
        					{
        						$msg = 'Campo inv&aacute;lido [Ex. "1980-10-05"]!';
        						$valid_field = false;
        					}
        				}
        				if($type_v == 'email'){
        					$val = $this->f3->get('POST.'.$field);
        					if(!filter_var($val, FILTER_VALIDATE_EMAIL))
        					{
        						$msg = 'Insira um e-mail v&aacute;lido!';
        						$valid_field = false;
        					}
        				}
        				if(!$valid_field)
        				{
        					$valid = false;
        					$failed_validated_fields[$field] = 1;
        					
        					$this->validation_errors[] = array('msg' => $msg,'field' => $field_name);
        					$failed_fields[$field] = $msg;
        					
        					
        				}
        				
        				// echo "$field ($type_v) $valid_field <br/> ";
        			}
        			
        			
        			
        		}
        	}
        	// print_r($this->validation_errors);
        	if(!empty($this->validation_errors)){
        		$this->f3->set('SESSION.validate.errors',$this->validation_errors);
        		$this->f3->set('SESSION.validate.fields',$failed_fields);
        	}
        	
        	// print_r($this->validation_errors);
        	return $valid;
        }
        
        function saveFile($inputname,$dst_folder = '')
	{
		if(empty($dst_folder)){
			$dst_folder = $this->f3->get('UPLOAD_DIR');
		}
		if(!empty($_FILES[$inputname]['name']))
		{
			// print_r($_FILES);
			// $nome_servidor = uniqid();
			$nome_servidor = $_FILES[$inputname]['name'];
			if(file_exists($dst_folder.$nome_servidor)){
				$nome_servidor = uniqid().$nome_servidor;
			}
			if(!move_uploaded_file($_FILES[$inputname]['tmp_name'], $dst_folder.$nome_servidor)){
				return false;
			}
			return $nome_servidor;
		}
		// return true;
		return false;
	}
	
	function getRows($conds = '',$select = '*',$showQuery = false){
		
		$rows = $this->db->getRows($this->tableName,$conds,$select,$showQuery);
		
		if(!empty($this->force_translation)){
			$language_set = $this->force_translation;
		}else{
			$language_set = $this->f3->get('lang_set');
		}
		// echo $language_set;
		// echo $this->f3->get('lang_set');
		if(is_array($this->translated_fields) && !empty($language_set)){
			
			foreach($rows as $key => $row){
				foreach($row as $_key => $_values){
					if(in_array($_key,$this->translated_fields)){
						// echo $_key."<br>";
						// $rows[$key][$_key] = 'traduzido!';
						
						$q = "SELECT field_value FROM i18n_translations 
						WHERE 
							table_name like '".$this->tableName."' AND
							field_id = ".$rows[$key][$this->primaryKey]." AND 
							field_name like '".$_key."' AND
							language like '".$language_set."'";
						$res = $this->db->exec($q);
						
						if(!empty($res[0]['field_value'])){
							$rows[$key][$_key] = $res[0]['field_value']."   ";
						}
						 
					}
				}
			}
		}
		
		return $rows;
		
	}
	
	public function getRow($conds = '',$select = '*',$showQuery = false){
		$res = $this->getRows($conds.' LIMIT 1',$select,$showQuery);
		if(isset($res[0]))
		{
			return $res[0];
		}
		return array();
	}
	
	public function edit(){
		$i18n = $this->f3->get('POST.i18n');
		if(strlen($i18n) > 1){
			
			foreach($this->translated_fields as $field){
				if($this->f3->exists('POST.'.$field)){
					$res = $this->db->getRow('i18n_translations',"WHERE 
							table_name like '".$this->tableName."' AND
				 			field_id like '".$this->f3->get('POST.'.$this->primaryKey)."' AND
				 			field_name like '".$field."' AND
				 			language like '".$this->f3->get('POST.i18n')."'");
				 	if(empty($res['id'])){
				 		
				 		$this->db->insert(array('table_name' => "'".$this->tableName."'",
				 			'field_id' => "'".$this->f3->get('POST.'.$this->primaryKey)."'",
				 			'field_name' => "'".$field."'",
				 			'field_value' => "'".$this->f3->get('POST.'.$field)."'",
				 			'language' => "'".$this->f3->get('POST.i18n')."'",
				 			),'i18n_translations');
				 		
				 	}else{
				 		$this->db->update('i18n_translations','id = '.$res['id'],array('field_value' => "'".$this->f3->get('POST.'.$field)."'"));
				 	}
				 	
				 	$this->f3->clear('POST.'.$field);
				}
			}
			// return true;
		}
		
		// print_r($this->f3->get('POST'));s
		// die(1);
		return $this->db->edit($this->f3->get('POST.id'),$this->tableName);
		
	}
	public function getValue($field,$conds, $showQuery = false){
		$res = $this->getRows($conds.' LIMIT 1',$field,$showQuery);
		if(isset($res[0][$field]))
		{
			return $res[0][$field];
		}
		return array();
	}
	
	public function delete($id){
		return $this->db->delete($id,$this->tableName);
	}
	
	
	public function save(){
		// echo "OIIIIIIIIIIIsII  ".$this->tableName;
		// die(1);
		if(!$this->beforeSave()){
			return false;
		}
		
		if(!$this->db->add($this->tableName)){
			return false;
		}
		if(!$this->afterSave()){
			return false;
		}
		
		// die(1);
		return true;
		
	}
	
	public function beforeSave(){
		return true;
		
	}
	
	public function afterSave()
	{
		return true;
	}
	
	public function down($id){
		
		
		$item = $this->db->getRow($this->tableName,"WHERE id = $id");
		$nova_ordem = $item['ordem']+1;
		
		$this->db->update($this->tableName,"ordem = ".$nova_ordem, array("ordem" => "ordem-1"));
		
		$this->db->update($this->tableName,"id = $id", array("ordem" => $nova_ordem));
		return true;
	}
	
	public function up($id){
		$item = $this->db->getRow($this->tableName,"WHERE id = $id");
		$nova_ordem = $item['ordem']-1;
		
		$this->db->update($this->tableName,"ordem = ".$nova_ordem, array("ordem" => "ordem+1"));
		
		$this->db->update($this->tableName,"id = $id", array("ordem" => $nova_ordem));
		return true;
	}
	
	public function all($conds = '',$select = '*',$showQuery = false){
		return $this->getRows($conds,$select,$showQuery);
	}
	
	public function paginate($page = 1, $page_views = 5, $order_field = 'created', $order_order = 'DESC',$conds = '',$pageCountSelect = 'id', $select = '*',$showQuery = false)
	{
		// return $this->db->allPaginateOrder($this->tableName, $conds, $page, $page_views, $order_field, $order_order, $select,$pageCountSelect,$showQuery );
		
		$order_by = "";
		$limit = "";
		$tableName = $this->tableName;
        	if(!empty($order_field))
        	{
        		if(empty($order_order))
        		{
        			$order_order = 'desc';
        		}
        		$order_by = "order by $order_field $order_order";
        	}
        	
        	if(empty($page_views))
        	{
        		$page_views = 5;
        	}
        	if(empty($page))
        	{
        		$page = 1;
        	}
        	$limit =  "LIMIT ".($page-1) * $page_views.",".$page_views;
        	
        	$sql = "select CEIL(count($pageCountSelect)/$page_views) as nr_paginas from $tableName $conds;";
        	if($showQuery){
        		echo $sql;
        		// die(1);
        	}
        	// echo $sql;
        	$total_pages = $this->db->exec($sql);
        	$total_pages = $total_pages[0]['nr_paginas'];
        	
        	$sql = "SELECT $select FROM $tableName $conds $order_by $limit";
        	if($showQuery){
        		echo $sql;
        		// die(1);
        	}
        	
        	// $results =  $this->db->exec($sql);
        	
        	$conds = "$conds $order_by $limit";
        	
        	$results =  $this->getRows($conds,$select);
		return array("query" => $sql, "page" => $page, "order_field" => $order_field, "order_order" => $order_order,"total_pages" => $total_pages, "pageview" => $page_views,"results" => $results);
	}
	
	public function getById($id){
		
		return $this->getRow("WHERE id = $id ".$this->sql_esconder);
	}
	
	public function update($where, $data,$showQuery = false){
		/*foreach($data as $field => $value){
			if(in_array($field,$this->translated_fields)){
				$res = $this->db->getRow('i18n_translations',"WHERE 
					table_name like '".$this->tableName."' AND
					field_id like '".$this->f3->get('POST.'.$this->primaryKey)."' AND
					field_name like '".$field."' AND
					language like '".$this->f3->get('POST.i18n')."'");
				if(empty($res['id'])){
					
					$this->db->insert(array('table_name' => "'".$this->tableName."'",
						'field_id' => "'".$this->f3->get('POST.'.$this->primaryKey)."'",
						'field_name' => "'".$field."'",
						'field_value' => "'".$this->f3->get('POST.'.$field)."'",
						'language' => "'".$this->f3->get('POST.i18n')."'",
						),'i18n_translations');
					
				}else{
					$this->db->update('i18n_translations','id = '.$res['id'],array('field_value' => "'".$this->f3->get('POST.'.$field)."'"));
				}
				die("THERE IS A FIELD TO TRANSLATE... update() ... model...");
			}
		}*/
		return $this->db->update($this->tableName,$where, $data,$showQuery);
	}
	
	public function getRowValue($fieldName,$conds = '',$showQuery=false) {
		
		$res = $this->getRow($conds,$fieldName,$showQuery);
		
		if(!empty($res[$fieldName]))
		{
			return $res[$fieldName];
		}else{
			return '';
		}
	}
	
	public function getCombined($value,$key = 'id',$conds = '',$select = '*',$showQuery = false){
		$res = $this->getRows($conds,$select,$showQuery);
		
		$resCombined = array();
		foreach($res as $result)
		{
			if(isset($result[$key]) && isset($result[$value])){
				$resCombined[$result[$key]] = $result[$value];
			}
		}
		return $resCombined;
	}
	// public function paginate(){
		// return $this->db->allPaginateOrder($this->tableName,"",$this->f3->get('PARAMS.p1'),10,'ordem','asc')
	// }
}
