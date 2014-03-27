<form method="POST" <?php if(!empty($this->_GET['return'])) { ?> action='?return=<?=$this->_GET['return']?>' <?php } ?>>
<div align="center">
   <h2><?=$this->_CONFIG['website_title'];?> login</h2>
   <table cellpadding="0" cellspacing="0" border="0">
      <tr>
         <td width=125 style="vertical-align: middle;padding-top: 0px"><h3>Bracelet code: </h3></td>
         <td style="vertical-align: top"><input type="text" name="ean_code"></td>
      </tr>
   </table><br>
   <input type="submit" name="submit" value="Login" style="width: 200px; height: 40px">
   <?php if(!empty($error_message) && !empty($this->_POST['submit'])) { ?>
      <h3><font color=red><?=$error_message?></font></h3>
   <?php } ?>
</div>
</form>
