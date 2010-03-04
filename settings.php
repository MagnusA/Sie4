<?php
// settings.php är en klass med basic settings för sie4export och lite mer eller mindre generella hjälpfunktioner.
// Läggs lämpligen i en egen fil

class settings extends sie4{

		protected  $encoding='ISO-8859-1'; 
		
	
	protected $tab="\t";
	protected $nyrad="\r\n";
	protected $globalSettings=array(
	'FLAGGA' => 0,
	
	'FORMAT' => 'PC8',
	'SIETYP' => 4,
	

	);
	// här sparas filerna
	protected $storedir = '../var/sie/';
	
	protected  $kostnadsstalle= true; //sätt till true om du vill aktivera kostnadsställen
	protected  $ks = array(
		'Kod 1' => 'Kostnadsställe 1',
		'Kod 2' => 'Kostnadsställe 2'); 
		//översättningstabell för kod och namn på kostnadsställen
	
		// här anger du olika konton som återfinns i din kontoplan och det namn som din input använder för respektive kontonummer
		// 'kontokod' => 'kontonummer'
	var  $konton= array(

	'kontonamn' => 'kontonummer'

	);
	
	//motkonton är kod för konton som ska registreras med negativa tal.
	var  $motkonto= array(
	'kontonamn'
	);
	
	
	// 
	 public  $localSettings=array(
	'FNAMN' => 'Företagsnamn',
	'FNR' => '', //Företagets  nummer i bokföringen, se SIE-dokumentationen
	'ORGNR' => '123456-7890',
	'ADRESS' => array('kontakt' =>'adressrad1','utdelningsadr' => 'adressrad2', 'postadr' => 'postadress', 'tel' => 'telefon'),
	'PROSA' => 'Exporterat av SIE export'
	
	);

	function getBelopp($belopp,$kontonamn){
		
		if(in_array($kontonamn,$this->motkonto)){
			
			return $this->minus($belopp);
		}
		return $belopp;
	}
	
	function minus($belopp){
		return  0-$belopp;
	}
	
	function getKonton(){
		return $this->konton;
	}


	function setKonto($namn, $nummer){
		$this->konton[$namn]=$nummer;
	}
	
	function getKonto($namn){
		return $this->konton[$namn];
	}
	
	

	function getKostnadsstalle($input){
		
			   		if ( $this->kostnadsstalle ) return  array(1 , $this->ks[$input]);
		return array();
		
	}
	function getKs($ks){
		if(array_key_exists($ks,$this->ks)) return $this->ks[$ks];
		return $ks;
	}
	function getEncoding(){
		return $this->encoding;
	}

	function getVerdatum($date){
		
		return date("Ymd",strtotime($date));
		}
		 function store($filename){
		
		$handle = fopen($this->storedir.$filename, 'wb');
		fwrite($handle, $this->printSie());
		fclose($handle);
	}
	
	function download($filename, $mimetype='text/sie4'){
		
		// Send file headers

	header("Content-type: $mimetype");

	header("Content-Disposition: attachment;filename=$filename");

	header("Content-Transfer-Encoding: binary");

	header('Pragma: no-cache');

	header('Expires: 0');

	// Send the file contents.

	set_time_limit(0);

	print $this->printSie();
		
	}

	
	

}


?>
