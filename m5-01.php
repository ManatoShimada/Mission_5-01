<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <title>Mission_5-01</title>
    </head>
    <body>
        <?php
        //phpの変数をHTMLのフォームに反映させたいから先に出しておく
        
        //テーブルを作成する
            //DB接続設定 
            $dsn  ="mysql:dbname=データベース名; host=localhost";
            $user ="ユーザー名";
            $password="パスワード";
            $pdo  =new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
        
            //データベース内にテーブルを作成
            $sql  ="CREATE TABLE IF NOT EXISTS Mission5"
            ."("
            ."id INT AUTO_INCREMENT PRIMARY KEY,"
            ."name char(32),"
            ."comment TEXT,"
            ."date char(32),"
            ."password char(32)"
            .");";
            $stmt=$pdo->query($sql);
            
        //編集番号を送信したときの動作
            if(!empty($_POST["edit"])){
            //ファイルの編集が空以外で$filenameが存在するかどうか調べ、ある場合は以下のように動く
                if(!empty($_POST["edit_pass"])){
                //ファイルのパスワードが空以外で$filenameが存在するかどうか調べ、ある場合は以下のように動く
                    $edit   =$_POST["edit"];
                    $edit_pass=$_POST["edit_pass"];
                    //$edit,$edit_passの定義をした
                    
                    //入力したデータレコードを抽出する
                    $sql  = 'SELECT * FROM Mission5 WHERE id=:id';
                    $stmt = $pdo -> prepare($sql);
                    $stmt -> bindParam(':id', $edit, PDO::PARAM_INT);
                    $stmt -> execute();
                    $results = $stmt -> fetchAll();
                    if($results==true){
                    //$editの番号の投稿がある場合以下のコードが動く 
                        if($results[0]["password"]==$edit_pass){
                        //パスワードがあっている場合
                        //正直ここもたつきの助けてもらった、、いまだにわかってないこともある
                            foreach($results as $row){
                            $num=$row["id"];
                            $name=$row["name"];
                            $comment=$row["comment"];
                            }
                        }
                        else{
                        //パスワードがあっていない場合    
                            $num="";
                            $name="";
                            $comment="";
                            $condition="パスワードが違います";
                        }
                    }
                    else{
                    //$edit_passの番号の投稿がない場合
                        $num="";
                        $name="";
                        $comment="";
                        $condition="編集対象の投稿がありません";
                    }
                }
                else{
                //パスワードの入力がなかった時以下のコードが動く
                    $num="";
                    $name="";
                    $comment="";
                    $condition="パスワードが入力されていません";
                }
                
            }
            else{
            //editの入力フォームが空の場合
                $num="";
                $name="";
                $comment="";
            }
        ?>
        <strong><span style="font-size: 25px;">入力フォーム<br></span></strong>
        <form action="" method="post">
            <input type="text" name="name" placeholder="名前" value="<?php echo $name?>"><br>
            <input type="text" name="comment" placeholder="コメント" value="<?php echo $comment?>"><br>
            <input type="hidden" name="edit_number" value="<?php echo $num ?>">
            <input type="password" name="pass" placeholder="パスワード">
            <!--valueで内部を?php echo $name?とすることによって上で作動させたコードの内容を反映させる-->
            <!--hiddenによってブラウザに表示されなくなる（これも助けてもらったとこ！）-->
            <input type="submit" value="送信"><br>
            <br>
            <input type="number" name="delete" placeholder="削除対象番号"><br>
            <input type="password" name="delete_pass" placeholder="パスワード">
            <input type="submit" value="削除"><br>
            <br>
            <input type="number" name="edit" placeholder="編集対象番号"><br>
            <input type="password" name="edit_pass" placeholder="パスワード">
            <input type="submit" value="編集">
        </form>
        <?php
        //編集か新規投稿で入力フォームに入力したときの動作
            if(!empty($_POST["name"]) && !empty($_POST["comment"])){
            //ファイルの名前とコメントが空以外のときコードは動く
                if(!empty($_POST["pass"])){
                //ファイルの名前とコメントが空以外のときコードは動く
                    $name   =$_POST["name"];
                    $comment=$_POST["comment"];
                    $date   =date("Y年m月d日 H:i:s");
                    $pass   =$_POST["pass"];
                    
        //編集したいとき編集番号をもらったときの動作            
                    if(!empty($_POST["edit_number"])){
                    //hiddenで隠したedit_numberが空以外のときにコードが動く。つまり、これが編集したいときのコードになる。
                    //入力されているデータレコードの内容を編集
                        $sql='UPDATE Mission5 SET name=:name, comment=:comment, date=:date,  password=:password WHERE id=:id';
                        $stmt=$pdo->prepare($sql);
                        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
                        $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
                        $stmt->bindParam(':date', $date, PDO::PARAM_STR);
                        $stmt->bindParam(':password', $pass, PDO::PARAM_STR);
                        $stmt->bindParam(':id', $edit_number, PDO::PARAM_INT);
                        $edit_number=$_POST["edit_number"];
                        //edit_numberを定義した
                        $stmt->execute();
                        $condition=$edit_number."の投稿の編集完了";
                    }
        
        //新規投稿での動作        
                    else{
                    //hiddenで隠したedit_numberが空のときにコードが動く。
                    
                    //データを入力
                        $sql=$pdo->prepare("INSERT INTO Mission5 (name, comment, date, password) VALUES (:name, :comment, :date, :password)");
                        $sql->bindParam(':name', $name, PDO::PARAM_STR);
                        $sql->bindParam(':comment', $comment, PDO::PARAM_STR);
                        $sql->bindParam(':date', $date, PDO::PARAM_STR);
                        $sql->bindParam(':password', $pass, PDO::PARAM_STR);
                        $sql->execute();
                        $condition="新規投稿の成功";
                    }
                }
                else{
                //パスワードが入力されてないとき
                    $condition="パスワードが入力されていません";
                }
            }
        
        //投稿を削除したいときの動作    
            if(!empty($_POST["delete"])){
            //ファイルの名前とコメントが空で削除が空以外のときは以下のコードが動く
                if(!empty($_POST["delete_pass"])){
                //削除のパスワードが空以外のときは以下のコードが動く
                    $delete =$_POST["delete"];
                    //$deleteの定義を決めた
                    $delete_pass=$_POST["delete_pass"];
                    //$delete_passの定義を決めた
                    
                    //入力したデータレコードを抽出する
                    $sql='SELECT * FROM Mission5 WHERE id=:id ';
                    $stmt=$pdo->prepare($sql);
                    $stmt->bindParam(':id', $delete, PDO::PARAM_INT);
                    $stmt->execute();
                    $result=$stmt->fetchAll();
                    if($result==true){
                    //$deleteの番号の投稿がある場合以下のコードが動く 
                        if($result[0]["password"]==$delete_pass){
                        //パスワードがあっている場合
                        //正直ここもたつきの助けてもらった、、いまだにわかってないこともある
                        
                        //入力したデータレコードを削除
                            $sql='delete from Mission5 WHERE id=:id';
                            $stmt=$pdo->prepare($sql);
                            $stmt->bindParam(':id', $delete, PDO::PARAM_INT);
                            $stmt->execute();
                            $condition=$delete."の投稿の削除完了";
                        }
                        else{
                            $condition="パスワードが違います";
                        }
                    }
                    else{
                        $condition="削除対象の投稿がありません";
                    }
                }
                else{
                //パスワードが入力されてないとき
                    $condition="パスワードが入力されていません";
                }
            }
            
        //$conditionの内容を表示する
            if(!empty($condition)){
                echo "<strong>投稿状態</strong>";
                echo "<br>";
                echo $condition."<br>";
                echo "<hr>";
            }
            
        //先ほどまでの動作の結果をブラウザに表示する
            $sql  = 'SELECT * FROM Mission5';
            $stmt = $pdo -> query($sql);
            $results = $stmt -> fetchAll();
            echo "<br>";
            echo "<strong>入力されたデータレコード</strong>";
            echo "<br>";
            foreach($results as $show){
                echo $show['id'].' '.$show['name'].' '.$show['comment'].' '.$show["date"].'<br>';
            }
        ?>
    </body>
</html>