<?php
$content = file_get_contents('final_fix_check.txt');
// Convert encoding if needed, or just print
// PowerShell > often makes UTF-16
$content = mb_convert_encoding($content, 'UTF-8', 'UTF-16LE');
echo $content;
