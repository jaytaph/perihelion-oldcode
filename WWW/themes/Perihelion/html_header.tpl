<!DOCTYPE HTML PUBLIC '-//W3C//DTD HTML 4.01 Transitional//EN'>
<html>
  <head>
    <title>{$title|default:"Perihelion - The Game"}</title>
    
    <!-- All code copyright (C) 2004 Joshua Thijssen -->
    
    <meta http-equiv="Content-Style-Type" content="text/css">
    <link rel="stylesheet" href="{$css_path}" type="text/css">
          
    <basefont face="Arial" size="2" />
    
    {$extra_headers}

		{literal}
    <script type="text/javascript" language="javascript">
       function targetBlank (url) {
         blankWin = window.open(url,"_blank","width=640, height=400, menubar=no, toolbar=no, location=no, directories=no, fullscreen=no, titlebar=no, hotkeys=no, status=no, scrollbars=no, resizable=yes");       
       }
    </script>
    {/literal}

  </head>
  
  <body bgcolor="black" {$background|default:""} text="white" link="white" vlink="white" alink="white" {$body_tags}>

<!-- End Header -->
