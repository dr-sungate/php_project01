<?php
/**
 * Smarty plugin {assign_array}
 *
 * usage: {assign_array var="var" values="val1[,val2,val3...]" [separator=","]}
 *
 * テンプレート内で配列を定義するSmartyプラグイン。
 * 変数varに配列を格納する。配列の値はvaluesにカンマ区切りで記述する。
 * separatorを指定することで、カンマ以外の文字を区切り文字に指定できる。
 *
 * @package   Lism::Plugins
 * @version   $Id: function.assign_array.php 3 2007-04-06 04:08:59Z Ryo Miyake $
 * @copyright 2006-2007 Lism.in
 * @author    Ryo Miyake <ryo.studiom@gmail.com>
 */

/**
 * assign_array
 *
 * @param  array   $value   引数のハッシュ
 * @param  object  &$smarty Smartyオブジェクト
 * @return void
 */
function smarty_function_assign_array($value, &$smarty)
{
    if (!isset($value['var']) || !isset($value['values'])) return;
    $separator = (isset($value['separator'])) ? $value['separator'] : '<|>';
    $smarty->assign($value['var'], explode($separator, $value['values']));
}
?>