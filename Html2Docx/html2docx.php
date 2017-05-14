<?php

namespace Naifu\Html2docx;

class Html2docx {
	
    public $pandoc_parameter = ' -s -S ';
    public $allowed_mime_types = array(
		'text/plain',
        'text/html',
        'text/html',
        'text/html',
        'text/css',
        'application/javascript',
        'application/xml'
    );
    public $pandoc_path = '';
    public $output_dir = '';
    public $output_file = '';
    public $html_file = '';
    public $html_content = '';


	function __construct(){

	}

	/* initiating */
	public function assign_pandoc($pandoc_path){

		if(!file_exists($pandoc_path)){die('Pandoc does not exist');}
		$this->pandoc_path = $pandoc_path;

	}

	public function output_path($output_dir){

		if(!is_dir($output_dir)){mkdir($output_dir) || die("Error in generating output dir.");}

		//create output file
		$temp_name = tempnam($this->output_dir, 'tmp_docx_');
		unlink($temp_name);
		$temp_name .= '.docx';
		$this->output_file = $temp_name;
	}

	public function output_file($output_file){

		$path_info = pathinfo($output_file);
		if(!is_dir($path_info['dirname'])){die('Output directroy is not valid');}
		$this->output_file = $output_file;
		$this->output_dir = $path_info['dirname'];
	}

	public function input_html_file($input_file){

		if(!file_exists($input_file)){die('HTML file does not exist');}
		if(!in_array(mime_content_type($input_file),$this->allowed_mime_types)){die('Input file type wrong');}
		$this->html_file = $input_file;			
	}

	public function input_html_content($input_content){

		if(empty($input_content)){die('Empty input.');}

		// utf8_encode($input_content);
		$input_content = $this->clean_script($input_content);
		$input_content = $this->clean_style($input_content);
		$input_content = $this->clean_encoding($input_content);
	// $input_content = $this->image_process($input_content);
	// $input_content = $this->math_process($input_content);

		$this->html_content = $input_content;
	}


	/* converting */
	public function convert_from_file($delete_input=false){

		if(!$this->pandoc_path){die('Need to tell me where is the pandoc.');}
		if(!$this->output_file){die('Need to tell me where should I put the outputfile.');}
		if(!$this->html_file){die('Need to tell me where is the input file.');}

		$content = file_get_contents($this->html_file);

		$output = $this->convert_from_html($content);
		if($delete_input){unlink($this->html_file);}
	return $output;
	}

	public function convert_from_html($content=null){

		if(!$content){$content = $this->html_content;}

		$html_input = tempnam($this->output_dir, 'tem_html');
		$fh1 = fopen($html_input,'w');
		fwrite($fh1, $content);
		fclose($fh1);
		rename($html_input, $html_input.='.html');

		$input = $html_input;
		$output = $this->output_file;
	chdir(pathinfo($this->pandoc_path,PATHINFO_DIRNAME));
		$cmd = basename($this->pandoc_path) . $this->pandoc_parameter . " -o $output " . " $input";
		// echo $cmd;
		exec($cmd);
	unlink($input);
		return $output;

	}

	protected function clean_script($html_content){

		$html_content = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $html_content);
		return $html_content;

	}

	protected function clean_style($html_content){

		$html_content = preg_replace('#<style(.*?)>(.*?)</style>#is', '', $html_content);
		return $html_content;

	}


	protected function clean_encoding($html_content){

		//todo [xe4,xbd]
		// $html_content = urldecode($html_content); //take a lot time this way
		// $html_content = html_entity_decode($html_content,ENT_QUOTES);				
		// $html_content = str_replace("\xA0", " ", $html_content);
		return $html_content;

	}

	protected function image_process($html_content){
		//convert image to base64 for converting

		// preg_match_all("/(\s*src=[\"\']\s*)([^\"\']*\s*)([\"\'])/", $html_content, $matches);
		// if (!empty($matches)){
		// 	foreach($matches[0] as $key=>$value){
		// 		//match 'http:\\'
		// 		if(preg_match('/http[^\"\']*/',$value,$match)){$path = $match[0];}

		// 		//match '\\'
		// 		elseif(preg_match('/\/\/[^\"\']*/',$value,$match)){$path = 'http:' . $match[0];}

		// 		//match '\'
		// 		elseif(preg_match('/\/[^\"\']*/',$value,$match)){$path = $_SERVER['DOCUMENT_ROOT'] . $match[0];}
		// 		else{$path = NULL;}
		// 		if (isset($path)){
		// 			$image_data = file_get_contents($path);
		// 			$image_type = pathinfo($path, PATHINFO_EXTENSION);
		// 			$base64 = ' src="data:image/' . $image_type . ';base64,' . base64_encode($image_data) . '"';
		// 			$html_content = str_replace($value,$base64,$html_content);
		// 		}
		// 	}
		// }

		return $html_content;
	}

	protected function math_process($html_content){
		//convet latex to mathml

		// preg_match_all("/(<span\s+class=\"ct-mathjax-latex\".*\>)(.*)(<\/span\>)/",$html_content,$ma);
		// if (!empty($ma)){
		// 	foreach($ma[2] as $key=>$value){
		// 		$mathml = latex2mathml_by_latexml($value);
		// 		$html_content = str_replace($value, $mathml, $html_content);
		// 	}
		// }
		return $html_content;
	}

	protected function latex2mathml_by_latexml($latex){

		/**
		 * convert latex to mathml
		 * @input: string, latex format
		 * @return: string, mathml format
		 */

		// if (!defined(CMD_LATEX2MATHML)) define(CMD_LATEX2MATHML, ' latexmlmath  --presentationmathml=');

		// //create tmp folder to temporary save
		// $dir = $_SERVER['DOCUMENT_ROOT'] . '/' . 'tmp';
		// if (!is_dir($dir)){$st = (mkdir($dir, 0775) || die("Error in generating tmp folder"));}

		// //create tmp file name
		// $output_mathml = tempnam($dir, 'tmp_mathml');
		// unlink($output_mathml);
		// $output_mathml .= '.xml';

		// $cmd_latex_post = CMD_LATEX2MATHML . $output_mathml . " '" . $latex . "'" ;
		// exec($cmd_latex_post);

		// $result = file_get_contents($output_mathml);
		// unlink($output_mathml);

		// return $result;

	}



}
