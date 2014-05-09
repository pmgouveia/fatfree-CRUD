<?php
namespace App\Plugins;

class html{
	static function link($href,$content = '',$array_attrs = array() )
	{
		$attrs = '';
		
		foreach($array_attrs as $key => $val)
		{
			$attrs .= " $key='$val'";
		}
		
		$uri = \Base::instance()->get('BASE');
		$lang = \Base::instance()->get('lang_set');
		
		if(!empty($lang)){
			$lang_href = "$lang/";
		}else{
			$lang_href = '';
		}
		
		if(strpos($href,"http://") !== false)
		{
			$uri = "";
			$attrs .= "target='_blank'";
		}
		if(strpos($href,"/") === 0)
		{
			$href = substr($href,1);
		}
		echo "<a href='".$uri."/$lang_href".$href."' $attrs>$content</a>";
	}
	static function getLink($href)
	{
		$uri = \Base::instance()->get('BASE');
		$lang = \Base::instance()->get('lang_set');
		
		if(!empty($lang)){
			$lang_href = "/$lang";
		}else{
			$lang_href = '';
		}
		echo $uri."$lang_href"."$href";
	}
	static function email($name,$array_attrs )
	{
		html::input($name,"email",$array_attrs);
	}
	static function text($name,$array_attrs)
	{
		html::input($name,"text",$array_attrs);
	}
	static function password($name,$array_attrs)
	{
		html::input($name,"password",$array_attrs);
	}
	static function hidden($name,$value = '')
	{
		if(empty($value) && \Base::instance()->exists('POST.'.$name))
		{
			$value = \Base::instance()->get('POST.'.$name);
		}
		echo "<input type='hidden' name='$name' value='$value'>";
	}
	static function file($name,$array_attrs = array())
	{
		$attrs = '';
		$label = '';
		
		foreach($array_attrs as $key => $val)
		{
			if($key == "class"){
				$attrs .= " $key='$val form-control'";
			}else if($key == "label"){
				$label = "<label>$val: </label>";
			}else{
				$attrs .= " $key='$val'";
			}
			
		}
		echo $label;
		
		echo "<input type='file' name='$name' $attrs>";
	}
	
	static function combo($name,$values,$array_attrs = array() )
	{
		$attrs = '';
		$label = '';
		$selected = '';
		
		foreach($array_attrs as $key => $val)
		{
			if($key == "class"){
				$attrs .= " $key='$val form-control'";
			}else if($key == "label"){
				$label = "<label>$val: </label>";
			}elseif($key == "selected"){
				$selected = $val;
			}else{
				$attrs .= " $key='$val'";
			}
			
		}
		echo $label;
		echo "<select name='$name' $attrs>";
		echo "<option></option>";
		foreach($values as $k => $val)
		{
			$sel = '';
			if($k == $selected)
			{
				$sel = 'selected';
			}
			echo "<option value='$k' $sel>$val</option>";
		}
		echo "</select>";
	}
	
