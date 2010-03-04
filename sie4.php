<?php
/* sie4.php är en basklass för export av sie4-filer till bokföringsprogram.
 * För mer info om Sieformatet, se SIE-gruppens hemsida http://www.sie.se
 * Basklassen kompletteras lämpligen med en settings-klass, som bifogas längre ner

  Upphovsman: Magnus Askaner, magnus@askaner.com
   
   Licens:
 
    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
    
    Får distribueras i enlighet med villkoren i Gnu GPLv3 och endast om uppgifter
    om upphovsman och licensinformation bifogas.
 
*/


 class sie4{
	//basklass för sie4
	
	
	var $siefile ='';
	
	protected $encoding='ISO-8859-1';
	
	protected $tab="\t";
	protected $nyrad="\r\n";
	
	
	protected $globalSettings=array(
	'FLAGGA' => 0,
	
	'FORMAT' => 'PC8',
	'SIETYP' => 4,
	

	);
	
	
	protected $ver = array();
	protected $trans = array();
	protected $vers =array();
	
	
	protected $localSettings=array(

	
	);
	
	protected $dim= array();
	var $rows=array();
	

	function __construct(){
				$this->readGlobalSettings();
				//$this->setArrayValues($this->localSettings);
				$this->setRow('GEN',date('Ymd'));
				


		
	}
	
	function setEncoding($encoding){
		$this->encoding=$encoding;
	}
	
	function setArrayValues($array=null){
		if(is_array($array)){
			foreach($array as $key=> $value){
			$this->setRow($key,$value);
			}
		}
	}
	function setRow($row,$value){
		
		$this->rows[]=array($row,$value);
		
	}
	
	function readGlobalSettings(){
		$this->setArrayValues($this->globalSettings);
		
	}
	function addLocalSettings(array $array){
		$this->setArrayValues($array);
		
	}


	
	function addSieRows(){
		foreach ($this->rows as  $value){
			
			if(is_array($value[1])) $value[1]= implode('" "',$value[1]);
			$this->siefile .= '#'.strtoupper($value[0])."\t".'"'.$value[1].'"'.$this->nyrad; 
			
			
			}
		}
			function addTrans($kontonr='',$belopp=0,$transtext='',$transdat='',$objektlista=array(),$kvantitet='',$sign=''){
				// checka att dessa index är med: kontonr {objektlista} belopp transdat transtext kvantitet sign

				$this->trans[]= array('kontonr'=>$kontonr,'objektlista'=>$objektlista,'belopp'=>$belopp,'transdat'=>$transdat,'transtext'=>$transtext,'kvantitet'=>$kvantitet,'sign'=>$sign);
				

				
			}
			
			
			
			function verAddIndex($verdatum,$vertext,$regdatum='',$sign='') {
				 $this->ver['index']=array(
				'verdatum' => date("Ymd",strtotime($verdatum)),
				'vertext' => $vertext,
				'regdatum' => $regdatum,
				'sign' => $sign);

				
				}
		function verClose(){
			$this->ver['trans']= $this->trans;
			$this->trans=array();
			$this->vers[]= $this->ver;
			$this->ver=array();	
			
			
			
			}
			
			function verPrint(){
				
			if (count($this->vers) >0){
			foreach($this->vers as $value){
				
				 $this->siefile .= "#VER ''  ''  {$value['index']['verdatum']} '{$value['index']['vertext']}' {$value['index']['regdatum']} {$value['index']['sign']}".$this->nyrad;
				 
				  $this->siefile .= "{".$this->nyrad;
				  $summa=0;
					foreach($value['trans'] as $trans){
						$summa += $trans['belopp'];
						if (count($trans['objektlista']) >0){
								$object = "  {$trans['objektlista'][0]} '{$trans['objektlista'][1]}'  ";
							}
							else $object= '';
							$belopp= number_format($trans['belopp'],2,'.','');
							 
						 $this->siefile .= "#TRANS {$trans['kontonr']} { $object} $belopp  {$trans['transdat']}  '{$trans['transtext']}'  {$trans['kvantitet']} {$trans['sign']} ".$this->nyrad;
						 
						
					}
					
					if(abs($summa) > 0.005) throw new exception('Verifikationen stämmer inte');
				$this->siefile .= "}\n";
				
				}
			}
			
			}
		
	
	function printSie(){
		
		 $this->siefile = preg_replace("/'/",'"',$this->siefile);
		$this->siefile = iconv($this->encoding, 'ISO-8859-1//TRANSLIT',$this->siefile);
		return $this->siefile;
				
		
	}
	
	
	
	
	
	}



/* 
 * Exempel:
 $sie= new settings;  // 
 * $sie->addLocalSettings(array(
	'FNAMN' => 'Företagsnamn',
	'FNR' => '',
	'ORGNR' => '',
	'ADRESS' => array('kontakt' =>'adressrad1','utdelningsadr' => 'adressrad2', 'postadr' => 'postadress', 'tel' => 'telefon'),
	'PROSA' => 'Exporterat av Magento SIE exporter'
	
	));
	*/
//Sätt denna om du har en annan encoding än ISO-8859-1	
//$sie->setEncoding('UTF-8');	

/*

//Sätter inledningen av filen
$sie->addSieRows();


//Börjar på en verifikatine
$sie->verAddIndex($verdatum='20100216',$vertext='Porto', $regdatum='', $sign ='');

//lägger till rader
$sie->addTrans($kontonr='6510',$belopp=100,$transtext='Pennor och suddigummin',$transdat='',$objektlista=array(),$kvantitet='',$sign='');
$sie->addTrans($kontonr='1310',$belopp=-100,$transtext='Kontant',$transdat='',$objektlista=array(),$kvantitet='',$sign='');
//stänger transaktionen
$sie->verClose();
 
// Börjar på en ny
$sie->verAddIndex($verdatum='20100216',$vertext='Porto', $regdatum='', $sign ='');
$sie->addTrans($kontonr='6510',$belopp=100,$transtext='Pennor och suddigummin',$transdat='',$objektlista=array(),$kvantitet='',$sign='');
$sie->addTrans($kontonr='1310',$belopp=-100,$transtext='Kontant',$transdat='',$objektlista=array(),$kvantitet='',$sign='');

$sie->verClose();
// skriver verifikationen till en variabel
$sie->verPrint();

// returnerar variabeln
$file = $sie->printSie();

// skriver till fil
$sie->store($filename);

// ladda ner
$sie->download($filename,$mimetype);

*/ 
?>


