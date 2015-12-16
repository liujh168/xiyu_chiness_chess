<?php
/**
 * Created by PhpStorm.
 * User: xiyu
 * Date: 2015/11/17
 * Time: 13:52
 * 1.判断棋子是否可以移动
 * 2.判断棋子是否可以吃子
 *
 */
class logic{
    public static function chess_moveable($chess,$map){
            return rand(1,10)>3;
    }
}



?>