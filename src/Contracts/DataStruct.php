<?php
/**
 * @author: helei
 * @createTime: 2016-06-08 13:37
 * @description: 基本的数据结构
 */

namespace Payment\Contracts;


abstract class DataStruct
{
    public function __construct( array $initValue = NULL )
    {
        if (empty($initValue)) {
            return;
        }

        $class = get_called_class();
        $setValues = get_class_vars( $class );
        foreach ($initValue as $k => $v) {
            if (! array_key_exists($k , $setValues)) {
                continue;
            }

            $this->$k = $v;
        }
    }

    /**
     * 转换为数组格式
     * @return array
     */
    public function toArray()
    {
        return get_object_vars($this);
    }
}