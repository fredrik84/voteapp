   <!-- Main Content -->
      <section>
         <header>
            <h2>Contribute</h2>
            <h3></h3>
         </header>
         <p>
<?php
   if(!empty($this->query[0])) {
      if(empty($_SESSION['ean_id'])) {
         echo "To contribute you need to login with a valid EAN code."; 
      } elseif(empty($error_message)) {
?>

<form method=POST enctype="multipart/form-data" <?php if(!empty($this->_GET['contribution_id'])) { echo "action='?contribution_id=".$this->_GET['contribution_id']."'"; } ?>>
   <table cellpadding=0 cellspacing=0 border=0 width=100%>
      <tr>
         <td width=50%>
            <table cellpadding=0 cellspacing=0 border=0>
               <tr>
                  <td align=right style='padding-right: 15px'>Competition:</td>
                  <td>
                  <?php
                     foreach($this->query as $value) {
                        if($value->competition_id == $contrib->competition_id) {
                           if($value->submit_enabled == 0) {
                              $option = "disabled";
                              $current = $value->competition_id;
                              break;
                           }
                        }
                     }
                  ?>
                     <select name='competition_id' style='width: 206px' <?=$option?>>
                     <?php
                        if(!empty($competition_name))
                           echo "<option value='$contrib->competition_id' selected> $competition_name";
                        foreach($this->query as $value) {
                           if($value->submit_enabled == 0 && $current != $value->competition_id)
                              continue;
                           if($value->competition_id == $contrib->competition_id)
                              echo "<option value='$value->competition_id' selected> $value->name\n";
                           else
                              echo "<option value='$value->competition_id'> $value->name\n";
                        }
                     ?>
                     </select>
                  </td>
               </tr>
               <tr>
                  <td align=right style='padding-right: 15px'>Artist(s):</td>
                  <td><input type="text" name="contributer" value="<?=$contrib->contributer?>" style='width: 200px' <?=$option?>></td>
               </tr>
               <tr>
                  <td align=right style='padding-right: 15px'>Production title:</td>
                  <td><input type="text" name="entry_name" value="<?=$contrib->entry_name?>" style='width: 200px' <?=$option?>></td>
               </tr>
               <tr>
                  <td align=right style='padding-right: 15px'>Beamer information</td>
                  <td><input type="text" name="beamer_info" value="<?=$contrib->beamer_info?>" style='width: 200px' <?=$option?>></td>
               </tr>
               <tr>
                  <td align=right style='padding-right: 15px'>Information to organizers</td>
                  <td><input type="text" name="description" value="<?=$contrib->description?>" style='width: 200px' <?=$option?>></td>
               </tr>
               <tr>
                  <td align=right style='padding-right: 15px'></td>
                  <td>
                     <?php if(!empty($this->_GET['contribution_id'])) {?><input type="hidden" name="contribution_id" value="<?=$this->_GET['contribution_id']?>"><?php } ?>
                     <?php if(!empty($this->_GET['contribution_id'])) {?><input type="hidden" name="action" value="edit"><?php } ?>
                     <input type="submit" name="submit" value="Contribute" <?=$option?>>
                  </td>
               </tr>
            </table>
         </td>
         <td>
            <table cellpadding=0 cellspacing=0 border=0>
               <tr>
                  <td align=right style='padding-right: 15px'></td>
                  <td>
<?php
   if(!empty($contrib) && $option != "disabled") {
?>
                     <script src="<?=$this->base_url;?>js/jquery/jquery.min.js"></script>
                     <script src="<?=$this->base_url;?>js/fileupload/vendor/jquery.ui.widget.js"></script>
                     <script src="<?=$this->base_url;?>js/fileupload/jquery.iframe-transport.js"></script>
                     <script src="<?=$this->base_url;?>js/fileupload/jquery.fileupload.js"></script>
                     <script type="text/javascript">
                        /*jslint unparam: true */
                        /*global window, $ */
                        $(function(){
                           $('#fileupload').fileupload({
                              url: 'http://<?=$_SERVER['HTTP_HOST'].$this->base_url."upload/"?>', 
                              dataType: 'json',
                              done: function (e, data) {
                                 $.each(data.result.files, function (index, file) {
                                    $('<p/>').text(file.name).appendTo('#files');
                                    $('#progress .progress-bar').css(
                                       'width',
                                       '100%'
                                    );
                                 });
                              },
                              progressall: function (e, data) {
                                 var progress = parseInt(data.loaded / data.total * 100, 10);
                                 $('#progress .progress-bar').css(
                                    'width',
                                    progress + '%'
                                 );
                              }
                           }).prop('disabled', !$.support.fileInput)
                              .parent().addClass($.support.fileInput ? undefined : 'disabled');
                           $('#fileupload2').fileupload({
                              url: 'http://<?=$_SERVER['HTTP_HOST'].$this->base_url."upload/"?>', 
                              dataType: 'json',
                              done: function (e, data) {
                                 $.each(data.result.files, function (index, file) {
                                    $('<p/>').text(file.name).appendTo('#files');
                                    $('#progress .progress-bar').css(
                                       'width',
                                       '100%'
                                    );
                                 });
                              },
                              progressall: function (e, data) {
                                 var progress = parseInt(data.loaded / data.total * 100, 10);
                                 $('#progress .progress-bar').css(
                                    'width',
                                    progress + '%'
                                 );
                              }
                           }).prop('disabled', !$.support.fileInput)
                              .parent().addClass($.support.fileInput ? undefined : 'disabled');

                           });
                     </script>

                      <span class="btn btn-success fileinput-button">
                          <i class="glyphicon glyphicon-plus"></i>
                          <span>select files for contribution...</span>
                          <!-- the file input field used as target for the file upload widget -->
                          <input id="fileupload" type="file" name="files" data-url="<?=$this->base_url?>Upload/?contribution_id=<?=$this->_GET['contribution_id']?>">
                      </span>
                      <!-- the global progress bar -->
                            <span class="btn btn-success fileinput-button">
                          <i class="glyphicon glyphicon-plus"></i>
                          <span>select thumbnail...</span>
                          <!-- the file input field used as target for the file upload widget -->
                          <input id="fileupload2" type="file" name="thumbnail" data-url="<?=$this->base_url?>Upload/?contribution_id=<?=$this->_GET['contribution_id']?>">
                      </span>
                      <br>
                      <br>
                      <div id="progress" class="progress">
                          <div class="progress-bar progress-bar-success"></div>
                      </div><br>
                     <div id="files" class="files"></div>

                     </form>

<!--                      <span class="btn btn-success fileinput-button">
                          <i class="glyphicon glyphicon-plus"></i>
                          <span>select thumbnail for your entry...</span>
                          <input id="fileupload" type="file" name="thumbnail">
                      </span>
                      <div id="progress2" class="progress">
                          <div class="progress-bar progress-bar-success"></div>
                      </div><br>
-->
<?php
   }
?>
                  </td>
               </tr>
            </table>
         </td>
      </tr>
   </table>
   <br><br>
<?php if(!empty($this->_GET['contribution_id'])) { ?>
   <table cellpadding=0 cellspacing=0 border=0 align=center>
      <tr>
         <td width=400>Uploaded file</td>
         <td width=150></td>
         <td></td>
      </tr>
      <tr>
         <td style="vertical-align: top">
         <?php if(!empty($contrib->filename)) { ?>
            <a href='<?=$this->filename_path?>' target="_new"><?=$contrib->filename?></a>
         <?php } else { ?>
            None
         <?php } ?>
         </td>
         <td>
         <?php if(!empty($contrib->thumbnail_filename)) { ?>
            <script type="text/javascript" src="<?=$this->base_url?>js/fancybox/jquery.fancybox.js?v=2.1.5"></script>
            <script type="text/javascript">
               $(document).ready(function() {
                  $(".fancybox").fancybox({
                     openEffect  : 'none',
                     closeEffect : 'none'
                  });
               });
            </script>
            <a class='fancybox' data-fancybox-group='tmp' title='<?=$contrib->entry_name?>' href='<?=$this->thumbnail?>'>Thumbnail</a>
         <?php } ?> 
         </td>
         <td>
         <?php if(!empty($this->_GET['contribution_id'])) { ?>
            <a href="?delete=<?=$this->_GET['contribution_id']?>">Delete contribution</a>
         <?php } ?>
         </td>
      </tr>
   </table>
<?php } ?>
<br>
<?php 
   if(!empty($error_message)) { 
      foreach($error_message as $value) {
         echo "<font color=red>$value</font>";
      }
   } 
?>

         </p>
      </section>
</div>
<div class="3u">
<?php
   $json_array = array("call" => "competitions",
                       "action" => "get",
                       "event_id" => $this->event_id);
   $this->queryApi($json_array);
   unset($competitions);
   foreach($this->query as $value)
      $competitions[$value->competition_id] = $value->name;
   foreach($this->query as $key => $value) 
?>
   <!-- Sidebar -->
      <section>
         <header>
            <h2>My contributions</h2>
         </header>
         <ul class="link-list">
            <?php
               foreach($contributions as $key => $value) 
                  echo "<li>(".$competitions[$value->competition_id].") <a href='?contribution_id=$value->contribution_id'>$value->entry_name</a></li>";
            ?>
         </ul>
      </section>
<?php
      } else {
         foreach($error_message as $value) {
            echo "$value\n<br>";
         }
      }
   } else {
      echo "No competitions available right now";
   }
?>
