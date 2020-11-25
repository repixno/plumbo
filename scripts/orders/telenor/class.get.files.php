<?PHP
class getfiles
{
	function getfiles($dir,$ext,$recursive=true)
	{
		$this->_fileList = array(); #aqui ele vai guardar todos os nomes dos arquivos
		$this->_fileListPath = array(); #aqui ele vai guardar todos os nomes dos arquivos com o caminho
		$this->returnPath = false; #se eh para retornar com ou caminho ou sem
		$this->_noList[] = array(); #arquivos que foram desconsiderados
		$this->_allFiles[] = array(); #todos os arquivos encontrados
		$this->_folders[] = array(); #mostra todas as pastas
		$this->_foldersPath[] = array(); #mostra todas as pastas com o caminho
		$this->recursive = $recursive; #se vai mostrar os subdiretorios
		
		#verificar se o diretorio existe e se eh um diretorio
		$dir = str_replace("\\","/",$dir);
		if(!is_dir($dir))
		{
			echo "<BR><B>ERROR $dir!</b><BR>";
			exit;
		}
		else #se for um diretorio ja guarda os diretorios visitados
		{
			$this->_foldersPath[] = $dir;
			$this->_dir = $dir;
		}

		if(is_array($ext)) #grava as exteções dos arquivos para montar a lista
			$this->_ext = $ext;
		else
			$this->_ext = array($ext);
			
		$this->getFileList($this->_dir);
	}
	
	function getimages($dir,$ext)
	{
		$this->_fileList = array(); 
		$this->_fileListPath = array(); 
		$this->returnPath = false; 
		$this->_noList[] = array(); 
		$this->_allFiles[] = array(); 
		$this->_folders[] = array(); 
		$this->_foldersPath[] = array(); 
		$this->recursive = false;
		
	
		$dir = str_replace("\\","/",$dir);
		if(!is_dir($dir))
		{
			echo "<BR><B>ERROR $dir!</b><BR>";
			exit;
		}
		else 
		{
			$this->_foldersPath[] = $dir;
			$this->_dir = $dir;
		}

		if(is_array($ext))
			$this->_ext = $ext;
		else
			$this->_ext = array($ext);
			
		$this->getFileList($this->_dir);
	}
	

	function getFileList($dir)
	{
		$_dir = $dir;
		$aux = explode("/",$dir);
		$dir = $aux[count($aux)-1];
		$this->_folders[] = $dir;
		$path = str_replace($dir,"",$_dir);
		$d = dir($path.$dir);
		while (false !== ($entry = $d->read())) 
		{
			if( ($entry != ".") && ($entry !="..") )
			{
				if(is_dir($d->path."/".$entry))
				{
					$this->_folders[] = $entry;
					if($this->recursive)
						$this->getFileList($path.$dir."/".$entry);
				}
				else
				{
					if($this->check($entry))
					{
						$this->_fileList[] = $entry;
						$this->_fileListPath[] = $d->path."/".$entry;
					}
					else
						$this->_noList[] = $entry;
					$this->_allFiles[] = $entry;
				}
			}
		}
		$d->close();
	}
	


	function check($file) 
	{
		foreach($this->_ext as $value)
		{
		   if (strtoupper($this->ext($file)) == strtoupper($value))
			  return true;
		}
		 return false;

		/*foreach($this->_ext as $value)
		{
			$ok = 0;
			$aux = split("[.]",$file);
			$ext = $aux[1];
			if(strtoupper($ext) == strtoupper($value))
				return true;
		}
		return false;*/
	}
	
	function ext($file) //Colaboracao de nosso amigo Ricardo Reis <rreis00@hotmail.com>
	{
		$filearray = explode('.', basename($file));
		return array_pop( $filearray );
	} // end.ext();


	
	#verifica se vai trazer o arquivo com o caminho ou nao
	function path($bool)
	{
		if(is_bool($bool))
			$this->returnPath = $bool;
		else
			echo "Este valor de ser TRUE ou FALSE";
	}
	
	#retorna a lista em array com ou sem o caminho
	function getList()
	{
		if($this->returnPath)
			return $this->_fileListPath;
		else
			return $this->_fileList;
	}
	
	#retorna apenas os nomes dos arquivos
	function getNameFiles()
	{
		return $this->_fileList;
	}
	
	#retorna os nomes dos arquivos com o caminho
	function getFilesPath()
	{
		return $this->_fileListPath;
	}
	
	#retorna apenas os nomes dos arquivos que estao no diretorio mas nao foram usado
	function getNoFiles()
	{
		return $this->_NoList;
	}
	
	#retorna todos os nomes dos arquivos
	function getAllFiles()
	{
		return $this->_allFiles;
	}
	
	#retorna todas as pastas
	function getFolders()
	{
		return $this->_folders;
	}
	
	#retorna todas as pastas com o caminho
	function getFolderPath()
	{
		return $this->_foldersPath;
	}
}
?>
