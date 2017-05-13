# url2docx
A PHP component using Pandoc to generate docx file from url

### Example
~~~ 
<?php
use Naifu\Url2dDocx;

$con = new Url2docx();
$con -> add_pandoc('your/pandoc/path/here');
$con -> add_output_path('your/output/path/here');
$con -> push_url('http://url.you.wanna.com');
$your_file = $con -> download_docx();


