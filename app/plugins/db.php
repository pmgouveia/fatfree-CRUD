<?php
namespace App\Plugins;

class db
{
	protected $db;
	protected $mapper;
	protected $tableName;
	function __construct()
	{
		$this->db = \Base::instance()->get('db');
	}
	
	public function setTable($tableName)
	{
		if(!empty($tableName))
		{
			$this->tableName = $tableName;
			$this->mapper = new \DB\SQL\Mapper($this->db,$tableName);
		}
	}
	public function all($tableName = '',$conds = '',$select = '*',$showQuery = false) {
		$q = "SELECT $select FROM $tableName $conds";
		// return $this->db->exec($q);
		return $this->getRows($tableName,$conds,$select,$showQuery);
	}
	
	public function add($tableName='') {
		$this->setTable($tableName);
		
		$this->mapper->copyFrom('POST');
		return $this->mapper->save();
	}
	
	public function getById($id,$tableName='') {
		$this->setTable($tableName);
		
		$this->mapper->load(array('id=?',$id));
		// $this->mapper->copyTo('POST');
		return $this->mapper;
	}
	
	public function getRow($tableName,$conds = '',$select = '*',$showQuery = false) {
		$res = $this->getRows($tableName,$conds.' LIMIT 1',$select,$showQuery);
		if(isset($res[0]))
		{
			return $res[0];
		}
		return array();
	}
	
	public function getRowValue($tableName, $fieldName,$conds = '',$showQuery=false) {
		
		$res = $this->getRow($tableName,$conds,$fieldName,$showQuery);
		// print_r($res);
		
		if(!empty($res[$fieldName]))
		{
			return $res[$fieldName];
		}else{
			return '';
		}
	}
	
	public function getRows($tableName,$conds = '',$select = '*',$showQuery = false) {
		$query = "SELECT $select FROM $tableName $conds";
		
		if($showQuery){echo $query;}
		$res = $this->db->exec($query);
		
		// if(\Base::instance()->exists('LANG')){
		// 	$res = $this->translatei18n(\Base::instance()->get('lang_set'),$tableName,$res);
		// }
		
		return $res;
	}
	
	public function getCombined($tableName,$select,$key,$value,$conds,$showQuery = false) {
		$q = "SELECT $select FROM $tableName $conds";
		
		if($showQuery)
		{
			echo $q;
			die(1);
		}
		$res = $this->db->exec($q);
		$resCombined = array();
		foreach($res as $result)
		{
			$resCombined[$result[$key]] = $result[$value];
		}
		return $resCombined;
	}
	
	public function getCombine($tableName,$key,$value,$conds,$showQuery = false) {
		$q = "SELECT $key,$value FROM $tableName $conds";
		
		if($showQuery)
		{
			echo $q;
			die(1);
		}
		$res = $this->db->exec($q);
		$resCombined = array();
		foreach($res as $result)
		{
			$resCombined[$result[$key]] = $result[$value];
		}
		return $resCombined;
		// $this->mapper->exec("SELECT * FROM $tableName $conds");
		// foreach($this->mapper as $kk)
		// 	{
		// 	}s
		// return $this->mapper;
	}
	
	public function getOne($q) {
		// $this->setTable($tableName);
		
		$query = $q;
		$res = $this->db->exec($query);
		return $res[0];
		// $this->mapper->copyTo('POST');
		// return $this->mapper;
	}
	
	public function edit($id,$tableName='') {
		$this->setTable($tableName);
		
		$this->mapper->load(array('id=?',$id));
		$this->mapper->copyFrom('POST');
		return $this->mapper->update();
	}
	
	public function delete($id,$tableName='') {
		$this->setTable($tableName);
		
		$this->mapper->load(array('id=?',$id));
		$this->mapper->erase();
	}
	
	public function insert($data,$tableName='',$showQuery = false) {
		
		$this->setTable($tableName);
		$fields = implode(",", array_keys($data));
		$values = implode(",", $data);
		
		$query = "INSERT INTO ".$tableName." (".$fields.") VALUES (".$values.")";
		
		if($showQuery){ echo $query; return;}
		
		return $this->db->exec($query);
		
	}
	
	public function lastId()
	{
		return $this->db->lastinsertid();
	}
	
	public function exec($query) {
		return $this->db->exec($query);
	}
	
	public function getRow_($query) {
		$res = $this->db->exec($query);
		if(!empty($res)) return $res[0];
		
		return array();
	}
	
	function update($table,$where, $data,$showQuery = false)
	{
		$set = array();
		foreach($data as $field => $value)
		{
			$set[] = "$field = $value";
		}
		$set_txt = implode($set,",");
		$query = "UPDATE $table SET $set_txt WHERE $where";
		
		if($showQuery) {echo $query;}
		
		return $this->db->exec($query);
	}
	
	public function combineT($array,$arrayIdxName,$arrayIdxVal = '') {
                $new_array = array();
                foreach($array as $key => $value)
                {
                        if(empty($arrayIdxVal))
                                $new_array[$array[$key][$arrayIdxName]] = $value;
                        else
                                $new_array[$array[$key][$arrayIdxName]] = $array[$key][$arrayIdxVal];
                }
                return $new_array;
        }
        
        // public function paginate($$pagina)
        // {
        	// $query = "select CEIL(count(id)/$page_views) as nr_paginas from forum_resposta WHERE forumTopicoId = $topicoId;";
        // }
        
        public function allPaginateOrder($tableName, $where, $page, $page_views, $order_field, $order_order, $select = '*',$pageCountSelect = 'id',$showQuery = false) 
        {
		$order_by = "";
		$limit = "";
		
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
        	
        	$sql = "select CEIL(count($pageCountSelect)/$page_views) as nr_paginas from $tableName $where;";
        	if($showQuery){
        		echo $sql;
        		// die(1);
        	}
        	$total_pages = $this->db->exec($sql);
        	$total_pages = $total_pages[0]['nr_paginas'];
        	
        	$sql = "SELECT $select FROM $tableName $where $order_by $limit";
        	if($showQuery){
        		echo $sql;
        		die(1);
        	}
		return array("query" => $sql, "page" => $page, "order_field" => $order_field, "order_order" => $order_order,"total_pages" => $total_pages, "pageview" => $page_views,"results" => $this->db->exec($sql));
	}
	
	function translatei18n($lang,$tbl,$results,$field_id = 'id'){
		
		/*// print_r($results);s
		$resulta = array();
		foreach($results as $key => $result)
		{
			echo $key;
			if(is_array($result)){
				foreach($result as $_key => $_result)
				{
					echo " > ".$_key." >   <br>";
					// $this->getRowValue('i18n_translations','field_value',"WHERE i18n_translations.table_name like '$tbl' AND i18n_translations.field_name like '$_key' AND i18n_translations.field_id = $field_id ");
					$q = "SELECT field_value FROM i18n_translations WHERE i18n_translations.table_name like '$tbl' AND i18n_translations.field_name like '$_key' AND i18n_translations.field_id = $field_id ";
					 $resulta = $this->db->exec($q);
					 echo $q;
					 echo "<br><br>";
					 // echo $q;
					 // if(!empty($resulta[]))
					print_r($resulta);
					// if(!empty($))
					// $results['']
				}
			}else{
				echo $resulta;
				// $resulta[] = $this->getRowValue('i18n_translations',$key,"WHERE i18n_translations.table_name like '$tbl' AND i18n_translations.field_name like '$key' AND i18n_translations.field_id = $field_id ");
			}
			
			
			
		}
		// print_r($resulta);
		die(1);*/
	}

}
