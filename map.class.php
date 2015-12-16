<?php
include_once "chess.class.php";
class map{
    private $battleground=array();
    private $map_x_start=250;
    private $map_y_start=200;
    private $chosen_chess=0;
    private $chosen_location=array();
    private $log=array();
    private $describe;   //中文版棋谱
    private $step=0;    //回合数
    private $mainwin;
    private $player;    //走子方，0=>black，1=>read
    private $player_name=array('黑方','红方');
    private $starting=false;
    private $map_img;
    private $created_chess=array();

    public function restart(){
        $this->chosen_chess=0;
        $this->chosen_location=array();
        $this->starting=false;
        $this->__construct($this->mainwin);
    }

    //@todo 重新执行构造函数多次，还是会导致图片加载次数过多，内存不足
    public function __construct($mainwin){
        global $win_heigh;
        global $win_width;
        global $record_frame;
        $this->map_y_start=($win_heigh>600)?(($win_heigh-600)/3):0;
        $this->map_x_start=($win_width>600)?(($win_width-600)/2)-50:0;
        $this->mainwin=$mainwin;
        $this->player=1;
        $this->step++;
        $this->describe='第1回合:'.PHP_EOL;
        $this->battleground=array_fill(0,9,array_fill(0,10,0));
        //初始化棋子
        $this->battleground[0][0]=$this->get_chess(0,4);
        $this->battleground[1][0]=$this->get_chess(0,3);
        $this->battleground[2][0]=$this->get_chess(0,2);
        $this->battleground[3][0]=$this->get_chess(0,1);
        $this->battleground[4][0]=$this->get_chess(0,0);
        $this->battleground[5][0]=$this->get_chess(0,1);
        $this->battleground[6][0]=$this->get_chess(0,2);
        $this->battleground[7][0]=$this->get_chess(0,3);
        $this->battleground[8][0]=$this->get_chess(0,4);
        $this->battleground[1][2]=$this->get_chess(0,5);
        $this->battleground[7][2]=$this->get_chess(0,5);
        $this->battleground[0][3]=$this->get_chess(0,6);
        $this->battleground[2][3]=$this->get_chess(0,6);
        $this->battleground[4][3]=$this->get_chess(0,6);
        $this->battleground[6][3]=$this->get_chess(0,6);
        $this->battleground[8][3]=$this->get_chess(0,6);
        $this->battleground[0][9]=$this->get_chess(1,4);
        $this->battleground[1][9]=$this->get_chess(1,3);
        $this->battleground[2][9]=$this->get_chess(1,2);
        $this->battleground[3][9]=$this->get_chess(1,1);
        $this->battleground[4][9]=$this->get_chess(1,0);
        $this->battleground[5][9]=$this->get_chess(1,1);
        $this->battleground[6][9]=$this->get_chess(1,2);
        $this->battleground[7][9]=$this->get_chess(1,3);
        $this->battleground[8][9]=$this->get_chess(1,4);
        $this->battleground[1][7]=$this->get_chess(1,5);
        $this->battleground[7][7]=$this->get_chess(1,5);
        $this->battleground[0][6]=$this->get_chess(1,6);
        $this->battleground[2][6]=$this->get_chess(1,6);
        $this->battleground[4][6]=$this->get_chess(1,6);
        $this->battleground[6][6]=$this->get_chess(1,6);
        $this->battleground[8][6]=$this->get_chess(1,6);
        if(!$this->map_img){
            $this->map_img=wb_load_image(PATH_RES.'chessdesktop.bmp');
        }
        $this->draw_map();
        wb_set_text($record_frame,$this->describe);
    }

