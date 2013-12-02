<?php
if ($_SERVER[QUERY_STRING] == "topframe") {
echo("<html><head><title>$title</title></head><body bgcolor=#d3d3d3><div align=center>If you see this text and your site - <i>and didn't get any pop ups or message boxes</i> - your site will probably be approved...</div></body></html>");
} else {
echo("<html><head><title>$title</title></head><frameset rows=20,* border=0><frame marginheight=0 marginwidth=0 scrolling=no noresize border=0 src=./urltest.php?topframe><frame marginheight=0 marginwidth=0 scrolling=auto noresize border=0 src=\"$_GET[url]\"></frameset></html>");
}
?>
