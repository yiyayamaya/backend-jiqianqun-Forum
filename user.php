<?php
header("ACCESS-CONTROL-ALLOW-ORIGIN:http://www.sornhub.com");
/**
 * user.php
 *  mode
 *      0 - get information
            input
 *              user_id
 *          output
 *              username
 *              script_uploaded
 *              script_run
 *
 *      1 - delete script
 *          input
 *              script_id
 *          output
 *              0 - delete successfully
 *              1 - unknown error
 *
 *      2 - get results
 *          input
 *              result_id
 *          output
 *              results
 * 
 *      4-user表里面查找关注的吧
 *      5-添加贴吧的关注
 * 
 *      6-tiezi表里查找被回复的内容 
 */

require_once('configuration/load.php');


if (isset($_POST["mode"])) {
    switch ($_POST["mode"]) {







        case 6:
        if (isset($_POST["user_id"])) {
            $result = [];
           
            
            

            // check if there is any comment of the script 显示主楼内容
            $zhulou = search_script_comment(["*"], true, 6, false, [$_POST["comment_id"]]);
            if ($zhulou != false) {
                $result["comments"] = $zhulou;
            } else {
                $result["comments"] = [];
            }



                
            
            // check if there is any comment of the script 显示一楼以后的回复内容

            //function search_script_comment($select_fields=["*"], $join_table=true, $mode=0, $is_asc=true, $where_values=[])
            $huifu = search_tiezi_bythread(["*"], false, 6, false, [$_POST["comment_id"]]);//此处设为false 避免了搜索里一个看不懂的地方
            if ($huifu != false) {
                $result["huifu"] = $huifu;   //此处的huifu是倒序的 下面的huifu_user也是倒序的
            } else {
                $result["huifu"] = [];
            }


            if($huifu != false) 
            {

            $user_name = array();

            for ($x=0; $x<count($huifu); $x++) 
            {
            
            $a = $huifu[$x]->userID;
            
            
            
            $huifu_user = search_user([$a],0, ["*"], false);//用userid搜索username
            if ($huifu_user != false) {
                
               
                    $user_name[$x] = $huifu_user[0]->username;
                  
               
            } else {
                //$result["huifu_user"] = [];
            }

            }
            
            

            $result["huifu_user"] = $user_name;   
            
            }
                
        
            echo json_encode($result);
        }
        break;










        //user添加对吧的关注
        case 5:
        if (isset($_POST["user_id"])) {
            $results = [];
            $script_id = $_POST["script_id"];
            




            // 查询是否已经关注这个吧了 已经关注过返回0 关注成功返回1
            $scripts_loved = search_user([$_POST["user_id"]], 0, ["loved_tieba"]);
            $str_scripts_loved=$scripts_loved[0]->loved_tieba; //$scripts_loved 是stdclass() 取出其中字符串$str_scripts_loved

            $divided_scripts_loved = explode(" ", $str_scripts_loved);

            
            $count=0;

            foreach($divided_scripts_loved as $value)
            { 
                if($value!='')
                {
                   
                    if($value!=$script_id)
                    {
                        $count=$count+1;
                    }
                   

                }

                } 
                      
            if($count==count($divided_scripts_loved)-1){

           
           
            $str_scripts_loved .=' ';

            $str_scripts_loved .=$script_id;//加空格 加最新关注的贴吧id

            $array_after=array(
                "loved_tieba" => $str_scripts_loved
            ); 



            $update = $db->update("user",$array_after,"userID", $_POST["user_id"]);
             //在user表里面 以userID那列为索引 user_id为值的那一行 修改


            
            echo json_encode(1);
            }
            else{
                echo json_encode(0);
            }

        }
            break;





        //user表里面查找关注的吧
        case 4:
        if (isset($_POST["user_id"])) {
            $results = [];






            // get the username of the user
            $usrename = search_user([$_POST["user_id"]], 0, ["username"]);
            if ($usrename != false) {
                $usrename = $usrename[0]->username;
                $results["username"] = $usrename;
            }
            // 搜到关注贴吧的id
            $scripts_loved = search_user([$_POST["user_id"]], 0, ["loved_tieba"]);
            $str_scripts_loved=$scripts_loved[0]->loved_tieba; //$scripts_loved 是stdclass() 取出其中字符串$str_scripts_loved
            $divided_scripts_loved = explode(" ", $str_scripts_loved);

           
            
            $total_scripts_loved= array();


            foreach($divided_scripts_loved as $value)
            { 
                if($value!='')
                {
                   
                  
                  $scripts_loved =search_script(["*"], 8, 10, [$value]);
                  $total_scripts_loved[] =  $scripts_loved;


                }

            }            


            $results["scripts_loved"] = $total_scripts_loved;//以上返回关注吧信息

            //以下返回新回复信息
            
            //1 先搜索层主的

            $gottenset=array();
           
            
            $reply = search_tiezi_bythread(["*"], false, 8, false, [$_POST["user_id"]]);//此处设为false 避免了搜索里一个看不懂的地方
            

            if ($reply != false) {
                $results["reply"] = $reply;   //此处的huifu是倒序的 下面的huifu_user也是倒序的


                foreach($reply as $k=>$v){ 
                    
                    $gottenset[$v->tieziID]=1;
                   
                    }

                    


            } else {
                $results["reply"] = [];
            }


            //下面的处理把楼主回复搜到了 然后把reply出现过的剔除了
            

            $reply2_arr=array();

            $reply2 = search_tiezi_bythread(["*"], false, 7, false, [$_POST["user_id"]]);//此处设为false 避免了搜索里一个看不懂的地方
            if ($reply2 != false) {
               
                
                foreach($reply2 as $k=>$v){ 
                    
                    if(array_key_exists( $v->tieziID, $gottenset)){

                      

                        
                    }   
                    else{
                        $gottenset[$v->tieziID]=2; //不作为对层主的的前提下 作为楼主回复 就是2
                        array_push($reply2_arr,$v);

                       
                    }

                    }
                
                //$results["reply"] = array_merge_recursive($results["reply"], $reply2_arr);
                $results["reply2"] =  $reply2_arr;




            } else {
                $results["reply2"] =  [];
            }

            
            $final_reply=array();
            $length1=count($results["reply"]);
            $length2=count($results["reply2"]);

            /*
            $copyofreply=array();
            $copyofreply2=array();
            copy($reply,$copyofreply);
            copy($reply2_arr,$copyofreply2);
            */

            

                while(count($reply2_arr)!=0 or count($reply)!=0){ //  进行处理的是$reply和$reply2_arr 强烈怀疑 把层主回复 和非层主的楼主回复捏在一起会有bug

                    if(count($reply2_arr)==0)
                    {
                        
                         $final_reply = array_merge_recursive($final_reply, $reply);
                        break;
                    }

                    if(count($reply)==0)
                    {
                        $final_reply = array_merge_recursive($final_reply, $reply2_arr);
                        break;
                    }

                    if($reply[0]<$reply2_arr[0])
                    {
                        array_push($final_reply,$reply2_arr[0]);
                        array_shift($reply2_arr);
                    }

                    else
                    {
                        array_push($final_reply,$reply[0]);
                        array_shift($reply);
                    }

                 }

                 /*
                 $new_gottenset=array();

                 $i=0;
                 foreach($gottenset as $v)
                 {
                    $new_gottenset[$i]=$v;
                    $i=$i+1;
                 }
                 */ 
                //php foreach遍历数组是按照添加的顺序遍历

            

           



            $results["gottenset"] = $gottenset;
            //$results["gottenset2"] = $gottenset2;
            $results["final"] = $final_reply;

            //2 
            echo json_encode($results);


        }
            break;
        // get information
        case 0:
            if (isset($_POST["user_id"])) {
                $results = [];

                // get the username of the user
                $usrename = search_user([$_POST["user_id"]], 0, ["username"]);
                if ($usrename != false) {
                    $usrename = $usrename[0]->username;
                    $results["username"] = $usrename;
                }

                // get the scripts uploaded by the user
                $scripts_uploaded = search_script(["*"], 1, 10, [$usrename]);
                if ($scripts_uploaded == false) {
                    $results["scripts_uploaded"] = [];
                } else {
                    $results["scripts_uploaded"] = $scripts_uploaded;
                }

                // get the scripts loved by the user
                $scripts_love = search_user_history($_POST["user_id"], "", 2, true);
                if ($scripts_love == false) {
                    $results["scripts_love"] = [];
                } else {
                    $results["scripts_love"] = $scripts_love;
                }

                // get the scripts the user ran before
                $scripts_run = search_user_history($_POST["user_id"], "", 0, true);
                if ($scripts_run == false) {
                    $results["scripts_run"] = [];
                } else {
                    // return at most five results
                    if (count($scripts_run) > 5) {
                        $scripts_run = array_slice($scripts_run, 0, 5);
                    }

                    foreach ($scripts_run as $idx => $script) {
                        // split the parameters
                        $scripts_run[$idx]->parameter = explode(' ', $scripts_run[$idx]->parameter);

                        // get the names and descriptions of the parameters
                        $parameter_names = search_script_parameter($script->scriptID, ["name"]);
                        if ($parameter_names == false) {
                            $scripts_run[$idx]->parameter_names = [];
                        } else {
                            $scripts_run[$idx]->parameter_names = $parameter_names;
                        }
                    }

                    $results["scripts_run"] = $scripts_run;
                }

                echo json_encode($results);
            }
            break;
        // delete script
        case 1:
            if (isset($_POST["script_id"])) {
                $script_id = $_POST["script_id"];
                // delete the folder if it already exists
                $folder_path = "../scripts/$script_id/";
                if (is_dir($folder_path) == true) {
                    $it = new RecursiveDirectoryIterator($folder_path, RecursiveDirectoryIterator::SKIP_DOTS);
                    $files = new RecursiveIteratorIterator($it, RecursiveIteratorIterator::CHILD_FIRST);
                    foreach($files as $file) {
                        if ($file->isDir()){
                            rmdir($file->getRealPath());
                        } else {
                            unlink($file->getRealPath());
                        }
                    }
                    if (rmdir($folder_path) == false) {
                        echo json_encode(1);
                        break;
                    } else {
                        $delete = $db->delete("script_result", "scriptID", $script_id);
                        $delete = $db->delete("feedback", "scriptID", $script_id);
                        $delete = $db->delete("script_comment", "scriptID", $script_id);
                        $delete = $db->delete("user_history", "scriptID", $script_id);
                        $delete = $db->delete("script_parameter", "scriptID", $script_id);
                        $delete = $db->delete("script", "scriptID", $script_id);
                        echo json_encode(0);
                    }
                }
            }
            break;
        // get results
        case 2:
            if (isset($_POST["result_id"])) {
                $results = search_script_result($_POST["result_id"], ["*"]);
                if ($results == false) {
                    echo json_encode("");
                } else {
                    echo json_encode($results);
                }
            }
            break;
    }
}
