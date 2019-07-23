<!--This page presents the article text and details about them. There is also a box including keywords in the article. At the bottom is a box for presenting images and entities of the articles.-->

<?php
    include_once("authenticate.php");
?>

<!DOCTYPE html>
<html>
   <head>
      <title>SCOTUSApp - Display Article</title>
      <meta charset="utf-8">
      <!-- Bootstrap stuff -->
      <meta name="viewport" content="width=device-width,initial-scale=1">
      <!-- Latest compiled and minified CSS -->
      <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
      <!-- jQuery library -->
      <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
      <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
      <script src="js/jquery.js"></script>
      <!-- Latest compiled JavaScript -->
      <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
			<script>
			function changeResBut(){  //***
				document.getElementById("resBut").style.backgroundColor =  //***
				"#87ceeb" /*sky blue*/;  //***
			}
			function revertResBut(){ //revert style back to original for tab2
				document.getElementById("resBut").style.backgroundColor =  //***
				"rgba(255, 255, 255, 0.7)" /*transparent white*/;  //***
			}
			</script>
   </head>
   <body style=" height:100%; background-color: #fffacd; font-family: monospace; font-weight: bold;">  <!--***-->
      <div style="float:right; margin-right:1.5%;font-size: 18px; font-family: monospace;">
         <a style="color:black;" href="user_page.php"><?php echo $_SESSION['name']?></a> | <a style="color:black;" href="logout.php">Logout</a>
      </div>
		 <!-- header -->
	   <div style="background-color: #fffacd; padding: 30px; text-align: center;">  <!--***-->
	      <h1 style="font-size: 50px; font-family: monospace; font-weight: bold;"><a href='index.php' style='color:black;'>SCOTUSApp</a></h1>  <!--***-->
         <div align="right">
            <a style="color:black; text-decoration:none;" href="index.php">
               <button class="btn btn-default" id="resBut" onmouseover="changeResBut()" onmouseout="revertResBut()" style="height: 30px; font-weight: bold; font-family: monospace; background-color: rgba(255, 255, 255, 0.45); border: solid 3px; border-radius: 10px;">
               Restart
               </button>
            </a>
		   </div>
	      <hr>
	   </div>
        <?php
          include_once("db_connect.php");
          $search_term = $_GET['idArticle'];
          $sql = "SELECT date, title, source, url, FROM article WHERE idArticle='%{$search_term}%'";

          if (isset($_POST['search'])) {
            $search_term = $_GET['idArticle'];
            $sql .= "WHERE idArticle='%{$search_term}%'";
          }
          else {
            $search_term = $_GET['idArticle'];
            $sql = "SELECT date, source, author, title, article_text, url,score,magnitude FROM article WHERE idArticle='{$search_term}'";
            $keywordSQL = "SELECT keyword FROM article_keywords WHERE idKey IN (SELECT idKey FROM keyword_instances WHERE idArticle = '{$search_term}')";

            $imageSQL = "SELECT path FROM image WHERE idArticle IN ('{$search_term}')";
            $imgEntity = "SELECT idEntity, score FROM entity_instances WHERE idImage IN (SELECT idImage FROM image WHERE idArticle IN ('{$search_term}'))";
          }

          $query = mysqli_query($connect, $sql) or die(mysqli_connect_error());
          $keywords = mysqli_query($connect, $keywordSQL) or die(mysqli_connect_error());
          $images = mysqli_query($connect, $imageSQL) or die(mysqli_connect_error());
          $entities = mysqli_query($connect, $imgEntity) or die(mysqli_connect_error());
        ?>
      <div class='container'>
      <div class='content-wrapper'>
      <div>
         <div style="float:left;" class='col-xs-3 col-md-3'>
            <div id="rectangle" style="width:number px; height:number px; background-color:white; border-radius: 25px; padding: 20px; border: 2px solid #000000;">
               <b><big><big><big>Details</big></big></big></b></br></br>
	       <b><big><big>ID:</big></big></b>
                	<big><?php echo $search_term; ?></big></br></br>
               <b><big>Author</big></b></br>
               <?php ($row = mysqli_fetch_assoc($query)); echo $row['author']; ?></br></br>
               <b><big>Source</big></b></br>
               <?php echo $row['source']; ?></br></br>
               <b><big>Publication Date</big></b></br>
               <?php echo $row['date']; ?></br></br>
               <b>
                  <big>
                     <div id="dont-break-out" style="word-break: break-word; word-break: break-all; -ms-word-break: break-all; word-wrap: break-word; overflow-wrap: break-word;">URL</div>
                  </big>
               </b>
               <a href="<?php echo $row['url']; ?>"><?php echo substr($row['url'], 0, 30); echo"...";?></a></br></br>
               <b><big>Sentiment Score: <?php echo $row['score']; ?></big></b></br>
               <b><big>Magnitude: <?php echo $row['magnitude']; ?></big></b></br>
            </div>
        </br>
            <div>
                <div id="rectangle" style="width:number px; height:number px; background-color:white; border-radius: 25px; padding: 20px; border: 2px solid #000000;">
                    <b><big><big><big>Key Words</big></big></big></b></br></br>
                   <?php $keywords = mysqli_query($connect, $keywordSQL) or die(mysqli_connect_error());
                       while ($row = mysqli_fetch_assoc($keywords)){
                          echo $row['keyword']; echo "</br>";
                       }
                    ?>
                </div>
             </div>
         </div>
         </div>
         <div style="float:right;" class='col-xs-9 col-md-9 center-block'>
            <div id="rectangle" style="width:number px; height:number px; background-color:white; border-radius: 25px; padding: 20px; border: 2px solid #000000;">
               <?php $query = mysqli_query($connect, $sql) or die(mysqli_connect_error()); ($row = mysqli_fetch_assoc($query));?>
               <b><big><?php echo $row['title']; ?></b></big></br>
               <?php echo $row['date']; ?></br>
               <?php 
                  // display only a third of the article text (for copyright reasons)
                  $text = $row['article_text'];
                  $n = floor(strlen($text) / 3);
                  $text = substr($text,0,$n) . "...";
                  echo nl2br($text); 
               ?>
               </br>
               </table>
            </div>
         </div>
     </br></br>
         <div style="float:right;" class='col-xs-9 col-md-9 center-block'>
         </br>
            <div id="rectangle" style="width:number px; height:number px; background-color:white; border-radius: 25px; padding: 20px; border: 2px solid #000000;">
                <div>
                <b><big><big><big>Images</big></big></big></b></br></br>
                    <?php
                        $images = mysqli_query($connect, $imageSQL) or die(mysqli_connect_error());
                       $row = mysqli_fetch_assoc($images);
                       if ($row){

                          echo "<img src=\"../images/"; echo $row['path']; echo "\" style=\"max-width:84%;\"></br>";
                       }
                       else{
                            echo "<b>None</b>";
                          }
                    ?>
               </table>
            </div>
            <div style="float:none;">
                <b><big><big><big><br>Entities</big></big></big></b></br></br>
                <?php
                        $entities = mysqli_query($connect, $imgEntity) or die(mysqli_connect_error());
                       $row = mysqli_fetch_assoc($entities);
                       if ($row){
                          $ID = $row['idEntity'];
                          $SQL = "SELECT entity from image_entities WHERE idEntity IN ('{$ID}')";
                          $sqlQ = mysqli_query($connect, $SQL) or die(mysqli_connect_error());
                          $sqlRow = mysqli_fetch_assoc($sqlQ);
                          echo $sqlRow['entity']; echo "<div style=\"float:right;\"> Score: "; echo $row['score'];
                              echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</div></br>";
                          while ($row = mysqli_fetch_assoc($entities)){
                              $ID = $row['idEntity'];
                              $SQL = "SELECT entity from image_entities WHERE idEntity IN ('{$ID}')";
                              $sqlQ = mysqli_query($connect, $SQL) or die(mysqli_connect_error());
                              $sqlRow = mysqli_fetch_assoc($sqlQ);
                              echo $sqlRow['entity']; echo "<div style=\"float:right;\"> Score: "; echo $row['score'];
                              echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</div></br>";
                          }
                       }
                       else{
                            echo "<b>None</b>";
                          }
                    ?>
            </div>
            </div>
         </div>
     </div>
      </br></br></br></br></br></br></br></br></br></br>
   </body>
</html>