    public function draw_map(){
        //1.画棋盘
        wb_draw_image($this->mainwin,$this->map_img,$this->map_x_start,$this->map_y_start);
        //2.画棋子
        foreach($this->battleground as $key1=>$value1){
            foreach($value1 as $key2=>$value2){
                if($value2 instanceof chess){
                    list($x,$y)=$this->get_coordinate($key1,$key2);
                    wb_draw_image($this->mainwin,$value2->image_resource,$x,$y,40,40);
                }
            }
        }
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

    /**
     * 悔棋
     */
    public function undo(){
        if(empty($this->log)){
            return ;
        }
        $undo=array_pop($this->log);
        $this->battleground[$undo['from'][0]][$undo['from'][1]]=$this->get_chess($undo['chess']['color'],$undo['chess']['token']);
        if(is_array($undo['chess_old'])){
            $chess_old=$this->get_chess($undo['chess_old']['color'],$undo['chess_old']['token']);
        }else{
            $chess_old=0;
        }
        $this->battleground[$undo['goto'][0]][$undo['goto'][1]]=$chess_old;
        $this->swich_player(true);
        $this->draw_map();
    }

    /**
     * 保存棋谱
     */
    public function save_game(){
        $file_path= PATH_DATABASE.date ('Ymd_His',time()).'_'.(time()%10000).'.chess.log';
        $log['machine']=$this->log;
        $log['mankind']=$this->describe;    //给人类看的棋盘
        $res=file_put_contents($file_path,json_encode($log));
        if(false===$res){
            return $res;
        }
        return $file_path;
    }

    public function __get($property){
        if(isset($this->$property)){
            return $this->$property;
        }else{
            return NULL;
        }
    }

    private function get_chess($color,$token){
        //懒汉式
        if(!isset($this->created_chess[$color.'_'.$token])){
            $this->created_chess[$color.'_'.$token]=new chess($color,$token);
        }
        return $this->created_chess[$color.'_'.$token];
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

    private function chose_chess($chess,$location){
        $this->draw_map();
        $this->chosen_chess = $chess;
        $this->chosen_location = $location;
        list($x_s, $y_s) = $this->get_coordinate($location[0], $location[1]);
        wb_draw_rect($this->mainwin, $x_s, $y_s, 40, 40, 0x0000FF,false,0,6);
//        $this->draw_rect($this->mainwin, $x_s, $y_s, 40, 40, 0x0000FF);
    }

    private function move_chess($location){
        $this->add_log($this->chosen_chess,$this->chosen_location,$location);
        list($i_c,$j_c)=$this->chosen_location;
        list($i, $j) = $location;
        $this->battleground[$i_c][$j_c]=0;
        $this->battleground[$i][$j]=$this->chosen_chess;
        $this->chosen_chess=0;
        $this->swich_player();
        $this->draw_map();
        list($x_s, $y_s) = $this->get_coordinate($i_c,$j_c);
        wb_draw_rect($this->mainwin, $x_s, $y_s, 40, 40, 0x0000FF,false,0,6);
        list($x_s, $y_s) = $this->get_coordinate($i,$j);
        wb_draw_rect($this->mainwin, $x_s, $y_s, 40, 40, 0x0000FF,false,0,6);
    }

    private function kill_chess($location){
        $this->add_log($this->chosen_chess,$this->chosen_location,$location);
        list($i_c,$j_c)=$this->chosen_location;
        list($i, $j) = $location;
        $ghost=$this->battleground[$i][$j];
        $this->battleground[$i_c][$j_c]=0;
        $this->battleground[$i][$j]=$this->chosen_chess;
        $this->chosen_chess=0;
        $this->draw_map();
        list($x_s, $y_s) = $this->get_coordinate($i_c,$j_c);
        wb_draw_rect($this->mainwin, $x_s, $y_s, 40, 40, 0x0000FF,false,0,6);
        list($x_s, $y_s) = $this->get_coordinate($i,$j);
        wb_draw_rect($this->mainwin, $x_s, $y_s, 40, 40, 0x0000FF,false,0,6);
        if(0==$ghost->token ){
            $this->starting=false;
            $winner=$this->player_name[$this->player];
            $res=wb_message_box($this->mainwin, "游戏结束，$winner 胜。".PHP_EOL."点击是保存棋谱，点击否重新开始",'游戏结束啦',WBC_YESNO);
            if($res){
                //保存棋盘
                $return=$this->save_game();
                if(false===$return){
                    $message='保存失败!';
                }else{
                    $message='保存成功!'.PHP_EOL.'棋谱位置：'.$return;
                }
                $res=wb_message_box($window, $message.PHP_EOL."点击确定重新开始",'保存棋谱',WBC_INFO);
                $this->restart();
            }else{
                $this->restart();
            }
            return ;
        }
        $this->swich_player();
    }

    private function swich_player($is_undo=false){
        $this->starting=true;
        $this->player=(1+$this->player)%2;
        if(1==$this->player && !$is_undo){
            $this->step++;
            $this->describe.='第'.$this->step.'回合:'.PHP_EOL;
        }

        if($is_undo) {
            $describe = $this->describe;
            $describe = explode(PHP_EOL, $describe);
            array_pop($describe);
            array_pop($describe);
            if (0 == $this->player) {
                $this->step--;
                array_pop($describe);
            }
            $this->describe = implode(PHP_EOL,$describe).PHP_EOL;
        }
    }
    private function draw_rect($mainwin,$x,$y,$width,$height,$color){
        wb_draw_line($mainwin,$x,$y,$x+$width,$y,$color,false,1);
        wb_draw_line($mainwin,$x,$y,$x,$y+$height,$color);
        wb_draw_line($mainwin,$x+$width,$y,$x+$width,$y+$height,$color);
        wb_draw_line($mainwin,$x,$y+$height,$x+$width,$y+$height,$color);
    }

    private function add_log($chess,$from,$goto){
        $x=array('九','八','七','六','五','四','三','二','一');
        $y=array('1','2','3','4','5','6','7','8','9');
        $start=($chess->color)?$x[$from[0]]:$y[$from[0]];
        $end=($chess->color)?$x[$goto[0]]:$y[$goto[0]];
        $end=($from[0]==$goto[0])?abs($from[1]-$goto[1]):$end;
        if($from[1]==$goto[1]){
            $action='平';
        }elseif($from[1]>$goto[1]){
            $action=($chess->color)?'进':'退';
        }else{
            $action=($chess->color)?'退':'进';
        }
        $describe=$chess->name.$start.$action.$end;
        $temp=($chess->color)?'后':'前';
        for($i=0;$i<10;$i++){
            if($i==$from[1]){
                $temp=($chess->color)?'前':'后';
                continue;
            }
            $target=$this->battleground[$from[0]][$i];
            if($target instanceof chess && $chess->color==$target->color && $chess->token==$target->token ){
                $describe=$temp.$chess->name.$action.$end;
                break;
            }
        }
        $old=$this->battleground[$goto[0]][$goto[1]];
        $log['chess']=$chess->to_array();
        $log['chess_old']=($old instanceof chess)?$old->to_array():$old;
        $log['from']=$from;
        $log['goto']=$goto;
        $log['describe']=$describe;
        $this->describe.=$this->player_name[$this->player].':'.$describe.PHP_EOL;
        $this->log[]=$log;
        global $record_frame;
        wb_set_text($record_frame,$this->describe);
//        echo iconv("UTF-8","GB2312",$describe.PHP_EOL);
    }

}


?>