<?php
namespace extend;

/**
*
*/
class File
{
    protected static $unit = ['B','KB','MB','GB'];

    public static function get_unit($num = 0){
        $i = 0;
        while ($num/pow(1024,$i) >= 1000) {
            $i++;
        }
        if ($i>(count(self::$unit)-1)) {
            $i = count(slef::$unit)-1;
        }
        $num = sprintf('%.2f',$num/pow(1024,$i)) . self::$unit[$i];
        return $num;
    }
    /**
     * 递归删除目录下所有文件
     */
    public static function del_dir_recursive($path,$delall = true){
        $dh = opendir($path);
        while(($d = readdir($dh)) !== false){
            if($d == '.' || $d == '..'){//如果为.或..
                continue;
            }
            $tmp = $path.'/'.$d;
            if(!is_dir($tmp)){//如果为文件
                @unlink($tmp);
            }else{//如果为目录
                self::del_dir_recursive($tmp,$delall);
            }
        }
        @closedir($dh);
        if ($delall) {
            @rmdir($path);
        }
    }

    public static function file_count( $dir ){
        $count=0;
        $result = array();
        $handle = opendir($dir);
        if ( $handle ){
            while ( ( $file = readdir( $handle ) ) !== false ){
                if ( $file != '.' && $file != '..'){
                    $cur_path = $dir . DIRECTORY_SEPARATOR . $file;
                    if ( is_dir( $cur_path ) ){
                        $count += self::file_count( $cur_path );
                    } else {
                        $count += 1;
                    }
                }
            }
            closedir($handle);unset($result);
        }
        // $count 为文件目录统计
        return $count;
    }

    public static function dir_size($dir){
        $dh = opendir($dir);
        $size = 0;
        while(false !== ($file = @readdir($dh))){ //循环读取目录下的文件
            if($file!='.' and $file!='..'){
                $path = $dir.'/'.$file;//设置目录，用于含有子目录的情况
                if(is_dir($path)){
                    $size += self::dir_size($path);//递归调用，计算目录大小
                } elseif(is_file($path)) {
                    $size += filesize($path);//计算文件大小
                }
            }
        }
        closedir($dh);
        return $size;
    }
}