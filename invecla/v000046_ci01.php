<?php
include_once("conex.php");
if(isset($registro[4]) and $registro[4] != "NO APLICA" )
{

   $tot=1;
   $por=0;
   $can=0;

   $pp=explode('-',$registro[4]);

   IF ($pp[0]=='01') 
    {
	  $tot=9;
	   
	if ($registro[5]=='on')
	  {
	   $can=$can+1;
	  }
    if ($registro[6]=='on')
	  {
	   $can=$can+1;
	  }
    if ($registro[7]=='on')
	  {
	   $can=$can+1;
	  }
    if ($registro[8]=='on')
	  {
	   $can=$can+1;
	  }
    if ($registro[9]=='on')
	  {
	   $can=$can+1;
	  }
    if ($registro[10]=='on')
	  {
	   $can=$can+1;
	  }
    if ($registro[11]=='on')
	  {
	   $can=$can+1;
	  }
    if ($registro[12]=='on')
	  {
	   $can=$can+1;
	  }  
    if ($registro[13]=='on')
	  {
	   $can=$can+1;
	  }
    if ($registro[14]=='on')
	  {
	   $can=$can+1;
	  }
    if ($registro[15]=='on')
	  {
	   $can=$can+1;
	  }
    if ($registro[16]=='on')
	  {
	   $can=$can+1;
	  }
    if ($registro[17]=='on')
	  {
	   $can=$can+1;
	  }
    if ($registro[18]=='on')
	  {
	   $can=$can+1;
	  }
    if ($registro[19]=='on')
	  {
	   $can=$can+1;
	  }
    if ($registro[20]=='on')
	  {
	   $can=$can+1;
      }
    if ($registro[21]=='on')
	  {
	   $can=$can+1;
	  }
    if ($registro[22]=='on')
	  {
	   $can=$can+1;
	  }
    if ($registro[23]=='on')
	  {
	   $can=$can+1;
	  }
    
   }
   ELSE
   {
     if (($pp[0]=='02') or ($pp[0]=='03') or ($pp[0]=='04')) 
      {
       $tot=10;
       
      if ($registro[5]=='on')
	  {
	   $can=$can+1;
	  }
      if ($registro[6]=='on')
	  {
	   $can=$can+1;
	  }
      if ($registro[7]=='on')
	  {
	   $can=$can+1;
	  }
      if ($registro[8]=='on')
	  {
	   $can=$can+1;
	  }
      if ($registro[9]=='on')
	  {
	   $can=$can+1;
	  }
      if ($registro[10]=='on')
	  {
	   $can=$can+1;
	  }
      if ($registro[11]=='on')
	  {
	   $can=$can+1;
	  }
      if ($registro[12]=='on')
	  {
	   $can=$can+1;
	  }
      if ($registro[13]=='on')
	  {
	   $can=$can+1;
	  }
      if ($registro[14]=='on')
	  {
	   $can=$can+1;
	  }
      if ($registro[15]=='on')
	  {
	   $can=$can+1;
	  }
      if ($registro[16]=='on')
	  {
	   $can=$can+1;
	  }
      if ($registro[17]=='on')
	  {
	   $can=$can+1;
	  }
      if ($registro[18]=='on')
	  {
	   $can=$can+1;
	  }
      if ($registro[19]=='on')
	  {
	   $can=$can+1;
	  }
      if ($registro[20]=='on')
	  {
	   $can=$can+1;
      }
      if ($registro[21]=='on')
	  {
	   $can=$can+1;
	  }
      if ($registro[22]=='on')
	  {
	   $can=$can+1;
	  }
      if ($registro[23]=='on')
	  {
	   $can=$can+1;
	  }
      
     } 
     else
     {
      if (($pp[0]=='05') or ($pp[0]=='06'))
       {
       $tot=11;
      if ($registro[5]=='on')
	  {
	   $can=$can+1;
	  } 
      if ($registro[6]=='on')
	  {
	   $can=$can+1;
	  }
      if ($registro[7]=='on')
	  {
	   $can=$can+1;
	  }
      if ($registro[8]=='on')
	  {
	   $can=$can+1;
	  }
      if ($registro[9]=='on')
	  {
	   $can=$can+1;
	  }
      if ($registro[10]=='on')
	  {
	   $can=$can+1;
	  }
      if ($registro[11]=='on')
	  {
	   $can=$can+1;
	  }
      if ($registro[12]=='on')
	  {
	   $can=$can+1;
	  }
      if ($registro[13]=='on')
	  {
	   $can=$can+1;
	  }
      if ($registro[14]=='on')
	  {
	   $can=$can+1;
	  }
      if ($registro[15]=='on')
	  {
	   $can=$can+1;
	  }
      if ($registro[16]=='on')
	  {
	   $can=$can+1;
	  }
      if ($registro[17]=='on')
	  {
	   $can=$can+1;
	  }
      if ($registro[18]=='on')
	  {
	   $can=$can+1;
	  }
      if ($registro[19]=='on')
	  {
	   $can=$can+1;
	  }
      if ($registro[20]=='on')
	  {
	   $can=$can+1;
      }
      if ($registro[21]=='on')
	  {
	   $can=$can+1;
	  }
      if ($registro[22]=='on')
	  {
	   $can=$can+1;
	  }
      if ($registro[23]=='on')
	  {
	   $can=$can+1;
	  }
     }
     else
      {
     if (($pp[0]=='07') or ($pp[0]=='08') or ($pp[0]=='09'))
      {
      $tot=12;
     
      if ($registro[5]=='on')
	 {
	  $can=$can+1;
	 }   
	 if ($registro[6]=='on')
	 {
	  $can=$can+1;
	 }
    if ($registro[7]=='on')
	  {
	   $can=$can+1;
	  }
    if ($registro[8]=='on')
	  {
	   $can=$can+1;
	  }
    if ($registro[9]=='on')
	  {
	   $can=$can+1;
	  }
    if ($registro[10]=='on')
	  {
	   $can=$can+1;
	  }
    if ($registro[11]=='on')
	  {
	   $can=$can+1;
	  }
    if ($registro[12]=='on')
	  {
	   $can=$can+1;
	  }
    if ($registro[13]=='on')
	  {
	   $can=$can+1;
	  }
    if ($registro[14]=='on')
	  {
	   $can=$can+1;
	  }
    if ($registro[15]=='on')
	  {
	   $can=$can+1;
	  }
    if ($registro[16]=='on')
	  {
	   $can=$can+1;
	  }
    if ($registro[17]=='on')
	  {
	   $can=$can+1;
	  }
    if ($registro[18]=='on')
	  {
	   $can=$can+1;
	  }
    if ($registro[19]=='on')
	  {
	   $can=$can+1;
	  }
    if ($registro[20]=='on')
	  {
	   $can=$can+1;
      }
    if ($registro[21]=='on')
	  {
	   $can=$can+1;
	  }
    if ($registro[22]=='on')
	  {
	   $can=$can+1;
	  }
    if ($registro[23]=='on')
	  {
	   $can=$can+1;
	  }
     } 
     ELSE
     {
     if (($pp[0]=='10') or ($pp[0]=='13'))
      {
       $tot=14;
     if ($registro[5]=='on')
	  {
	   $can=$can+1;
	  }  
     if ($registro[6]=='on')
	  {
	   $can=$can+1;
	  }
    if ($registro[7]=='on')
	  {
	   $can=$can+1;
	  }
    if ($registro[8]=='on')
	  {
	   $can=$can+1;
	  }
    if ($registro[9]=='on')
	  {
	   $can=$can+1;
	  }
    if ($registro[10]=='on')
	  {
	   $can=$can+1;
	  }
    if ($registro[11]=='on')
	  {
	   $can=$can+1;
	  }
    if ($registro[12]=='on')
	  {
	   $can=$can+1;
	  }
    if ($registro[13]=='on')
	  {
	   $can=$can+1;
	  }
    if ($registro[14]=='on')
	  {
	   $can=$can+1;
	  }
    if ($registro[15]=='on')
	  {
	   $can=$can+1;
	  }
    if ($registro[16]=='on')
	  {
	   $can=$can+1;
	  }
    if ($registro[17]=='on')
	  {
	   $can=$can+1;
	  }
    if ($registro[18]=='on')
	  {
	   $can=$can+1;
	  }
    if ($registro[19]=='on')
	  {
	   $can=$can+1;
	  }
    if ($registro[20]=='on')
	  {
	   $can=$can+1;
      }
    if ($registro[21]=='on')
	  {
	   $can=$can+1;
	  }
    if ($registro[22]=='on')
	  {
	   $can=$can+1;
	  }
    if ($registro[23]=='on')
	  {
	   $can=$can+1;
	  }
     }  
     else //10
     {
      if ($pp[0]=='11')
      {
       $tot=18;
     if ($registro[5]=='on')
	  {
	   $can=$can+1;
	  }  
     if ($registro[6]=='on')
	  {
	   $can=$can+1;
	  }
    if ($registro[7]=='on')
	  {
	   $can=$can+1;
	  }
    if ($registro[8]=='on')
	  {
	   $can=$can+1;
	  }
    if ($registro[9]=='on')
	  {
	   $can=$can+1;
	  }
    if ($registro[10]=='on')
	  {
	   $can=$can+1;
	  }
    if ($registro[11]=='on')
	  {
	   $can=$can+1;
	  }
    if ($registro[12]=='on')
	  {
	   $can=$can+1;
	  }
    if ($registro[13]=='on')
	  {
	   $can=$can+1;
	  }
    if ($registro[14]=='on')
	  {
	   $can=$can+1;
	  }
    if ($registro[15]=='on')
	  {
	   $can=$can+1;
	  }
    if ($registro[16]=='on')
	  {
	   $can=$can+1;
	  }
    if ($registro[17]=='on')
	  {
	   $can=$can+1;
	  }
    if ($registro[18]=='on')
	  {
	   $can=$can+1;
	  }
    if ($registro[19]=='on')
	  {
	   $can=$can+1;
	  }
    if ($registro[20]=='on')
	  {
	   $can=$can+1;
      }
    if ($registro[21]=='on')
	  {
	   $can=$can+1;
	  }
    if ($registro[22]=='on')
	  {
	   $can=$can+1;
	  }
    if ($registro[23]=='on')
	  {
	   $can=$can+1;
	  }
     }
     else
     {
      if ($pp[0]=='12')
      {
       $tot=19;
     if ($registro[5]=='on')
	  {
	   $can=$can+1;
	  }  
     if ($registro[6]=='on')
	  {
	   $can=$can+1;
	  }
    if ($registro[7]=='on')
	  {
	   $can=$can+1;
	  }
    if ($registro[8]=='on')
	  {
	   $can=$can+1;
	  }
    if ($registro[9]=='on')
	  {
	   $can=$can+1;
	  }
    if ($registro[10]=='on')
	  {
	   $can=$can+1;
	  }
    if ($registro[11]=='on')
	  {
	   $can=$can+1;
	  }
    if ($registro[12]=='on')
	  {
	   $can=$can+1;
	  }
    if ($registro[13]=='on')
	  {
	   $can=$can+1;
	  }
    if ($registro[14]=='on')
	  {
	   $can=$can+1;
	  }
    if ($registro[15]=='on')
	  {
	   $can=$can+1;
	  }
    if ($registro[16]=='on')
	  {
	   $can=$can+1;
	  }
    if ($registro[17]=='on')
	  {
	   $can=$can+1;
	  }
    if ($registro[18]=='on')
	  {
	   $can=$can+1;
	  }
    if ($registro[19]=='on')
	  {
	   $can=$can+1;
	  }
    if ($registro[20]=='on')
	  {
	   $can=$can+1;
      }
    if ($registro[21]=='on')
	  {
	   $can=$can+1;
	  }
    if ($registro[22]=='on')
	  {
	   $can=$can+1;
	  }
    if ($registro[23]=='on')
	  {
	   $can=$can+1;
	  }		
     }
     }
    }
    }
   }
  }  
 }

     $por=(($can/$tot)*100);

     $registro[$i]= $can; 
     
     $registro[$i+1]= $por;
			
}

?>