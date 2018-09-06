<?php
namespace app\admin\controller;
use think\Session;
use app\admin\Model\Menu;
class Index extends AdminBase{
	protected $class = ['nav nav-second-level','nav nav-third-level'];
    protected $menus,$html  = [
        'noleaf_a' => "<a class='J_menuItem' href='%s'><i class='fa fa-fw fa-%s level-%s'></i>",
        'hasleaf_a' => "<a class='' href='%s'><i class='fa fa-fw fa-%s level-%s'></i>",
        'lv_menu' =>"<span class='nav-label'>%s</span>",
        'left_icon' =>"<span class='fa fa-fw arrow'></span>",
    ];
	public function index(){
        $this->view->engine->layout(false);
        $this->getMenus();
        $headimg = Session::get('user.headimg');
       
        $pash = "./public/upload/".$headimg;
        if(!file_exists($pash)){//检测图片图片
            $headimg="";
        }
        // dump($this->menus);die;
    	return view('',['menus'=>$this->menus,'headimg'=>$headimg]);
	}
    public function main(){
        
    }
    public function get_dataImg(){
        vendor('jpgraph.jpgraph');
        vendor('jpgraph.jpgraph_bar');
        vendor('jpgraph.jpgraph_line');
        $datay = model('order')->get_earnings_data();
        if(count($datay) ==1){
            $datay = array(0,$datay[0]);
        }
        // $datay =array(200,456);  
        $num  = array_sum($datay);//收益总量
        $graph = new \Graph('1083',349);
        $graph->SetScale("textlin");
        $graph->SetShadow();
        $graph->img->SetMargin(60,30,50,70); //设置图像边距
        $graph->yaxis->scale->SetGrace(10);//设置y轴刻度值分辨率  
        $graph->graph_theme = null; //设置主题为null，否则value->Show(); 无效
        $lineplot1=new \LinePlot($datay); //创建设置曲线对象
        $lineplot1->value->SetColor("red");
        $lineplot1->value->Show();
        $graph->Add($lineplot1);  //将曲线放置到图像上
        $graph->title->Set(iconv('UTF-8','GB2312',"近一个月收益统计图(总收益: ￥".$num.")"));   //设置图像标题
        $graph->title->SetFont(FF_BIG5,FS_BOLD);//设置X轴字体样式及大小
        $graph->title->SetColor('red');
        $graph->xaxis->title->Set(iconv('UTF-8','GB2312',"日期")); //设置坐标轴名称
        $graph->xaxis->title->SetColor('red');
        $graph->xaxis->SetFont(FF_SIMSUN,FS_BOLD);//设置X轴字体样式及大小
        $graph->yaxis->title->Set(iconv('UTF-8','GB2312','收益(拾RMB)'));
        $graph->yaxis->title->SetColor('red');
        $graph->yaxis->SetFont(FF_SIMSUN,FS_BOLD);//设置y轴字体样式及大小
        $graph->title->SetMargin(10);
        $graph->xaxis->title->SetMargin(10);
        $graph->yaxis->title->SetMargin(10);
        $graph->title->SetFont(FF_SIMSUN,FS_BOLD); //设置字体
        $graph->yaxis->title->SetFont(FF_SIMSUN,FS_BOLD);
        $graph->xaxis->title->SetFont(FF_SIMSUN,FS_BOLD);
        if(file_exists('./graph.png')){
            unlink('./graph.png');
        }
        $graph->Stroke('./graph.png');
    }
    /**
     * 获取html菜单，并写入缓存文件
     */
    protected function getMenus(){
        $menu_list = unserialize(session('user.menuList'));
        if(!empty($menu_list)){
            $menu_id = $menu_list['menu_id'];
            $this->getMenuId($menu_id);

            // TONE:如果正式环境，不显示菜单设置
            if( config('app_debug') === false ){
                unset($menu_id[16]);
            }

            config('menu_id',$menu_id);
            $menus = Menu::all(function($db){
                $db->where(['menu_id'=>['in',config('menu_id')],'status'=>['eq',1]])->order('sort', 'asc');
            });
            resultToArray($menus);
            if ($menus) {
                $option = ['primary_key'=>'menu_id'];
                $menus = getTree($menus,$option)->makeTreeForHtml();
                $menus = getTree($menus,$option)->makeTree();
                $this->setMenu($menus);
            }
        }
    }
    /**
     * 获取已有菜单父级菜单
     */
    protected function getMenuId(&$menu_id){
        $menu_pid = model('menu')->where(['menu_id'=>['in',$menu_id],'status'=>['eq',1],'pid'=>['neq',0]])->column('pid');
        $menu_pid = array_flip(array_flip($menu_pid));
        if ($menu_pid) {
            $this->getMenuId($menu_pid);
        }
        $menu_id = array_merge($menu_id,$menu_pid);
        $menu_id = array_flip(array_flip($menu_id));
        sort($menu_id);
    }

    /**
     * 递归设置菜单格式
     */
    protected function setMenu(&$menus){
        array_walk($menus, 'self::setMenuFormat');
    }
    /**
     * 递归菜单html
     */
    protected function setMenuFormat(&$val){
        if ($val['level']>=2) {//保证最多只显示三级菜单
            $val['leaf'] = true;unset($val['child']);
        }
        extract($this->html);extract($val);
        $menu_icon = $menu_icon ?: 'list';
        if (array_key_exists('leaf', $val) && $leaf===true) {
            $format = "<li>{$noleaf_a}{$lv_menu}</a></li>";
            $this->menus .= sprintf($format,$url,$menu_icon,$level,$menu_name);
        } else {
            $val['leaf'] = false;
            $format = "<li>{$hasleaf_a}{$lv_menu}{$left_icon}</a><ul class='nav {$this->class[$level]}'>";
            $this->menus .= sprintf($format,$url,$menu_icon,$level,$menu_name);
            $this->setMenu($child);
            $this->menus .= "</ul></li>";
        }
    }
  
}