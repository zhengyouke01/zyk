<?php
declare(strict_types = 1);
/**
 * 数组分页
 * @param $data
 * @param $page
 * @param $pageSize
 * @return array
 */
function zyk_paging_data(array $data, int $page, int $pageSize) {
    $total = count($data);
    $pageCount = ceil($total / $pageSize);
    $start = ($page-1) * $pageSize;
    $list = [];
    if($page <= $pageCount) {
        $list = array_slice($data, $start, $pageSize);
    }
    return ['list' => $list, 'page' => $page, 'page_size' => $pageSize, 'total' => $total];
}

/**
 * 数组切割
 *
 * @author wxw 2020/1/13
 *
 * @param array $arr 数组
 * @param int $size 长度
 * @param string $field 筛选的字段
 * @param string $last 筛选的值
 *
 * @return array
 */
function zyk_page_slice(array $arr, int $size, string $field, string $last = '') {
    if (!empty($last)) {
        $index = array_search($last, array_column($arr, $field));
    } else {
        $index = -1;
    }
    if ($index === false) {
        // 找不到，返回空
        return [];
    }
    // 切割数组，从index+1开始，size大小
    return array_slice($arr, $index+1, $size);
}

/**
 * 二维数组变成一维数组
 * @author lwh 2020-03-20
 * @param $array
 * @return array
 */
function zyk_multi2array(array $array) {
    static $result_array = array();
    foreach ($array as $key => $value) {
        if (is_array($value)) {
            multi2array($value);
        }else{
            $result_array[] = $value;
        }
    }
    return $result_array;
}

/**
 * 二维数组去重  *****|*****
 * @author lwh 2020-02-05
 * @param $array2D
 * @param bool $stkeep
 * @param bool $ndformat
 * @return mixed
 */
function zyk_unique_arr(array $array2D, bool $stkeep=false, bool $ndformat=true) {
    // 判断是否保留一级数组键 (一级数组键可以为非数字)
    if($stkeep) $stArr = array_keys($array2D);
    // 判断是否保留二级数组键 (所有二级数组键必须相同)
    if($ndformat) $ndArr = array_keys(end($array2D));
    //降维,也可以用implode,将一维数组转换为用逗号连接的字符串
    foreach ($array2D as $v){
        $v = join(",",$v);
        $temp[] = $v;
    }
    //去掉重复的字符串,也就是重复的一维数组
    $temp = array_unique($temp);
    //再将拆开的数组重新组装
    foreach ($temp as $k => $v)
    {
        if($stkeep) $k = $stArr[$k];
        if($ndformat) {
            $tempArr = explode(",",$v);
            foreach($tempArr as $ndkey => $ndval) $output[$k][$ndArr[$ndkey]] = $ndval;
        }
        else $output[$k] = explode(",",$v);
    }
    $last_names = array_column($output,'cate_id');
    array_multisort($last_names,SORT_ASC,$output);
    return $output;
}

/**
 * 删除数组中的某一列
 * @param $array
 * @param $key
 * @return mixed
 */
function zyk_arr_delete_col(array $array, string $key) {
    array_walk($array, function (&$v) use ($key) {
        unset($v[$key]);
    });
    return $array;
}

/**
 * 将数组某一个key，作为数组的key（保证key不重复）array_column()  *****|*****
 * @param $arr
 * @param $key_name
 * @return mixed
 */
function zyk_array_key_change(array $arr, string $key_name) {
    return array_reduce($arr,function(&$newArray,$v) use ($key_name) {
        $newArray[$v[$key_name]] = $v;
        return $newArray;
    });
}

/**
 * 过滤数组中的字段（除了需要的字段，其他内容将被过滤）
 * @param $array array 被过滤的数组
 * @param $fields array 需要的字段
 * @return mixed
 */
function zyk_filter_array_fields(array $array, array $fields) {
    foreach ($array as $key => $value) {
        if (!in_array($key, $fields)) {
            unset($array[$key]);
        }
    }
    return $array;
}

/**
 * 二维数组排序
 * @param $arr
 * @param $keys
 * @param string $type
 * @return array
 */
function zyk_array_sort(array $arr, string $keys, string $type = 'desc') {

    $key_value = $new_array = array();
    foreach ($arr as $k => $v) {
        $key_value[$k] = $v[$keys];
    }
    if ($type == 'asc') {
        asort($key_value);
    } else {
        arsort($key_value);
    }
    reset($key_value);
    foreach ($key_value as $k => $v) {
        $new_array[$k] = $arr[$k];
    }
    return $new_array;
}

/**
 * 获取数组中的某一列 array_column可实现 *****|*****
 * @param array $arr 数组
 * @param string $key_name  列名
 * @return array 返回那一列的数组
 */
function zyk_get_arr_column(array $arr, string $key_name) {
    $arr2 = array();
    foreach($arr as $key => $val){
        $arr2[] = $val[$key_name];
    }
    return $arr2;
}

/**
 * 获取所有下级信息
 * @param array $data
 * @param int $pid
 * @param string $p_name
 * @param string $s_name
 * @return array
 */
function get_childrens($data = [], $pid = 0, $p_name='pid' , $s_name='id') {
    $allchild=array();//最终结果
    $info=array($pid);//第一次执行时候
    do {
        $child=array();
        $state=false;
        foreach ($info as $value) {
            foreach ($data as $key => $val) {
                if($val[$p_name] == $value){
                    $allchild[]=$val[$s_name];//找到我的下级立即添加到最终结果中
                    $child[]=$val[$s_name];//将我的下级id保存起来用来下轮循环他的下级
                    $state=true;
                }
            }
            $info=$child;//foreach中找到的我的下级集合,用来下次循环
        }

    }while ($state==true);
    return $allchild;
}

/**
 * 把返回的数据集转换成Tree
 * @param array $list 要转换的数据集
 * @param string $pid parent标记字段
 * @param string $level level标记字段
 * @return array
 */
function zyk_list_to_tree($list, $pk='id', $pid = 'pid', $child = '_child', $root = 0) {
    // 创建Tree
    $tree = array();
    if(is_array($list)) {
        // 创建基于主键的数组引用
        $refer = array();
        foreach ($list as $key => $data) {
            $refer[$data[$pk]] =& $list[$key];
        }
        foreach ($list as $key => $data) {
            // 判断是否存在parent
            $parentId =  $data[$pid];
            if ($root == $parentId) {
                $tree[] =& $list[$key];
            }else{
                if (isset($refer[$parentId])) {
                    $parent =& $refer[$parentId];
                    $parent[$child][] =& $list[$key];
                }
            }
        }
    }
    return $tree;
}

