<form method="POST">
<div align="center">
   <h2><?=$this->_CONFIG['website_title'];?> Login</h2>
   <table cellpadding="0" cellspacing="0" border="0">
      <tr>
         <td width=125 style="vertical-align: middle;padding-top: 2px"><h3>Username</h3></td>
         <td style="vertical-align: top"><input type="text" name="username"></td>
      </tr>
      <tr>
         <td width=125 style="vertical-align: middle;padding-top: 2px"><h3>Password</h3></td>
         <td style="vertical-align: top"><input type="password" name ="password"></td>
      </tr>
   </table>
   <input type="submit" name="submit" value="Login" style="width: 200px; height: 40px">
   <?php if(!empty($error_message) && !empty($this->_POST['username'])) { ?>
      <h3><font color=red><?=$error_message?></font></h3>
   <?php } ?>
</div>
</form>
