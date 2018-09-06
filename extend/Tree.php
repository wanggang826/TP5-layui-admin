<?php
namespace extend;
/**
 * PHP生成树形结构,无限多级分类
 */
class Tree{

	protected static $config = array(
		/* 主键 */
		'primary_key' 	=> 'id',
		/* 父键 */
		'parent_key'  	=> 'pid',
		/* 展开属性 */
		'expanded_key'  => 'expanded',
		/* 叶子节点属性 */
		'leaf_key'      => 'leaf',
		/* 孩子节点属性 */
		'children_key'  => 'child',
		/* 是否展开子节点 */
		'expanded'    	=> false,
		/* 生成的html class名 */
		'class_name'    => '',
		/* 生成的html id名 */
		'id_name'       => '',
		/* 表单名称 */
		'form_name'   => '',
	);

	protected static $data = [];

	/* 结果集 */
	protected static $result = [];

	/* 层次暂存 */
	protected static $level = [];

	protected static $getlevel = 0;

	public function __construct($data,$options=[],$level=0){
		$config = array_merge(self::$config,$options);
		$config['form_name'] = $config['form_name'] ?: $config['primary_key'];
		self::$data = $data;
		self::$config = $config;
		self::$getlevel  = (int)$level;
 	}

	/**
	 * @name 生成树形结构
	 * @param array 二维数组
	 * @return mixed 多维数组
	 */
	public static function makeTree($index = 0){
		$dataset = self::buildData();
		$r = self::makeTreeCore($index,$dataset,'normal');
		return $r;
	}
	/* 生成select */
    public function makeSelect($selected='0',$option_key='',$placeholder =''){
    	extract(self::$config);
    	!$option_key && $option_key = $primary_key;
    	$select = '<select id="'.$id_name.'" class="'.$class_name.'" name="'.$form_name.'">';
    	$r = self::makeTreeForHtml();
    	if ($placeholder != '') {
    		$select .= '<option value="">'.$placeholder.'</opton>';
    	}
    	foreach($r as $item){
    		$select .= '<option ';
    		if ($item[$primary_key] == $selected) {
    			$select .= 'selected="selected" ';
    		}
    		$select .= 'value="'.$item[$primary_key].'">';
    		$select .= str_repeat('&nbsp;&nbsp;&nbsp;',$item['level']).$item[$option_key];
    		$select .= '</option>';
    	}
    	$select .= '</select>';
        return $select;
    }
	/* 生成线性结构, 便于HTML输出 */
	public static function makeTreeForHtml($index = 0){
		$dataset = self::buildData();
		$r = self::makeTreeCore($index,$dataset,'linear');
		return $r;
	}

	/* 格式化数据, 私有方法 */
	private static function buildData(){
		extract(self::$config);
		$r = array();
		foreach(self::$data as $item){
			$id = $item[$primary_key];
			$parent_id = $item[$parent_key];
			$r[$parent_id][$id] = $item;
		}

		return $r;
	}

	/* 生成树核心, 私有方法  */
	private static function makeTreeCore($index,$data,$type='linear')
	{
		extract(self::$config);
		foreach($data[$index] as $id=>$item)
		{
			if($type=='normal'){
				if(isset($data[$id]))
				{
					$item[$expanded_key]= self::$config['expanded'];
					$item[$children_key]= self::makeTreeCore($id,$data,$type);
				}
				else
				{
					$item[$leaf_key]= true;
				}
				$r[] = $item;
			}else if($type=='linear'){
				$parent_id = $item[$parent_key];
				self::$level[$id] = $index==0?0:self::$level[$parent_id]+1;
				if(self::$getlevel){
					if (self::$level[$id]<self::$getlevel) {
						$item['level'] = self::$level[$id];
						self::$result[] = $item;
					}
				} else {
					$item['level'] = self::$level[$id];
					self::$result[] = $item;
				}
				if(isset($data[$id])){
					self::makeTreeCore($id,$data,$type);
				}

				$r = self::$result;
			}
		}
		return $r;
	}
}


?>