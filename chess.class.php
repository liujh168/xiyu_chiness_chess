<?php
class chess{
    private $color; //0=>black，1=>read
    private $token;
    private $image;
    private $name;
    private $token_to_name=array(0=>'将/帅',1=>'士/仕',2=>'象/相',3=>'马',4=>'車',5=>'炮',6=>'卒/兵');

    public function __construct($color,$token){
        $this->color=$color;
        $this->token=$token;
        $this->image=$this->get_image_name();
        $this->name=$this->token_to_name[$token];
    }

    private function get_image_name(){
        $id_to_image_name=array(
            '0_0'=>'b_jiang.bmp',
            '0_1'=>'b_shi.bmp',
            '0_2'=>'b_xiang.bmp',
            '0_3'=>'b_ma.bmp',
            '0_4'=>'b_ju.bmp',
            '0_5'=>'b_pao.bmp',
            '0_6'=>'b_zu.bmp',
            '1_0'=>'r_shuai.bmp',
            '1_1'=>'r_shi.bmp',
            '1_2'=>'r_xiang.bmp',
            '1_3'=>'r_ma.bmp',
            '1_4'=>'r_ju.bmp',
            '1_5'=>'r_pao.bmp',
            '1_6'=>'r_bing.bmp',
        );
        $identity=$this->color.'_'.$this->token;
        return $id_to_image_name[$identity];
    }

    public function moveable($map,$location){
        $flag=$this->color.'_'.$this->token;
        $function_name='moveable_'.$this->color.'_'.$this->token;
        $function_name_common='moveable_'.$this->token;
        $res=false;
        switch($flag){
            case '0_0':
            case '0_1':
            case '0_2':
            case '0_6':
            case '1_0':
            case '1_1':
            case '1_2':
            case '1_6':
                $res=$this->$function_name($map,$location);
                break;
            case '0_3':
            case '0_4':
            case '1_3':
            case '1_4':
                $res=$this->$function_name_common($map,$location);
                break;
            case '0_5':
            case '1_5':
                $res=$this->moveable_4($map,$location);
                break;
        }
        return $res;
    }

    //黑将移动规则
    private function moveable_0_0($map,$location){
        $moveable_location=array(
            array(3,0),array(4,0),array(5,0),
            array(3,1),array(4,1),array(5,1),
            array(3,2),array(4,2),array(5,2),
        );
        if(!in_array($location,$moveable_location)){
            return false;
        }
        $pre_location=$map->chosen_location;
        if(1==abs($location[0]-$pre_location[0])+abs($location[1]-$pre_location[1])){
            return true;
        }else{
            return false;
        }
    }

    //黑士移动规则
    private function moveable_0_1($map,$location){
        $moveable_location=array(
            array(3,0),            array(5,0),
                        array(4,1),
            array(3,2),            array(5,2),
        );
        if(!in_array($location,$moveable_location)){
            return false;
        }
        $pre_location=$map->chosen_location;
        if(1==abs($location[0]-$pre_location[0])&&1==abs($location[1]-$pre_location[1])){
            return true;
        }else{
            return false;
        }
    }

    //黑象移动规则
    private function moveable_0_2($map,$location){
        $moveable_location=array(
            array(2,0),            array(6,0),
            array(0,2), array(4,2),array(8,2),
            array(2,4),            array(6,4),
        );
        if(!in_array($location,$moveable_location)){
            return false;
        }
        $pre_location=$map->chosen_location;
        if(2==abs($location[0]-$pre_location[0])&&2==abs($location[1]-$pre_location[1])){
            $stop_x=($location[0]+$pre_location[0])/2;
            $stop_y=($location[1]+$pre_location[1])/2;
            if($map->battleground[$stop_x][$stop_y] instanceof chess){
                return false;
            }else {
                return true;
            }
        }else{
            return false;
        }
    }

    //黑卒移动规则
    private function moveable_0_6($map,$location){
        $pre_location=$map->chosen_location;
        $diff_x=abs($location[0]-$pre_location[0]);
        $diff_y=abs($location[1]-$pre_location[1]);
        if(1!=$diff_x+$diff_y){
            return false;
        }
        if($pre_location[1]<5 && $diff_x==1){
            return false;
        }
        if($location[1]-$pre_location[1]<0){
            return false;
        }
        return true;
    }

    //红帅移动规则
    private function moveable_1_0($map,$location){
        $moveable_location=array(
            array(3,7),array(4,7),array(5,7),
            array(3,8),array(4,8),array(5,8),
            array(3,9),array(4,9),array(5,9),
        );
        if(!in_array($location,$moveable_location)){
            return false;
        }
        $pre_location=$map->chosen_location;
        if(1==abs($location[0]-$pre_location[0])+abs($location[1]-$pre_location[1])){
            return true;
        }else{
            return false;
        }
    }

    //红士移动规则
    private function moveable_1_1($map,$location){
        $moveable_location=array(
            array(3,7),            array(5,7),
                         array(4,8),
            array(3,9),            array(5,9),
        );
        if(!in_array($location,$moveable_location)){
            return false;
        }
        $pre_location=$map->chosen_location;
        if(1==abs($location[0]-$pre_location[0])&&1==abs($location[1]-$pre_location[1])){
            return true;
        }else{
            return false;
        }
    }

