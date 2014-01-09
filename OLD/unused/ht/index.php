<?php 
// 
// +-----------------------------------+ 
// |        Image Filter v 1.0         | 
// |      http://www.SysTurn.com       | 
// +-----------------------------------+ 
// 
// 
//   This program is free software; you can redistribute it and/or modify 
//   it under the terms of the ISLAMIC RULES and GNU Lesser General Public 
//   License either version 2, or (at your option) any later version. 
// 
//   ISLAMIC RULES should be followed and respected if they differ 
//   than terms of the GNU LESSER GENERAL PUBLIC LICENSE 
// 
//   This program is distributed in the hope that it will be useful, 
//   but WITHOUT ANY WARRANTY; without even the implied warranty of 
//   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the 
//   GNU General Public License for more details. 
// 
//   You should have received a copy of the license with this software; 
//   If not, please contact support @ S y s T u r n .com to receive a copy. 
// 

  if(isset($_FILES['userimage'])) 
  { 
    require_once('filter.class.php'); 
    $filter = new ImageFilter; 
    $score = $filter->GetScore($_FILES['userimage']['tmp_name']); 
  } 
?> 
<html> 
<head> 
    <title>Image Filter Example</title> 
</head> 
<body> 
<?php if(isset($score)){ ?> 
    <?php if($score >= 30){ ?> 
    <div style="text-align:center; font-weight:bold; color:#DD0000">Image scored <?php echo $score ?>%<br/><br/>It seems that you have uploaded a nude picture :-(</div> 
    <?php } else { ?> 
    <div style="text-align:center; font-weight:bold; color:#008800">Image scored <?php echo $score ?>%<br/><br/>It seems that you have uploaded a good picture :-)</div> 
    <?php } ?> 
<?php } ?> 
<div align="center"> 
<form enctype="multipart/form-data" action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post"> 
 Filter this image: <input name="userimage" type="file" /> 
 <input type="submit" value="Filter" style="width:150px" /> 
</form> 
</div> 
</body> 
</html>
