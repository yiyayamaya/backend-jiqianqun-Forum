<?php
header("ACCESS-CONTROL-ALLOW-ORIGIN:http://www.sornhub.com");
/**
 * thread.php
 *  mode
 *      0 - get the information of the thread
 *          input
 *              comment_id 其实是threadid
 *              
 *          output
 *              thread内容等等
 *    
 *      1 - 新建tiezi repleyID为默认值0                 
 *      2 - 新建tiezi repleyID为要回复的帖子的ID           
 *
 *
 */

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


require_once("configuration/load.php");


if (isset($_POST["mode"])) {
    global $db;

    switch ($_POST["mode"]) {
        // get the thread  包括主楼 其他楼
        case 0:
            if (isset($_POST["comment_id"])) {
                $result = [];
               
                
                

                // check if there is any comment of the script 显示主楼内容
                $zhulou = search_script_comment(["*"], true, 6, false, [$_POST["comment_id"]]);
                if ($zhulou != false) {
                    $result["comments"] = $zhulou;
                } else {
                    $result["comments"] = [];
                }



                 $huifua=array();
                 $huifub=array();   
                
                // check if there is any comment of the script 显示一楼以后的回复内容

                //function search_script_comment($select_fields=["*"], $join_table=true, $mode=0, $is_asc=true, $where_values=[])
                $huifu = search_tiezi_bythread(["*"], false, 6, false, [$_POST["comment_id"]]);//此处设为false 避免了搜索里一个看不懂的地方
                if ($huifu != false) {


                    for ($x=0; $x<count($huifu); $x++) {  //把搜索到的$huifu遍历并分类

                        $thetype=$huifu[$x]->commentType;
                        if($thetype==1)
                        {
                            $huifua[]=$huifu[$x];
                        }

                        elseif($thetype==0)
                        {
                            $huifub[]=$huifu[$x];
                        }

                    }

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
                
                
                $result["huifua"] = $huifua; 

                $result["huifub"] = $huifub; 
                
                }
                    
            
                echo json_encode($result);
            }
            break;
        
         // create new tiezi
         case 1:
         if (isset($_POST["user_id"]) && isset($_POST["comment_id"]) && isset($_POST["tiezi"])) {
             $results = [];
             $time = date("Y/m/d H:i:s", time());
             $insert = $db->insert("tiezi",
                 array(
                     "userID" => $_POST["user_id"],
                     "threadID" => $_POST["comment_id"],
                     "commentType" => 0,
                     "userComment" => $_POST["tiezi"],
                     "notify_lz" => $_POST["louzhu"],//要回复的楼主的id
                     "postTime" => $time
                 )
             );
             if ($insert) {

                $thequery="SELECT LAST_INSERT_ID()";
                $tiezi_id=$db->query($thequery);
                
                 $user = search_user([$_POST["user_id"]], 0, ["username"])[0];
                 $results["username"] = $user->username;
                 $results["time"] = $time;
                 $results["tiezi_id"] = $tiezi_id;
             }
             echo json_encode($results);
         }
         break;
        
          // create new tiezi 并且新帖子是回复性的
          case 2:
          if (isset($_POST["user_id"]) && isset($_POST["comment_id"]) && isset($_POST["tiezi"])) {
              $results = [];
              $time = date("Y/m/d H:i:s", time());
              $insert = $db->insert("tiezi",
                  array(
                      "userID" => $_POST["user_id"],
                      "threadID" => $_POST["comment_id"],
                      "commentType" => 1,                          //楼中楼形式的帖子 type为1
                      "userComment" => $_POST["tiezi"],
                      "notify_cz_tiezi_id" => $_POST["reply_id"], //层主帖子id
                      "notify_cz" => $_POST["replyedu_id"],//要回复的层主的id
                      "notify_lz" => $_POST["louzhu"],//要回复的楼主的id
                      "postTime" => $time
                  )
              );
              if ($insert) {
                  $user = search_user([$_POST["user_id"]], 0, ["username"])[0];
                  $results["username"] = $user->username;
                  $results["time"] = $time;



                  $thequery="SELECT LAST_INSERT_ID()";
                  $tiezi_id=$db->query($thequery);
                  $results["tiezi_id"] = $tiezi_id;
                  
                  
                 
              }
              echo json_encode($results);
          }
          break;


    }
}