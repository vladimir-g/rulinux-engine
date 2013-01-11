<?php

  /* 
   * This class used to create images from LaTeX markup.
   * 
   * There are two available methods to convert tex to png:
   * - latex -> dvips -> imagemagick. This method crates images with best quality.
   * - latex -> dvipng. This method creates images with slightly worse quality.
   * 
   * Usage:
   * $math = new LaTeXMark();
   * if ($math->is_available())
   *    $img = $math->make_image($text);
   */
class LaTeXMark
{
	private $latex_path = null;
	private $convert_path = null;
	private $dvips_path = null;
	private $dvipng_path = null;
	private $log_file = null;
	private $tmp_dir = null;
	private $img_dir = null;
	private $img_url = null;

	/* From https://github.com/marklundeberg/dokuwiki-plugin-latex/blob/master/class.latexrender.php */
	public $tags_blacklist = array(
		"include","def","command","loop","repeat","open","toks","output","input",
		"catcode","name","^^",
		"\\every","\\errhelp","\\errorstopmode","\\scrollmode","\\nonstopmode","\\batchmode",
		"\\read","\\write","csname","\\newhelp","\\uppercase", "\\lowercase","\\relax","\\aftergroup",
		"\\afterassignment","\\expandafter","\\noexpand","\\special"
		);
	public $max_len = 2048;
	
	private $tpl = <<<EOT
	\\documentclass{report}
	\\pagestyle{empty}
        \\usepackage[T2A]{fontenc}
        \\usepackage[utf8]{inputenc}
        \\usepackage[russian]{babel}
	\\usepackage{amsmath}
	\\usepackage{amsfonts}
	\\usepackage{amssymb}
	\\usepackage{color}
	\\begin{document}
	{{ TEXT }}
	\\end{document}
EOT;
	
        public function __construct($log_file=null, $img_dir=null, 
				    $tmp_dir=null, $img_url=null)
	{
		/* Get path for all binaries */
		$this->latex_path = $this->get_path('latex');
		$this->convert_path = $this->get_path('convert');
		$this->dvips_path = $this->get_path('dvips');
		$this->dvipng_path = $this->get_path('dvipng');
		$this->dirname = null;

		if ($log_file !== null)
			$this->log_file = fopen($log_file, 'ab');

		$this->tmp_dir = $_SERVER["DOCUMENT_ROOT"].'/tmp/latex/';
		if (!empty($tmp_dir))
			$this->tmp_dir = $tmp_dir;
		if (!file_exists($this->tmp_dir))
			mkdir($this->tmp_dir, 0775, true);

		$this->img_dir = $_SERVER["DOCUMENT_ROOT"].'/images/formulas/';
		if (!empty($img_dir))
			$this->img_dir = $img_dir;

		$this->img_url = '/images/formulas/';
		if (!empty($img_url))
			$this->img_url = $img_url;

		$this->log(gmdate("Y-m-d H:i:s")."\n");
	}

	/* Close log file if it exists on destruction */
	public function __destruct()
	{
		if (is_resource($this->log_file))
		{
			$this->log("\n\n");
			fclose($this->log_file);
		}
	}

	/* Get full path to command */
	private function get_path($cmd)
	{
		$line = exec('command -v '.$cmd, $output);
		$this->log(implode("\n", $output));
		return $line;
	}

	/* Log some string to log file if available */
	private function log($str)
	{
		if ($this->log_file) {
			fwrite($this->log_file, $str);
		}
	}

	/* Check if required commands are available in this system */
	public function is_available()
	{
		if (empty($this->latex_path))
			return false;
		if ((empty($this->dvips_path) && empty($this->convert_path)) &&
		    (empty($this->dvipng_path)))
			return false;
		return true;
	}

	private function exec($cmd)
	{
		exec('TEXMFVAR='.$this->tmp_dir.'texmf/ '.$cmd, $output, $code);
		$this->log(implode("\n", $output));
		return $code;
	}

	/* Remove file or directory */
	private function rm($path)
	{
		exec('/bin/rm -r '.$path);
	}

	/* Remove blacklisted tags. Is this safe? */
	private function remove_tags($text)
	{
		for ($i=0; $i < count($this->tags_blacklist); $i++) {
			$text = str_replace($this->tags_blacklist[$i], '', $text);
		}
		return $text;
	}

	public function clean()
	{
		$this->rm($this->dirname);
	}

	/* Create image from LaTeX markup */
	private function process($text)
	{
		$text = trim($text);
		if (strlen($text) > $this->max_len)
			throw new Exception('Message too long (limit: '.$this->max_len.' chars)');
		$name = sha1($text.strlen($text));
		$text = $this->remove_tags($text);
		$doc = str_replace('{{ TEXT }}', $text, $this->tpl);

		/* Set names and paths */
		$this->dirname = $this->tmp_dir.$name.'/';
		$fname_base = $this->dirname.$name; /* Base file name, absolute path */
		$result_name = 'math_'.$name.'.png'; /* Result file name */
		$result = $this->img_dir.$result_name; /* Result file path */

		/* Create temporary directory */
		$this->clean();
		mkdir($this->dirname);
		$f = fopen($fname_base.'.tex', 'w');
		fwrite($f, $doc);

		/* Run latex */
		$err = $this->exec($this->latex_path.' -interaction=nonstopmode -output-directory='.$this->dirname.' '.$fname_base.'.tex');
		if ($err !== 0)	throw new Exception("latex can't process this text");

		/* dvips->imagemagick */
		if (!empty($this->dvips_path) && !empty($this->convert_path))
		{
			$err = $this->exec($this->dvips_path.' -E -o '.$fname_base.'.ps '.$fname_base.'.dvi');
			if ($err !== 0)	throw new Exception('dvips error');
			$err = $this->exec($this->convert_path. ' -density 150 -background "#fffffe" -flatten '.
					   $fname_base.'.ps '.$result);
			if ($err !== 0)	throw new Exception('convert error');
		}
		/* dvipng */
		elseif (!empty($this->dvipng_path))
		{
			$err = $this->exec($this->dvipng_path. ' -D 150 -T tight '.
					   $fname_base.'.dvi -o '.$result);
			if ($err !== 0)	throw new Exception('dvipng error');
		}

                /* Clean and exit */
		$this->clean();
		return $this->img_url.$result_name;
	}

	/* Public wrapper for process method */
	public function make_image($text)
	{
		try
		{
			$img = $this->process($text);
			return '<img src="'.$img.'" alt="" />';
		}
		catch (Exception $e) {
			$this->clean();
			return '<p>Error: '.$e->getMessage().'</p>';
		}
	}
}