    //红相移动规则
    private function moveable_1_2($map,$location){
        $moveable_location=array(
            array(2,5),            array(6,5),
            array(0,7), array(4,7),array(8,7),
            array(2,9),            array(6,9),
        );
        if(!in_array($location,$moveable_location)){
            return false;
        }
        $pre_location=$map->chosen_location;
        if(2==abs($location[0]-$pre_location[0])&&2==abs($location[1]-$pre_location[1])){
            $stop_x=($location[0]+$pre_location[0])/2;
            $stop_y=($location[1]+$pre_location[1])/2;
            if($map->battleground[$stop_x][$stop_y] instanceof chess){
                return false;
            }else {
                return true;
            }
        }else{
            return false;
        }
    }

    //红兵移动规则
    private function moveable_1_6($map,$location){
        $pre_location=$map->chosen_location;
        $diff_x=abs($location[0]-$pre_location[0]);
        $diff_y=abs($location[1]-$pre_location[1]);
        if(1!=$diff_x+$diff_y){
            return false;
        }
        if($pre_location[1]>=5 && $diff_x==1){
            return false;
        }
        if($location[1]-$pre_location[1]>0){
            return false;
        }
        return true;
    }

    //马移动规则
    private function moveable_3($map,$location){
        $pre_location=$map->chosen_location;
        $diff_x=abs($location[0]-$pre_location[0]);
        $diff_y=abs($location[1]-$pre_location[1]);
        $moveable_array=array(
            array(1,2),
            array(2,1),
        );
        if(!in_array(array($diff_x,$diff_y),$moveable_array)){
            return false;
        }
        $stop_x=$pre_location[0]+(2==$diff_x)*($location[0]-$pre_location[0])/2;
        $stop_y=$pre_location[1]+(2==$diff_y)*($location[1]-$pre_location[1])/2;
        if($map->battleground[$stop_x][$stop_y] instanceof chess){
            return false;
        }else{
            return true;
        }
    }

    //車炮移动规则
    private function moveable_4($map,$location){
        $pre_location=$map->chosen_location;
        $diff_x=abs($location[0]-$pre_location[0]);
        $diff_y=abs($location[1]-$pre_location[1]);
        if($diff_x!=0 && $diff_y!=0){
            return false;
        }
        $length=max($diff_x,$diff_y);
        $step=($location[0]-$pre_location[0]+$location[1]-$pre_location[1])/$length;
        for($i=0;abs($i)<$length-1;){
            $i+=$step;
            if($map->battleground[$pre_location[0]+$i*($diff_x!=0)][$pre_location[1]+$i*($diff_y!=0)] instanceof chess){
                return false;
            }
        }
        return true;
    }

    //判断是否可以吃子
    public function killable($map,$location){
        $flag=$this->color.'_'.$this->token;
        $function_name_common='killable_'.$this->token;
        $function_name_moveable='moveable_'.$this->color.'_'.$this->token;
        $function_name_common_moveable='moveable_'.$this->token;
        $res=false;
        switch($flag){
            case '0_1':
            case '0_2':
            case '0_6':
            case '1_1':
            case '1_2':
            case '1_6':
                $res=$this->$function_name_moveable($map,$location);    //可走就可以吃
                break;
            case '0_3':
            case '0_4':
            case '1_3':
            case '1_4':
                $res=$this->$function_name_common_moveable($map,$location);
                break;
            case '0_0':
            case '0_5':
            case '1_0':
            case '1_5':
                $res=$this->$function_name_common($map,$location);
                break;
        }
        return $res;
    }

    //炮的吃子规则
    private function killable_5($map,$location){
        $bridge=false;
        $pre_location=$map->chosen_location;
        $diff_x=abs($location[0]-$pre_location[0]);
        $diff_y=abs($location[1]-$pre_location[1]);
        if($diff_x!=0 && $diff_y!=0){
            return false;
        }
        $length=max($diff_x,$diff_y);
        $step=($location[0]-$pre_location[0]+$location[1]-$pre_location[1])/$length;
        for($i=0;abs($i)<$length-1;){
            $i+=$step;
            if($map->battleground[$pre_location[0]+$i*($diff_x!=0)][$pre_location[1]+$i*($diff_y!=0)] instanceof chess){
                if($bridge){
                    return false;
                }else{
                    $bridge=true;
                }
            }
        }
        return $bridge;
    }

    //将帅的吃子规则
    private function killable_0($map,$location){
        $move_function='moveable_'.$this->color.'_0';
        if($this->$move_function($map,$location)){
            return true;
        }
        $target=$map->battleground[$location[0]][$location[1]];
        if($target instanceof chess && $target->token==0 && $this->moveable_4($map,$location)){
            return true;
        }
        return false;
    }

    public function __get($attribute){
        if(isset($this->$attribute)){
            return $this->$attribute;
        }else{
            return NULL;
        }
    }

}






?>