	static function input($name,$type,$array_attrs )
	{
		$attrs = '';
		$label = '';
		$validation = '';
		if($type == 'radio'){
			if(\Base::instance()->exists('SESSION.validate.fields.'.$name)){
				if(!isset($array_attrs['class'])){
					$array_attrs['class'] = "";
				}
				$array_attrs['class'] .= " validation-error";
				
				if(isset($array_attrs['validate_class'])){
					$clas = $array_attrs['validate_class'];
					$validation = "<script>$(document).ready(function(){
					$('$clas').addClass('validation-error');});</script>";
				}
			}
		}else{
			if(\Base::instance()->exists('SESSION.validate.fields.'.$name)){
				if(!isset($array_attrs['class'])){
					$array_attrs['class'] = "";
				}
				$array_attrs['class'] .= " validation-error";
				
				$validation = "<label class='field-validation-error'>".\Base::instance()->get('SESSION.validate.fields.'.$name)."</label>";
			}
		}
		foreach($array_attrs as $key => $val)
		{
			if($key == "class"){
				$attrs .= " $key='$val form-control'";
			}else if($key == "label"){
				$label = "<label>$val: </label>";
			}else{
				if(!empty($val)){
					$attrs .= " $key='$val'";
				}else{
					$attrs .= " $key ";
				}
			}
			
		}
		
		
		
		if($type == 'radio'){
			
			if(\Base::instance()->exists('POST.'.$name))
			{
				$value = \Base::instance()->get('POST.'.$name);
				if($array_attrs['value'] == $value){
					$attrs .= " checked ";
				}
			}
		}else{
			if(!isset($array_attrs['value']) && \Base::instance()->exists('POST.'.$name))
			{
				$attrs .= " value='".\Base::instance()->get('POST.'.$name)."' ";
			}
		}
		echo "$label<input type='$type' name='$name' $attrs>$validation";
	}
	static function textArea($name,$array_attrs )
	{
		$label = '';
		$attrs = '';
		$value = '';
		$validation = '';
		if(\Base::instance()->exists('SESSION.validate.fields.'.$name)){
			if(!isset($array_attrs['class'])){
				$array_attrs['class'] = "";
			}
			$array_attrs['class'] .= " validation-error";
			
			$validation = "<label class='field-validation-error'>".\Base::instance()->get('SESSION.validate.fields.'.$name)."</label>";
		}
		if(!isset($array_attrs['value']) && \Base::instance()->exists('POST.'.$name))
		{
			$array_attrs['value'] = \Base::instance()->get('POST.'.$name);
		}
		
		if(empty($array_attrs["rows"]))
		{
			$array_attrs["rows"] = 3;
		}
		foreach($array_attrs as $key => $val)
		{
			if($key == "class"){
				$attrs .= " $key='$val form-control'";
			}else if($key == "label"){
				$label = "<label>$val: </label>";
			}else if($key == "value"){
				$value = $val;
			}else{
				$attrs .= " $key='$val'";
			}
		}
		echo "$label<textarea name='$name' $attrs>$value</textarea>$validation";
	
	}
	static function startForm($array_attrs = array()){
		$attrs = '';
		$validate = false;
		$id = '';
		
		foreach($array_attrs as $key => $val)
		{
			if($key == "action")
			{
				$val = \App\Plugins\html::makeLink($val);
			}
			
			if($key == "id"){
				$id = $val;
			}
			$attrs .= " $key='$val'";
		}
		
		echo "<form role='form' $attrs>";
	}
	static function endForm($array_attrs = array()){
		echo "</form>";
	}
	
	static function submit($text = "submit",$array_attrs = array())
	{
		foreach($array_attrs as $key => $val)
		{
			if($key == "class"){
				$attrs .= " $key='$val btn btn-default'";
			}else{
				$attrs .= " $key='$val'";
			}
		}
		echo "<br/><br/><button type='submit'>$text</button>";
	}
	
	static function img($imgurl,$array_attrs = array()){
		$fw=\Base::instance();
		$uri = $fw->get('uri');
		$attrs = "src='$uri$imgurl'";
		foreach($array_attrs as $key => $val)
		{
			$attrs .= " $key='$val'";
		}
		
		echo "<img $attrs>";
	}
	
	static function pagination($total_pages, $page, $url)
	{
		echo "<div class='pagination'>";
		echo "<ul>";
		
		for ($i = 1; $i <= $total_pages; $i++){  
                        if($i == $page)
                        {
                                echo "<li class='active'>";
                        }else
                        {
                                echo "<li>";
                        }
                        
                        echo "<a href='".$url."$i'>$i</a>";
                        echo "</li>";
                }
                
                echo "</ul>";
                echo "</div>";
	}
	
	static function pagination2($total_pages, $page, $url)
	{
		
		
		echo "<ul class='pagination'>";
		
		 for ($i = 1; $i <= $total_pages; $i++){  
                        if($i == $page)
                        {
                                echo "<li class='active'>";
                        }else
                        {
                                echo "<li>";
                        }
                        
                        echo "<a href='".\App\Plugins\html::makeLink($url)."$i'>$i</a>";
                        echo "</li>";
                }
                
                echo "</ul>";
	}
	
	static function defaultValue($name){
		if(\Base::instance()->exists('POST.'.$name))
		{
			echo "value='".\Base::instance()->get('POST.'.$name)."'";
		}
		
	}
	
	static function checkbox($name,$caption,$array_attrs = array()){
		$attrs = '';
		$validation = '';
		if(\Base::instance()->exists('SESSION.validate.fields.'.$name)){
			if(!isset($array_attrs['class'])){
				$array_attrs['class'] = "";
			}
			$array_attrs['class'] .= " validation-error";
			
			$validation = "<span class='field-validation-error'>(".\Base::instance()->get('SESSION.validate.fields.'.$name).")</span>";
		}
		
		foreach($array_attrs as $key => $val)
		{
			if($key == "class"){
				$attrs .= " $key='$val btn btn-default'";
			}else{
				$attrs .= " $key='$val'";
			}
		}
		
		$checked = '';
		
		if(!isset($array_attrs['checked']) && \Base::instance()->exists('POST.'.$name)  )
		{ 
			$val = \Base::instance()->get('POST.'.$name);
			if($val){
				$checked = 'checked';
			}
		}
		
		echo "<input type='checkbox' name='$name' $attrs $checked>$caption $validation ";
	}
	
	private static function makeLink($link){
		$lang = \Base::instance()->get('lang_set');
		$lang_href = '';
		if(!empty($lang)){
			$lang_href = "/$lang";
		}
		
		$uri = substr(\Base::instance()->get('uri'),0,-1);
		
		return $uri.$lang_href.$link;
		
	}
	
}
