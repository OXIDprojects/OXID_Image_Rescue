<?php
/* sponsored by suabo.de */
class rescueimg extends oxUBase {
  
  protected $_aArticleWithImage;
  
  public function render() {
    if($this->getUser()->oxuser__oxrights->value != 'malladmin') { echo "Du bist kein Admin, oder?";exit();}
    $aMatch = glob("out/pictures/master/1/*.*");
    $iActPicCnt = oxDb::getDb()->getOne("SELECT COUNT(oxartnum) FROM oxarticles WHERE (oxpic1!='' AND oxpic1!='nopic.jpg');");    
    ?>    
    <html>
    <body><head><meta charset="utf-8">
    <link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" />
    <script src="http://code.jquery.com/jquery-1.9.1.js"></script>
    <script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>    
    <style>
      body, html {padding:25px 10px 0;margin:0;font-family:Verdana;}
      h1 {position:fixed;top:0px;left:0px;width:100%;padding:5px 10px;background:menu;box-shadow:0px 2px 3px #ccc;margin:0;font:16px Verdana;line-height:25px;height:25px;}
      h2 {position:fixed;top:0px;right:50px;padding:5px 10px;margin:0;font:16px Verdana;line-height:25px;height:25px;}
      #footer {position:fixed;bottom:0px;left:0px;width:100%;padding:5px 10px;background:menu;box-shadow:0px 2px 3px #ccc;margin:0;font:16px Verdana;line-height:25px;height:25px;}
      a {text-decoration:none;color:#666;}
      a.btn {text-decoration:none;color:white;font-weight:bold;font-size:14px;}
      .btn {padding:4px 10px;border:1px solid #666;border-radius:5px;background:#333;box-shadow:2px 2px 3px #ccc;}
      .box {width:375px;float:left;padding:5px;border:1px solid #666;border-radius:5px;background:#fff;}
      .imgbox {width:600px;min-height:150px;padding:10px;border:1px solid #666;border-radius:5px;background:#ccc;box-shadow:2px 2px 3px #ccc;}
      img {max-width:200px;max-height:200px;float:left;margin-right:10px;}
      img#info {max-width:50px;max-height:50px;float:left;margin-right:10px;}
      label {display:inline-block;min-width:130px;}
      input[type="text"] {width:250px;}
      input.btn {color:white;font-weight:bold;cursor:pointer;font-size:14px;}      
    </style>
    
<script>
  $(function() {
     $( "#artnum" ).autocomplete({
      source: "/?cl=rescueimg&fnc=ajaxSearchArtNum",
      minLength: 2,
      select: function( event, ui ) {        
        $(this).val(ui.item);
        $('#artid').val(ui.item.id);
        $('#artnum').val(ui.item.artnum);
        $('#label').html(ui.item.label);
        $('#info').attr('src', '/out/pictures/'+ui.item.image);
        console.log(ui.item.image);
        if(ui.item.image != '' && ui.item.image != '1/nopic.jpg') $('.box').css('background', 'green');
        else $('.box').css('background', 'red');
      }
    });
     $( "#title" ).autocomplete({
      source: "/?cl=rescueimg&fnc=ajaxSearchTitle",
      minLength: 2,
      select: function( event, ui ) {        
        $(this).val(ui.item);
        $('#artid').val(ui.item.id);
        $('#label').html(ui.item.label);
        $('#artnum').val(ui.item.artnum);
        $('#info').attr('src', '/out/pictures/'+ui.item.image);
        if(ui.item.image != '' && ui.item.image != '1/nopic.jpg') $('.box').css('border-color', 'green');
        else $('.box').css('border-color', '#666');                        
      }
    });
    
    $('#deleteButton').click(function(e) {
      e.preventDefault();
      if (confirm('Sicher löschen?')) {
          $(location).attr('href', e.target);
      }
    });    
  });
</script>    
    
    </head>
    <h1>OXID Bildzuweisungen Wiederherstellen</h1>
    <h2>Es sind <? echo $iActPicCnt; ?> / <? echo count($aMatch); ?> Bilder zugewiesen.</h2>
    <?if(!$_GET['do'] && !$_POST['do']) { ?>
    <a class="btn" href="/?cl=rescueimg&do=startAutoImgageRescue">Automatische Bildrettung starten</a>&nbsp;&nbsp;&nbsp;
    <a class="btn" href="/?cl=rescueimg&do=startAdvImgageRescue">manuelle Bildrettung starten</a>
    <? } else if($_GET['do']) $this->$_GET['do']();
         else if($_POST['do']) $this->$_POST['do']();    
    ?>    
    <div id="footer">
      supported by <a href="http://www.suabo.de" target="_blank">suabo.de</a>
    </div>
    </body>
    </html>
    <?
    exit();
  }

  public function ajaxSearchTitle() {
    $oArtList = oxNew('oxList');
    $oArtList->init('oxarticle');
    $oArtList->SelectString("SELECT oxid, oxtitle, oxartnum, oxpic1 FROM oxarticles WHERE oxtitle LIKE '%{$_GET['term']}%' AND oxactive='1';");
    $aArticles = array();
    foreach($oArtList as $oArticle) {      
      $aArticle['id'] = $oArticle->oxarticles__oxid->value;
      $aArticle['label'] = $oArticle->oxarticles__oxtitle->value." - ".$oArticle->oxarticles__oxartnum->value;
      $aArticle['artnum'] = $oArticle->oxarticles__oxartnum->value;
      $aArticle['image'] = $oArticle->oxarticles__oxpic1->value;
      $aArticle['value'] = $oArticle->oxarticles__oxtitle->value;
      $aArticles[] = $aArticle; 
      
    }
    echo json_encode($aArticles);
    exit();
  }
  
  public function ajaxSearchArtNum() {
    $oArtList = oxNew('oxList');
    $oArtList->init('oxarticle');
    $oArtList->SelectString("SELECT oxid, oxtitle, oxartnum, oxpic1 FROM oxarticles WHERE oxartnum LIKE '%{$_GET['term']}%' AND oxactive='1';");
    $aArticles = array();
    foreach($oArtList as $oArticle) {      
      $aArticle['id'] = $oArticle->oxarticles__oxid->value;
      $aArticle['label'] = $oArticle->oxarticles__oxartnum->value." - ".$oArticle->oxarticles__oxtitle->value;
      $aArticle['artnum'] = $oArticle->oxarticles__oxartnum->value;
      $aArticle['image'] = $oArticle->oxarticles__oxpic1->value;
      $aArticle['value'] = $oArticle->oxarticles__oxartnum->value;
      $aArticles[] = $aArticle;      
    }
    echo json_encode($aArticles);
    exit();
  }
  
  public function startAdvImgageRescue() {    
    $aMatch = glob("out/pictures/master/1/*.*");        
    ?>
    <form action="/index.php" method="POST">
    <? 
    $iImgCnt = 0;
    if($iImgCnt = $_GET['jmpimg']) { for($i=0;$i<$iImgCnt;$i++) unset($aMatch[$i]);  }
       
    foreach($aMatch as $sImageName) {
      $iImgCnt++;
      $aPathInfo = pathinfo($sImageName);      
      $sArtNum = oxDb::getDb()->getOne("SELECT oxartnum FROM oxarticles WHERE oxpic1='".$aPathInfo['basename']."';");
      if($sArtNum) {
        $this->_aArticleWithImage[] = $sArtNum;
      } else {        
        ?>
        <div class="imgbox">          
          <img src="<? echo $sImageName; ?>" alt="">
          <div>
            <label>Bildname:</label> <? echo $aPathInfo['basename']; ?><br>
            <div class="box" style="margin-top:10px;">
              <label>Ausgewähltes Produkt:</label><br><br>
              <img id="info" src="" alt="">              
              <strong id="label"></strong><br>
            </div>
            <div style="float:left;margin-top:10px;width:390px;">
              <input type="hidden" name="cl" value="rescueimg">
              <input type="hidden" name="do" value="saveImageToArticle">
              <input type="hidden" id="artid" name="id" value="">
              <input type="hidden" name="image" value="<? echo $sImageName; ?>">
              <label>Artikelnummer:</label> <input id="artnum" type="text" name="artnum" value=""><br>
              <label>Titel:</label> <input id="title" type="text" name="title" value=""><br>
              <br>
            </div>
            <div style="float:left;margin-top:10px;width:100%;">
              <a class="btn" href="/?cl=rescueimg&do=startAdvImgageRescue&jmpimg=<? echo $iImgCnt; ?>">Bild überspringen</a>&nbsp;&nbsp;&nbsp;<a id="deleteButton" class="btn" href="/?cl=rescueimg&do=deleteImage&jmpimg=<? echo $iImgCnt; ?>&imgpath=<? echo $sImageName; ?>">Bild löschen</a><input class="btn" type="submit" value="Bild zu Artikel Speichern" style="float:right;">
            </div>
          </div>
          <div style="clear:both;"></div>
        </div>        
        <?
        break;                
      }
    }
    ?>
    </form>
    <?
  }
  
  public function deleteImage() {
    $sImagePath = $_GET['imgpath'];
    $iJumpToImage = $_GET['jmpimg'];
    if($sImagePath && file_exists($sImagePath)) {
      unlink($sImagePath);  
    }
    ?>
    <div class="imgbox">
      <strong>Bildpfad:</strong> <? echo $sImagePath; ?><br>
      Das Bild wurde erfolgreich gelöscht.<br>
      <br><br><br>
      <a class="btn" href="/?cl=rescueimg&do=startAdvImgageRescue&jmpimg=<? echo ($iJumpToImage-1); ?>" style="float:right;">Weiter mit nächstem Bild</a>   
    </div>
    <?
  }
  
  public function saveImageToArticle() {    
    $oArticle = oxNew('oxarticle');
    if($oArticle->load($_POST['id'])) {
      $aPathInfo = pathinfo($_POST['image']);
      $oArticle->oxarticles__oxpic1->value = $aPathInfo['basename'];
      $oArticle->save();
      ?>
      <div class="imgbox">
        <img src="/out/pictures/<? echo $oArticle->oxarticles__oxpic1->value; ?>" alt="">
        <div style="float:left;width:330px;margin-bottom:40px;">
          <label>Artikelnummer:</label><? echo $oArticle->oxarticles__oxartnum->value; ?><br>
          <strong><? echo $oArticle->oxarticles__oxtitle->value; ?></strong><br>
          Bild wurde erfolgreich gespeichert.          
        </div>        
        <a href="/?cl=rescueimg&do=startAdvImgageRescue" class="btn" style="float:right;">Weiter mit nächstem Bild</a>
        <div sytle="clear:both;"></div>
      </div>
      <?       
    }    
  }
  
  public function startAutoImgageRescue($blSaveFoundImages = true) {
    $oArtList = oxNew('oxList');
    $oArtList->init('oxarticle');
    $oArtList->SelectString("SELECT oxid, oxartnum, oxpic1 FROM oxarticles WHERE (oxpic1='' OR oxpic1='nopic.jpg') AND oxactive='1';");
    
    foreach($oArtList as $oArticle) {      
      if( $sImagePath = $this->searchImage($oArticle->oxarticles__oxartnum->value) ) {
        $aPathInfo = pathinfo($sImagePath);
        echo "Artnum: ". $oArticle->oxarticles__oxartnum->value." Image: ".$oArticle->oxarticles__oxpic1->value." Masterbild gefunden: ";
        echo $sImagePath."<br>";
        echo "<br>";
        if($blSaveFoundImages) {
          $oArticle->oxarticles__oxpic1->value = $aPathInfo['basename'];
          $oArticle->save();
        }
      }      
    }  
  }
  
  private function searchImage($sArtNum) {
    $aMatch = glob("out/pictures/master/1/".$sArtNum."*");
    if(count($aMatch) === 1) {
      //we got one
      return $aMatch[0];
    } else if(count($aMatch) > 1) {
      //we take the last one, seams to fit best here :D           
      return $aMatch[count($aMatch)-1];
    }    
  }
}
?>