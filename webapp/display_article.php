<?php
    // this page displays the full details about any given article
    include_once("authenticate.php");
    include("admins.php");
    include_once("db_connect.php");

    $idArticle = (!empty($_GET['idArticle']) ? trim($_GET['idArticle']) : '');
    $idArticle = mysqli_real_escape_string($connect,$idArticle);

    $details_sql = "SELECT * from article WHERE idArticle='$idArticle'";
    $keywords_sql = "SELECT keyword FROM keyword_instances NATURAL JOIN article_keywords WHERE idArticle = '$idArticle'";
    $images_sql = "SELECT idImage, url, path FROM image WHERE idArticle='$idArticle'";

    $details_query = mysqli_query($connect, $details_sql);
    $keywords_query = mysqli_query($connect, $keywords_sql);
    $images_query = mysqli_query($connect, $images_sql);
?>

<html>
    <head>
        <title>SCOTUSApp - Display Article</title>
        <meta charset="utf-8">
        <!-- Bootstrap stuff -->
        <meta name="viewport" content="width=device-width,initial-scale=1">
        <!-- Latest compiled and minified CSS -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
        <!-- jQuery library -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
        <!--<script src="js/jquery.js"></script>-->
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
        <style>
            .box {
                background-color:white; 
                border-radius: 25px; 
                padding: 20px; 
                border: 2px solid #000000;
                word-wrap: break-word
            }
            .box.hr {
                border-color:"black";
                background-color:"#black";
                color:"#black";
            }
            .box-header {
                font-size: 25px;
            }
            .field-header {
                font-size: 15.5px;
            }
            .subheader {
                font-size:17px;
            }
            #entities {
                font-weight: bold;
            }
            #entities td {
                font-size:14px;
            }
        </style>
    </head>
    <body style="height:100%; background-color: #fffacd; font-family: monospace; font-weight: bold;">
        <?php echo contactLink(); ?>
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
        <div class='container'>
            <div class="row">
                <div class="col-md-3">
                    <div id="details" class="box">
                        <span class="box-header">Details</span><br><br>
                        <?php $row = mysqli_fetch_assoc($details_query) ?>
                        <span class="field-header">ID: <?php echo isset($row['idArticle']) ? $row['idArticle'] : "N/A"; ?></span><br><br>
                        <span class="field-header">Author</span><br><?php echo !empty($row['author']) ? $row['author'] : "N/A"; ?><br><br>
                        <span class="field-header">Source</span><br><?php echo !empty($row['source']) ? $row['source'] : "N/A"; ?><br><br>
                        <span class="field-header">Publication Date</span><br><?php echo !empty($row['date']) ? $row['date'] : "N/A"; ?><br><br>
                        <span class="field-header">URL</span><br><?php echo !empty($row['url']) ? "<a href='{$row['url']}'>{$row['url']}</a>" : "N/A"; ?><br><br>
                        <span class="field-header">Sentiment Score: <?php echo isset($row['score']) ? $row['score'] : "N/A"; ?></span><br>
                        <span class="field-header">Magnitude: <?php echo isset($row['magnitude']) ? $row['magnitude'] : "N/A"; ?></span><br>
                    </div>
                    <div id="bias" class="box" style="margin-top:6%;">
                        <span class="box-header">Source Bias</span><hr>
                        <?php
                            if(!empty($row['source'])) {
                                $bias_sql = "SELECT * FROM source_bias WHERE source = '{$row['source']}' ORDER BY allsides_id LIMIT 1";
                                $bias_query = mysqli_query($connect,$bias_sql);
                                $bias = mysqli_fetch_assoc($bias_query);
                                echo "<span class='subheader'>AllSides</span><br><br>";
                                if(!empty($bias['allsides_bias'])) {
                                    echo "<span class='field-header'>Bias: {$bias['allsides_bias']}</span><br><br>";
                                    echo "<span class='field-header'>Confidence</span><br>{$bias['allsides_confidence']}<br><br>";
                                    $total_votes = $bias['allsides_agree'] + $bias['allsides_disagree'];
                                    $community_agreement = $total_votes > 0 ? round(($bias['allsides_agree'] / $total_votes) * 100,2) . "%" : "N/A";
                                    echo "<span class='field-header'>Community Agreement</span><br>$community_agreement [{$bias['allsides_agree']} / {$bias['allsides_disagree']}]";
                                }
                                else {
                                    echo "N/A";
                                }
                                echo "<hr>";
                                echo "<span class='subheader'>Media Bias Fact Check</span><br><br>";
                                if(!empty($bias['mbfs_bias'])) {
                                    echo "<span class='field-header'>Bias: {$bias['mbfs_bias']}</span><br><br>";
                                    echo "<span class='field-header'>Score: {$bias['mbfs_score']}</span>";
                                }
                                else {
                                    echo "N/A";
                                }
                            }
                            else { 
                                echo "No data";
                            }
                        ?>
                    </div>
                    <div id="keywords" class="box" style="margin-top:6%; margin-bottom: 6%;">
                        <span class="box-header">Keywords</span><br><br>
                        <?php
                            if(mysqli_num_rows($keywords_query) > 0) {
                                while($keywords = mysqli_fetch_assoc($keywords_query)) {
                                    echo "{$keywords['keyword']}<br>";
                                }
                            }
                            else {
                                echo "None";
                            }  
                        ?>
                    </div>
                </div>
                <div class="col-md-9">
                    <div id="main" class="box">
                        <?php echo !empty($row['title']) ? "<span class='subheader'>{$row['title']}</span>" : "N/A"; ?><hr>
                        <?php 
                            // display only a third of the article text (for copyright reasons)
                            if(!empty($row['article_text'])) {
                                $text = $row['article_text'];
                                $n = floor(strlen($text) / 3);
                                $text = nl2br(substr($text,0,$n) . "...");
                            }
                            else {
                                $text = "N/A";
                            }
                            echo $text;
                        ?>
                    </div>
                    <div id="images" class="box" style="margin-top:1.25%;">
                    <span class="box-header">Images & Entities</span><br><br>
                        <?php
                            if(mysqli_num_rows($images_query) > 0) {
                                while($image = mysqli_fetch_assoc($images_query)) {
                                    $imgpath = file_exists("../images/{$image['path']}") ? "../images/{$image['path']}" : $image['url'];
                                    echo "<img src='$imgpath' style='max-width:85%;'><br><br>";
                                    $img_entity_sql = "SELECT entity,score FROM image_entities NATURAL JOIN entity_instances WHERE idImage={$image['idImage']}";
                                    $img_entity_query = mysqli_query($connect,$img_entity_sql);
                                    if(mysqli_num_rows($img_entity_query) > 0) {
                                        $table =   "<table id='entities' style='width:75%'>
                                                        <thead>
                                                            <tr class='subheader'>
                                                                <th>Entity</th>
                                                                <th>Score</th>
                                                            </tr>
                                                        </thead>
                                                    <tbody>";
                                        while($entity = mysqli_fetch_assoc($img_entity_query)) {
                                            $table .=  "<tr>
                                                            <td>{$entity['entity']}</td>
                                                            <td>{$entity['score']}</td>
                                                        <tr>";        
                                        }
                                        $table .= "</tbody></table>";
                                        echo $table;
                                    }
                                    else {
                                        echo "No entities";
                                    }
                                }
                            }
                            else {
                                echo "None";
                            }
                        ?>
                    </div>
                </div>
            </div>
            <!-- source bias citations -->
            <footer style='text-align:center; margin-top:1.25%'>
                <div class="row">
                    <div class="col-md-12">
                        <!--THIS PORTION OF THE ATTRIBUTION MUST BE INCLUDED-->
                        <a rel="license" href="http://creativecommons.org/licenses/by-nc/4.0/"><img style="margin-top: 5px; margin-bottom: 5px;" alt="Creative Commons License" style="border-width:0" src="https://i.creativecommons.org/l/by-nc/4.0/88x31.png" /></a><br />
                        <p><a xmlns:dct="http://purl.org/dc/terms/" href="https://www.allsides.com/media-bias/media-bias-ratings" rel="dct:source"><span xmlns:dct="http://purl.org/dc/terms/" href="http://purl.org/dc/dcmitype/Dataset" property="dct:title" rel="dct:type">AllSides Media Bias Ratings</span></a> by <a xmlns:cc="http://creativecommons.org/ns#" href="https://www.allsides.com/unbiased-balanced-news" property="cc:attributionName" rel="cc:attributionURL">AllSides.com</a> are licensed under a <a rel="license" href="http://creativecommons.org/licenses/by-nc/4.0/">Creative Commons Attribution-NonCommercial 4.0 International License</a>. You may use this data for research or noncommercial purposes provided you include this attribution.</p>
                    </div>  
                </div>
            </footer>
        </div>
    </body>
</html>