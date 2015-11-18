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

    public function __get($attribute){
        if(isset($this->$attribute)){
            return $this->$attribute;
        }else{
            return NULL;
        }
    }

}






?>