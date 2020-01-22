<!DOCTYPE html>
<html>
<head>
    <title></title>
  <style type="text/css">
    table{
      border-collapse: collapse;
      border: 1px solid gray;
      width: 100%
    }
      th{
        border: 1px solid gray;
       color: black;
       text-align: center;
      /* text-transform: uppercase;*/
       font-weight: bold;
      }
      td{
        border: 1px solid gray;
         color: black;
       text-align: center;
      }
      .thwidth:nth-child(2){
        width:30%;
      }
  </style>
</head>
<body>
 
<div class="row"> 
   <div class="row">
         <div class="col-lg-12">
                  <h2 style="text-align: center;">RSB Foundation</h2> 
         </div> 

          <h3 >Report Of : <?php echo $title; ?></h3>       
    </div>
<hr>

    <div class="row">
         <div class="col-lg-12">
          <table style="padding: 3px;">
        <thead >
                <tr >
                    <th width="8%">Sr. No.</th>
                    <?php 
                        foreach ($tablehead as $key => $value) {

                        ?>
                            <th ><?php echo $value; ?></th>
                        <?php
                        }
                     ?>
                </tr>
        </thead>
        <hr>
<tbody>
    <?php 
    $count=1;
        foreach ($result as $key => $value) {
            ?>
            <tr>
            <?php
              echo '<td  width="8%">'.$count.'</td>';
           foreach ($value as $key1 => $value1) {
            ?>
                <td class="color">
                    <?php
                      if(strpos($value1,".png")) 
                      {
                        ?>
                        <img src="<?php echo $value1; ?>" width="200" height="100x" alt="No Image"/>
                        <?php
                      }
                      else{
                       echo $value1;
                     } 
                    ?>
                </td>
            <?php 
           }
           ?>
       </tr>
           <?php
           $count++;
        }
     ?>
</tbody>
     </table>
 </div><!-- //table col 12 -->

    </div> <!-- //Table row  -->
</div><!-- /.row -->
<hr>
<p style="float: right;">RSB FOUNDATION</p>
</body>
</html>