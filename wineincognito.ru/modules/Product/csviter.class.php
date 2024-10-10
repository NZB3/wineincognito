<?php

class CSVIter{
	private $fhndl;
	private $buffer;
	public function open($fpath){
		if(!file_exists($fpath)){
			return false;
		}
		$this->fhndl = fopen($fpath,'r');
		$this->buffer = '';
		return true;
	}
	public function close(){
		$buffer = null;
		if($this->fhndl){
			@fclose($this->fhndl);
			$this->fhndl = null;
		}
	}
	public function get_row(){
		if(!$this->fhndl){
			return false;
		}
		while(!preg_match('#^[\s\S]+?(?:[";]|;[^"][^;]*)\r?\n#',$this->buffer,$match) && ($buffer = fgets($this->fhndl,4096))!==false){
			$this->buffer .= $buffer;
		}
		if($match){
			$raw_row = $match[0];
		} else {//EOF
			$raw_row = $this->buffer;
		}
		$this->buffer = substr($this->buffer,strlen($raw_row),strlen($this->buffer));
		$raw_row = trim($raw_row);
		$result = array();
		while(strlen($raw_row) && (preg_match('#^"([\s\S]*?)"(?:;|$)#',$raw_row,$match) || preg_match('#^([^;]*)(?:;|$)#',$raw_row,$match))){
			$result[] = trim($match[1]);
			$raw_row = substr($raw_row,strlen($match[0]),strlen($raw_row));
		}
		return $result;
	}
	function __destruct(){
		$this->close();
	}
}