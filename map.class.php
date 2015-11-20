<?php
include_once "chess.class.php";
class map{
    private $battleground=array();
    private $map_x_start=250;
    private $map_y_start=200;
    private $chosen_chess=0;
    private $chosen_location=array();
    private $mainwin;
    private $player;    //走子方，0=>black，1=>read

    public function __construct($mainwin){
        $this->mainwin=$mainwin;
        $this->player=1;
        $this->battleground=array_fill(0,9,array_fill(0,10,0));
        //初始化棋子
        $this->battleground[0][0]=new chess(0,4);
        $this->battleground[1][0]=new chess(0,3);
        $this->battleground[2][0]=new chess(0,2);
        $this->battleground[3][0]=new chess(0,1);
        $this->battleground[4][0]=new chess(0,0);
        $this->battleground[5][0]=new chess(0,1);
        $this->battleground[6][0]=new chess(0,2);
        $this->battleground[7][0]=new chess(0,3);
        $this->battleground[8][0]=new chess(0,4);
        $this->battleground[8][0]=new chess(0,4);
        $this->battleground[1][2]=new chess(0,5);
        $this->battleground[7][2]=new chess(0,5);
        $this->battleground[0][3]=new chess(0,6);
        $this->battleground[2][3]=new chess(0,6);
        $this->battleground[4][3]=new chess(0,6);
        $this->battleground[6][3]=new chess(0,6);
        $this->battleground[8][3]=new chess(0,6);
        $this->battleground[0][9]=new chess(1,4);
        $this->battleground[1][9]=new chess(1,3);
        $this->battleground[2][9]=new chess(1,2);
        $this->battleground[3][9]=new chess(1,1);
        $this->battleground[4][9]=new chess(1,0);
        $this->battleground[5][9]=new chess(1,1);
        $this->battleground[6][9]=new chess(1,2);
        $this->battleground[7][9]=new chess(1,3);
        $this->battleground[8][9]=new chess(1,4);
        $this->battleground[8][9]=new chess(1,4);
        $this->battleground[1][7]=new chess(1,5);
        $this->battleground[7][7]=new chess(1,5);
        $this->battleground[0][6]=new chess(1,6);
        $this->battleground[2][6]=new chess(1,6);
        $this->battleground[4][6]=new chess(1,6);
        $this->battleground[6][6]=new chess(1,6);
        $this->battleground[8][6]=new chess(1,6);
        $this->draw_map();
    }

    public function draw_map(){
        //1.画棋盘
        $map_img=wb_load_image(PATH_RES.'chessdesktop.bmp');
        wb_draw_image($this->mainwin,$map_img,$this->map_x_start,$this->map_y_start);
        wb_destroy_image($map_img);
        //2.画棋子
        foreach($this->battleground as $key1=>$value1){
            foreach($value1 as $key2=>$value2){
                if($value2 instanceof chess){
                    $chesses_img=wb_load_image(PATH_RES.$value2->image);
                    list($x,$y)=$this->get_coordinate($key1,$key2);
                    wb_draw_image($this->mainwin,$chesses_img,$x,$y,40,40);
                    wb_destroy_image($chesses_img);
                }
            }
        }
    }

    private function get_coordinate($key1,$key2){
        $return=array($this->map_x_start+18+59*$key1,$this->map_y_start+15+59*$key2);
        return $return;
    }

    private function get_chess_by_coordinate($x,$y){
        $i1=floor(($x-($this->map_x_start+18))/59);
        $i2=ceil(($x-($this->map_x_start+18+40))/59);
        if($i1!=$i2){
            return 0;
        }
        $j1=floor(($y-($this->map_y_start+15))/59);
        $j2=ceil(($y-($this->map_y_start+15+40))/59);
        if($j1!=$j2){
            return 0;
        }
        return $this->battleground[$i1][$j1];
    }

    private function get_near_location_by_coordinate($x,$y){
        $i1=floor(($x-($this->map_x_start+18))/59);
        $i2=ceil(($x-($this->map_x_start+18+40))/59);
        if($i1!=$i2){
            return 0;
        }
        $j1=floor(($y-($this->map_y_start+15))/59);
        $j2=ceil(($y-($this->map_y_start+15+40))/59);
        if($j1!=$j2){
            return 0;
        }
        return array($i1,$j1);
    }

    public function click($x,$y){
        global $statusbar;
        if($x>$this->map_x_start && $x<$this->map_x_start+550 && $y>$this->map_y_start && $y<$this->map_y_start+600){
            $location=$this->get_near_location_by_coordinate($x,$y);
            if(is_array($location)) {
                list($i, $j) = $location;
                $chess_target=$this->battleground[$i][$j];
                if($chess_target instanceof chess){
                    if($chess_target->color==$this->player){
                        $this->chose_chess($chess_target,$location);
                        wb_set_text($statusbar, '选中一个棋子,名字叫：'.$chess_target->name);
                    }else{
                        wb_set_text($statusbar, '你不能操作这个棋子');
                        if($this->chosen_chess instanceof chess){
                            if($this->chosen_chess->killable($this,$location)){
                                $this->kill_chess($location);
                                wb_set_text($statusbar, '吃掉了一个棋子,移动方变为：'.$this->player);
                            }
                        }
                    }
                }else{
                    wb_set_text($statusbar, '点击了无效地址');
                    if($this->chosen_chess instanceof chess){
                        wb_set_text($statusbar, '你不能操作这个棋子-move');
                        if($this->chosen_chess->moveable($this,$location)){
                            $this->move_chess($location);
                            wb_set_text($statusbar, '移动一个棋子,移动方变为：'.$this->player);
                        }
                    }
                }
            }else{
                wb_set_text($statusbar, '点击无效');
            }
        }
    }

    private function chose_chess($chess,$location){
        $this->draw_map();
        $this->chosen_chess = $chess;
        $this->chosen_location = $location;
        list($x_s, $y_s) = $this->get_coordinate($location[0], $location[1]);
        $this->draw_rect($this->mainwin, $x_s, $y_s, 40, 40, 0x0000FF);
    }

    private function move_chess($location){
        list($i_c,$j_c)=$this->chosen_location;
        list($i, $j) = $location;
        $this->battleground[$i_c][$j_c]=0;
        $this->battleground[$i][$j]=$this->chosen_chess;
        $this->chosen_chess=0;
        $this->swich_player();
        $this->draw_map();
    }

    private function kill_chess($location){
        list($i_c,$j_c)=$this->chosen_location;
        list($i, $j) = $location;
        $ghost=$this->battleground[$i][$j];
        $this->battleground[$i_c][$j_c]=0;
        $this->battleground[$i][$j]=$this->chosen_chess;
        $this->chosen_chess=0;
        $this->draw_map();
        if(0==$ghost->token ){
            $winner=(1==$this->player)?'红方':'黑方';
            wb_message_box($this->mainwin, "游戏结束，$winner 胜",'游戏结束啦');
        }
        $this->swich_player();
    }

    private function swich_player(){
        $this->player=(1+$this->player)%2;
    }
    private function draw_rect($mainwin,$x,$y,$width,$height,$color){
        wb_draw_line($mainwin,$x,$y,$x+$width,$y,$color);
        wb_draw_line($mainwin,$x,$y,$x,$y+$width,$color);
        wb_draw_line($mainwin,$x+$width,$y,$x+$width,$y+$width,$color);
        wb_draw_line($mainwin,$x,$y+$width,$x+$width,$y+$width,$color);
    }

    public function __get($property){
        if(isset($this->$property)){
            return $this->$property;
        }else{
            return NULL;
        }
    }

}


?>