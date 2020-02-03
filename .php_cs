<?php
$header = <<<EOF
The file is part of the payment lib.

(c) Leo <dayugog@gmail.com>

This source file is subject to the MIT license that is bundled
with this source code in the file LICENSE.
EOF;

$finder = PhpCsFixer\Finder::create()
    ->exclude('vendor/')
    ->in(__DIR__);

$rules = [
    // PSR2为基准
    '@PSR2'                                       => true,

    // 数组统一:[ ]
    'array_syntax'                                => ['syntax' => 'short'],

    // namespace 之后空一行
    'blank_line_after_namespace'                  => true,

    // new: namespace 之前不能有空行
    'no_blank_lines_before_namespace'             => true,

    // 合并多条连续的 unset 方法
    'combine_consecutive_unsets'                  => true,

    // 字符串连接空格
    'concat_space'                                => ['spacing' => 'one'],

    // case 空格
    'cast_spaces'                                 => true,

    // else if  转换为 elseif
    'elseif'                                      => true,

    // utf8 无bom头编码
    'encoding'                                    => true,

    // 必须使用<?php 或者 <?=
    'full_opening_tag'                            => true,

    // 单行注释使用 双斜杠
    'hash_to_slash_comment'                       => true,

    // 头部的注释信息,统一
    'header_comment'                              => ['header' => $header],

    // 代码必须使用配置的缩进类型
    'indentation_type'                            => true,

    // 所有PHP文件必须使用相同的行结束
    'line_ending'                                 => true,

    // true, false, and null 必须为小写
    'lowercase_constants'                         => true,

    // 在方法参数和方法调用中，每个逗号之间绝对不能为空格，每个逗号之后必须有一个空格
    'method_argument_space'                       => true,

    // class 大括号后不应有空行
    'no_blank_lines_after_class_opening'          => true,

    // 删除空注释
    'no_empty_comment'                            => true,

    // 删除无用的空语句
    'no_empty_statement'                          => true,

    // 命名空间声明行不应包含前导空
    'no_leading_namespace_whitespace'             => true,

    // => 不应该被多行空格包围
    'no_multiline_whitespace_around_double_arrow' => true,

    // 在开始的括号之后，绝对不能是一个空格。在右括号之前绝对不能是空格
    'no_spaces_inside_parenthesis'                => true,

    // 绝对不能在offset 周围空格
    'no_spaces_around_offset'                     => ['inside', 'outside'],

    // 禁止在关闭分号之前的单行空格
    'no_singleline_whitespace_before_semicolons'  => true,

    // 删除列表函数调用中的逗号
    'no_trailing_comma_in_list_call'              => true,

    // PHP单行数组不应该有逗号
    'no_trailing_comma_in_singleline_array'       => true,

    // 注释和phpdocs中必须没有尾随空格
    'no_trailing_whitespace_in_comment'           => true,

    // 删除空白行末尾的尾随空格
    'no_whitespace_in_blank_line'                 => true,

    // 在数组声明中，每个逗号之前绝对不能是空格
    'no_whitespace_before_comma_in_array'         => true,

    // 必须删除未使用的使用语句
    'no_unused_imports'                           => true,

    // 删除无用的else
    'no_useless_else'                             => true,

    // 删除无用的return
    'no_useless_return'                           => true,

    // 导入列表排序
    'ordered_imports'                             => true,

    // 没有结束标记的PHP文件必须始终以单个空行内容结尾
    'single_blank_line_at_eof'                    => true,

    // 每个声明必须是一个使用关键字
    'single_import_per_statement'                 => true,

    // 将简单字符串的 双引号 转换为 单引号
    'single_quote'                                => true,

    // 三元操作符空格
    'ternary_operator_spaces'                     => true,

    // 数组去空格
    'trim_array_spaces'                           => true,

    // 一元运算符靠着操作数
    'unary_operator_spaces'                       => true,

    /** 可见性必须在所有属性和方法上声明;
     * 抽象和最终必须在可见性之前声明;
     * 静态必须在可见性之后声明
     **/
    'visibility_required'                         => true,

    // 数组箭头对齐
    'binary_operator_spaces'                      => [
        'operators' => [
            '='  => 'align_single_space_minimal',
            '=>' => 'align_single_space_minimal',
        ],
    ],

    //  数组的逗号后面有一个空格
    'whitespace_after_comma_in_array'             => true,
];

return PhpCsFixer\Config::create()
    ->setRules($rules)
    ->setFinder($finder);
