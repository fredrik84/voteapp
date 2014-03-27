<table cellpadding=0 cellspacing=0 border=0 align=center>
<tr>
<td  style="padding-left: 35px">
   <pre style="margin-top:0px;font-family: 'Courier New', Courier, monospace; line-height: normal; text-align: left; border-top: 1px solid black;border-bottom: 1px solid black">
   <font size=2>
<?php 
   $tmp = explode("\\n", $this->query[0]);
   $out = preg_replace("/\\\\/", "", implode("\n", $tmp));
   echo $out."\n";
?>
   </font>
   </pre>
</td>
</tr>
</table>
