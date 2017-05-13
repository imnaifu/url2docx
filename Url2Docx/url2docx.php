<?php
namespace Naifu\Url2Docx;
use Naifu\Html2docx;

class Url2Docx {

	public $urls = array();
	protected $pandoc_path = 'C:\Program Files (x86)\Pandoc\pandoc.exe';
	protected $output_path = 'D:\html2docx_test';

	function __construct(){

	}

	public function add_pandoc($pandoc_path){
		$this->pandoc_path = $pandoc_path;
	}

	public function add_output_path($output_path){
		$this->output_path = $output_path;
	}

	public function push_url($url){
		
		if (!filter_var($url, FILTER_VALIDATE_URL)){die('Url not valid.');}

		$parse = parse_url($url);
		$parse['url'] = $url;
		//[todo] change host to domain name
		$output_file = tempnam($this->output_path, 'Url2Html_');
		if ($output_file){
			$parse['output_file'] = $output_file.'.docx';
			unlink($output_file);		
		}
		else {die('Error create output file.');}
		array_push($this->urls, $parse);
		print_r($this->urls);
	}


	public function download_docx(){
		//[todo] now only support 1 download
		foreach ($this->urls as $key => $parse) {
			$html_content = $this->get_html_from_url($parse['url']);

			// print_r($parse);
			// print_r($html_content);
			$converter = new Html2docx\Html2docx();
			$converter->assign_pandoc($this->pandoc_path);
			$converter->input_html_content($html_content);
			$converter->output_file($parse['output_file']);
			$docx_file = $converter->convert_from_html();

			$this->download_docx_init($docx_file);
			$this->download_file($docx_file);
		}
		unset($this->urls);
	}

	protected function get_html_from_url($url){
		//[todo] https not working
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);  
		// curl_setopt($ch, CURLOPT_HEADER, false);  
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    		'Content-Type: text/html; charset=UTF-8',
    		'Accept-Language: en-US,en;q=0.5'
    	));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //echo if 0  
		$html = curl_exec($ch);
		curl_close($ch);
		return $html;
	
	}

	protected function download_docx_init($file){

		header('Content-Description: File Transfer');
		header('Content-Type: application/octet-stream');
		header("Content-Disposition: attachment; filename=\"$file\"");
		header('Content-Transfer-Encoding: binary');
		header('Expires: 0');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Pragma: public');
		header('Content-Length: ' . filesize($file));
	}

	protected function download_file($file, $delete_file=null){

		ob_clean();
		flush();
		$status = readfile($file);
		if ($delete_file){unlink($file);}
		exit;
	}

}