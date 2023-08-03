<?php

require('PHPPdf/fpdf.php');

require('PHPPdf/html.php');  



class JSPDF extends FPDF {

	var $javascript;

	var $n_js;



	function IncludeJS($script) {

		$this->javascript=$script;

	}
function EAN13($x, $y, $barcode, $h=16, $w=.55)
{
    $this->Barcode($x,$y,$barcode,$h,$w,13);
}

function UPC_A($x, $y, $barcode, $h=16, $w=.35)
{
    $this->Barcode($x,$y,$barcode,$h,$w,12);
}

function GetCheckDigit($barcode)
{
    //Compute the check digit
    $sum=0;
    for($i=1;$i<=11;$i+=2)
        $sum+=3*$barcode[$i];
    for($i=0;$i<=10;$i+=2)
        $sum+=$barcode[$i];
    $r=$sum%10;
    if($r>0)
        $r=10-$r;
    return $r;
}

function TestCheckDigit($barcode)
{
    //Test validity of check digit
    $sum=0;
    for($i=1;$i<=11;$i+=2)
        $sum+=3*$barcode[$i];
    for($i=0;$i<=10;$i+=2)
        $sum+=$barcode[$i];
    return ($sum+$barcode[12])%10==0;
}

function Barcode($x, $y, $barcode, $h, $w, $len)
{
    //Padding
    $barcode=str_pad($barcode,$len-1,'0',STR_PAD_LEFT);
    if($len==12)
        $barcode='0'.$barcode;
    //Add or control the check digit
    if(strlen($barcode)==12)
        $barcode.=$this->GetCheckDigit($barcode);
    /*elseif(!$this->TestCheckDigit($barcode))
        $this->Error('Incorrect check digit');*/
    //Convert digits to bars
    $codes=array(
        'A'=>array(
            '0'=>'0001101','1'=>'0011001','2'=>'0010011','3'=>'0111101','4'=>'0100011',
            '5'=>'0110001','6'=>'0101111','7'=>'0111011','8'=>'0110111','9'=>'0001011'),
        'B'=>array(
            '0'=>'0100111','1'=>'0110011','2'=>'0011011','3'=>'0100001','4'=>'0011101',
            '5'=>'0111001','6'=>'0000101','7'=>'0010001','8'=>'0001001','9'=>'0010111'),
        'C'=>array(
            '0'=>'1110010','1'=>'1100110','2'=>'1101100','3'=>'1000010','4'=>'1011100',
            '5'=>'1001110','6'=>'1010000','7'=>'1000100','8'=>'1001000','9'=>'1110100')
        );
    $parities=array(
        '0'=>array('A','A','A','A','A','A'),
        '1'=>array('A','A','B','A','B','B'),
        '2'=>array('A','A','B','B','A','B'),
        '3'=>array('A','A','B','B','B','A'),
        '4'=>array('A','B','A','A','B','B'),
        '5'=>array('A','B','B','A','A','B'),
        '6'=>array('A','B','B','B','A','A'),
        '7'=>array('A','B','A','B','A','B'),
        '8'=>array('A','B','A','B','B','A'),
        '9'=>array('A','B','B','A','B','A')
        );

    $code='101';
    $p=$parities[$barcode[0]];
    for($i=1;$i<=6;$i++)
        $code.=$codes[$p[$i-1]][$barcode[$i]];
    $code.='01010';
    for($i=7;$i<=12;$i++)
        $code.=$codes['C'][$barcode[$i]];
    $code.='101';
    //Draw bars
    for($i=0;$i<strlen($code);$i++)
    {
        if($code[$i]=='1')
            $this->Rect($x+$i*$w,$y,$w,$h,'F');
    }
    //Print text uder barcode
  /*  $this->SetFont('Arial','',12);
    $this->Text($x,$y+$h+11/$this->k,substr($barcode,-$len));*/
}

function Code39($xpos, $ypos, $code, $baseline=0.5, $height=5){

    $wide = $baseline;
    $narrow = $baseline / 3 ; 
    $gap = $narrow;

    $barChar['0'] = 'nnnwwnwnn';
    $barChar['1'] = 'wnnwnnnnw';
    $barChar['2'] = 'nnwwnnnnw';
    $barChar['3'] = 'wnwwnnnnn';
    $barChar['4'] = 'nnnwwnnnw';
    $barChar['5'] = 'wnnwwnnnn';
    $barChar['6'] = 'nnwwwnnnn';
    $barChar['7'] = 'nnnwnnwnw';
    $barChar['8'] = 'wnnwnnwnn';
    $barChar['9'] = 'nnwwnnwnn';
    $barChar['A'] = 'wnnnnwnnw';
    $barChar['B'] = 'nnwnnwnnw';
    $barChar['C'] = 'wnwnnwnnn';
    $barChar['D'] = 'nnnnwwnnw';
    $barChar['E'] = 'wnnnwwnnn';
    $barChar['F'] = 'nnwnwwnnn';
    $barChar['G'] = 'nnnnnwwnw';
    $barChar['H'] = 'wnnnnwwnn';
    $barChar['I'] = 'nnwnnwwnn';
    $barChar['J'] = 'nnnnwwwnn';
    $barChar['K'] = 'wnnnnnnww';
    $barChar['L'] = 'nnwnnnnww';
    $barChar['M'] = 'wnwnnnnwn';
    $barChar['N'] = 'nnnnwnnww';
    $barChar['O'] = 'wnnnwnnwn'; 
    $barChar['P'] = 'nnwnwnnwn';
    $barChar['Q'] = 'nnnnnnwww';
    $barChar['R'] = 'wnnnnnwwn';
    $barChar['S'] = 'nnwnnnwwn';
    $barChar['T'] = 'nnnnwnwwn';
    $barChar['U'] = 'wwnnnnnnw';
    $barChar['V'] = 'nwwnnnnnw';
    $barChar['W'] = 'wwwnnnnnn';
    $barChar['X'] = 'nwnnwnnnw';
    $barChar['Y'] = 'wwnnwnnnn';
    $barChar['Z'] = 'nwwnwnnnn';
    $barChar['-'] = 'nwnnnnwnw';
    $barChar['.'] = 'wwnnnnwnn';
    $barChar[' '] = 'nwwnnnwnn';
    $barChar['*'] = 'nwnnwnwnn';
    $barChar['$'] = 'nwnwnwnnn';
    $barChar['/'] = 'nwnwnnnwn';
    $barChar['+'] = 'nwnnnwnwn';
    $barChar['%'] = 'nnnwnwnwn';

   /* $this->SetFont('Arial','',10);
    $this->Text($xpos, $ypos + $height + 4, $code);
    $this->SetFillColor(0);*/

    $code = '*'.strtoupper($code).'*';
    for($i=0; $i<strlen($code); $i++){
        $char = $code[$i];
        if(!isset($barChar[$char])){
            $this->Error('Invalid character in barcode: '.$char);
        }
        $seq = $barChar[$char];
        for($bar=0; $bar<9; $bar++){
            if($seq[$bar] == 'n'){
                $lineWidth = $narrow;
            }else{
                $lineWidth = $wide;
            }
            if($bar % 2 == 0){
                $this->Rect($xpos, $ypos, $lineWidth, $height, 'F');
            }
            $xpos += $lineWidth;
        }
        $xpos += $gap;
    }
}
	function _putjavascript() {

		$this->_newobj();

		$this->n_js=$this->n;

		$this->_out('<<');

		$this->_out('/Names [(EmbeddedJS) '.($this->n+1).' 0 R]');

		$this->_out('>>');

		$this->_out('endobj');

		$this->_newobj();

		$this->_out('<<');

		$this->_out('/S /JavaScript');

		$this->_out('/JS '.$this->_textstring($this->javascript));

		$this->_out('>>');

		$this->_out('endobj');

	}



	function _putresources() {

		parent::_putresources();

		if (!empty($this->javascript)) {

			$this->_putjavascript();

		}

	}



	function _putcatalog() {

		parent::_putcatalog();

		if (!empty($this->javascript)) {

			$this->_out('/Names <</JavaScript '.($this->n_js).' 0 R>>');

		}

	}

}



class HTML extends JSPDF

{

    var $B=0;

    var $I=0;

    var $U=0;

    var $HREF='';

    var $ALIGN=''; 



	function IncludeJS($script) {

		$this->javascript=$script;

	}

	

	function PDF($orientation='P', $unit='mm', $format='A4')

	{

		//Call parent constructor

		$this->FPDF($orientation,$unit,$format);

		//Initialization

		$this->B=0;

		$this->I=0;

		$this->U=0;

		$this->HREF='';		

	}



	

	function AutoPrint($dialog=false)

	{

		//Open the print dialog or start printing immediately on the standard printer

		$param=($dialog ? 'true' : 'false');

		$script="print($param);";

		$this->IncludeJS($script);

	}



	function AutoPrintToPrinter($server, $printer, $dialog=false)

	{

		//Print on a shared printer (requires at least Acrobat 6)

		$script = "var pp = getPrintParams();";

		if($dialog)

			$script .= "pp.interactive = pp.constants.interactionLevel.full;";

		else

			$script .= "pp.interactive = pp.constants.interactionLevel.automatic;";

		$script .= "pp.printerName = '\\\\\\\\".$server."\\\\".$printer."';";

		$script .= "print(pp);";

		$this->IncludeJS($script);

	}

	

	function WriteCell($html, $x=10, $w="", $l="", $b)

	{		

		//HTML parser

		$html=str_replace("\n",' ',$html);

		$html=str_replace("<\b>",'</b>',$html);

		$html=str_replace("<\i>",'</i>',$html);

		$html=str_replace("<\u>",'</u>',$html);

		$a=preg_split('/<(.*)>/U',$html,-1,PREG_SPLIT_DELIM_CAPTURE);

		foreach($a as $i=>$e)

		{

			if($i%2==0)

			{

				//Text

				if($this->HREF){

					$this->PutLink($this->HREF,$e);

				}else{				

					$this->SetX($x);

					

					$border=substr($e,0,6) == "&nbsp;"? 'LR' : $b;

					if(substr($e,-6) == "&nbsp;") $border = 'LRB';

					$e = substr($e,0,6) == "&nbsp;" ? str_replace("&nbsp;", "", $e) : $e;

					

					$this->Cell($w, 5, $e, $border,'',$l);

				}

			}

			else

			{

				//Tag

				if($e[0]=='/')

					$this->CloseTag(strtoupper(substr($e,1)));

				else

				{

					//Extract attributes

					$a2=explode(' ',$e);

					$tag=strtoupper(array_shift($a2));

					$attr=array();

					foreach($a2 as $v)

					{

						if(preg_match('/([^=]*)=["\']?([^"\']*)/',$v,$a3))

							$attr[strtoupper($a3[1])]=$a3[2];

					}

					$this->OpenTag($tag,$attr);

				}

			}

		}

	}

	

	function WriteHTML2($html, $w="", $l="", $m="")

	{

		

		//HTML parser

		$html=str_replace("\n",' ',$html);

		$html=str_replace("<\b>",'</b>',$html);

		$html=str_replace("<\i>",'</i>',$html);

		$html=str_replace("<\u>",'</u>',$html);

		$a=preg_split('/<(.*)>/U',$html,-1,PREG_SPLIT_DELIM_CAPTURE);

		foreach($a as $i=>$e)

		{

			if($i%2==0)

			{

				//Text

				if($this->HREF){

					$this->PutLink($this->HREF,$e);

				}else{

					$this->w = !empty($w) ? $w : 200 + $this->MarginLeft;

					if(empty($this->MarginLeft))

						$this->lMargin = !empty($l) ? $l : 10;

					else

						$this->lMargin = $this->MarginLeft;					

					

					if(!empty($m)) $this->lMargin = $m;					

					$this->Write(5,$e);

				}

			}

			else

			{

				//Tag

				if($e[0]=='/')

					$this->CloseTag(strtoupper(substr($e,1)));

				else

				{

					//Extract attributes

					$a2=explode(' ',$e);

					$tag=strtoupper(array_shift($a2));

					$attr=array();

					foreach($a2 as $v)

					{

						if(preg_match('/([^=]*)=["\']?([^"\']*)/',$v,$a3))

							$attr[strtoupper($a3[1])]=$a3[2];

					}

					$this->OpenTag($tag,$attr);

				}

			}

		}

	}          

	

    function OpenTag($tag,$prop)

    {

				

		

        //Opening tag

        if($tag=='B' || $tag=='I' || $tag=='U')

            $this->SetStyle($tag,true);

        if($tag=='A')

            $this->HREF=$prop['HREF'];

        if($tag=='BR')            

            $this->Ln(5);            

        if($tag=='P')

            $this->ALIGN=$prop['ALIGN'];		

		

        if($tag=='HR')

        {

            if( !empty($prop['WIDTH']) )

                $Width = $prop['WIDTH'];

            else

                $Width = $this->w - $this->lMargin-$this->rMargin;

            $this->Ln(2);

            $x = $this->GetX();

            $y = $this->GetY();

            $this->SetLineWidth(0.4);

            $this->Line($x,$y,$x+$Width,$y);

            $this->SetLineWidth(0.2);

            $this->Ln(2);

        }

        if($tag=='BLOCKQUOTE'){

            $this->SetLeftMargin(28); 

            $this->Ln(10);                       

        }         

        if($tag=='PARA'){

           $this->SetLeftMargin(50); 

           $this->Ln(10); 

        }

        if($tag=='TAB'){

           $this->SetLeftMargin(65);            

        }        

        if($tag=='TAB2'){

           $this->SetLeftMargin(110);            

        }

    }



    function CloseTag($tag)

    {

        //Closing tag

        if($tag=='B' || $tag=='I' || $tag=='U')

            $this->SetStyle($tag,false);

        if($tag=='A')

            $this->HREF='';

        if($tag=='P')

            $this->ALIGN='';

        if($tag=='BLOCKQUOTE')

            $this->SetLeftMargin(20);

            //$this->Ln(5);

        if($tag=='PARA')

            $this->SetLeftMargin(20); 

        if($tag=='TAB')

           $this->SetLeftMargin(28);

        if($tag=='TAB2')

           $this->SetLeftMargin(28);            

                                                                             

    }



    function SetStyle($tag,$enable)

    {

        //Modify style and select corresponding font

        $this->$tag+=($enable ? 1 : -1);

        $style='';

        foreach(array('B','I','U') as $s)

            if($this->$s>0)

                $style.=$s;

        $this->SetFont('',$style);

    }



    function PutLink($URL,$txt)

    {

        //Put a hyperlink

        $this->SetTextColor(0,0,255);

        $this->SetStyle('U',true);

        $this->Write(5,$txt,$URL);

        $this->SetStyle('U',false);

        $this->SetTextColor(0);

    }

    

	function Header(){

		if(!empty($this->ImageParaf) && $this->PageNo() == 2){

			$this->Image($this->ImageParaf,153,15);		

		}

	}

	

    function Footer()

    {				

		$this->SetY(-15);

		$this->SetFont('Arial','',8);				

		if(!empty($this->FooterText) && $this->PageNo() >= $this->FooterStart && ($this->PageNo() <= $this->FooterEnd || empty($this->FooterEnd))){

			//Page number

			$this->SetX(10);

			$this->Cell(190,5,'  Halaman '.($this->PageNo()-($this->FooterStart-1)).' dari {nb}',0,0,'C');	

						

			if(!empty($this->FooterText)){

				$this->Ln(5);

				$this->SetX(10);

				$this->Cell(190,5,$this->FooterText,0,0,'C');		

			}

			if(!empty($this->FooterAdd)){				

				$this->SetX(10);

				$this->Cell(190,5,$this->FooterAdd,0,0,'R');

			}

		}

    } 

    

    function WriteTable($data,$border,$margin,$class,$align,$w)

	{			

		//$this->SetLineWidth(.1);

		$this->SetFillColor(255,255,255);

		$this->SetTextColor(0);		

		if(empty($this->LineHeight)) $this->LineHeight = 5;

		

		//$this->SetFont('Arial','',8);				

		$n=0;

		foreach($data as $row)

		{

			$this->SetX($margin);

			

			$nb=0;

			for($i=0;$i<count($row);$i++)

				$nb=max($nb,$this->NbLines($w[$i],trim($row[$i])));

			$h=$this->LineHeight*$nb;

			$this->CheckPageBreak($h,$margin);

			

			$lastWidth = 0;

			for($i=0;$i<count($row);$i++)

			{

				$x=$this->GetX();

				$y=$this->GetY();

										

				if($class[$n] == "clear"){

					$width = $w[$i];

					if(!empty($border)) $this->Rect($x,$y,$w[$i],$h);					

					$a = empty($align[$n]) ? "L" : $align[$n];

					$this->MultiCell($w[$i],5,trim($row[$i]),0,$a);

				}else if(in_array($class[$n], array("cell", "left", "right", "none", "notop","wkwk","total", "nobottom", "noleft", "noright", "leftright", "topbottom"))){

					$a = empty($align[$n]) ? "L" : $align[$n];

					$b = $border;

					if($class[$n] == "none") $b = 0;

					if($class[$n] == "left") $b = "L";

					if($class[$n] == "right") $b = "R";

					if($class[$n] == "notop") $b = "LRB";
					if($class[$n] == "total") $b = "LB";

					if($class[$n] == "wkwk") $b = "B";

					if($class[$n] == "nobottom") $b = "TLR";

					if($class[$n] == "noleft") $b = "TRB";

					if($class[$n] == "noright") $b = "TLB";

					if($class[$n] == "leftright") $b = "LR";

					if($class[$n] == "topbottom") $b = "TB";

					

					$this->WriteCell(trim($row[$i]), $x, $w[$i], $a, $b);					

				}else{

					$width = $w[$i]+10;

					if(!empty($border)) $this->Rect($x,$y,$w[$i],$h);

					$this->WriteHTML2(trim($row[$i]), $width+$x, $x, $lastWidth);						

				}

				

				$lastWidth = $width;			

				$this->SetXY($x+$w[$i],$y);//                    

				$n++;

			}

			

			$this->Ln($h);

		}

	}



	function NbLines($w, $txt)

	{

		//Computes the number of lines a MultiCell of width w will take

		$cw=&$this->CurrentFont['cw'];

		if($w==0)

			$w=$this->w-$this->rMargin-$this->x;

		$wmax=($w-2*$this->cMargin)*1000/$this->FontSize;

		$s=str_replace("\r",'',$txt);

		$nb=strlen($s);

		if($nb>0 && $s[$nb-1]=="\n")

			$nb--;

		$sep=-1;

		$i=0;

		$j=0;

		$l=0;

		$nl=1;

		while($i<$nb)

		{

			$c=$s[$i];

			if($c=="\n")

			{

				$i++;

				$sep=-1;

				$j=$i;

				$l=0;

				$nl++;

				continue;

			}

			if($c==' ')

				$sep=$i;

			$l+=$cw[$c];

			if($l>$wmax)

			{

				if($sep==-1)

				{

					if($i==$j)

						$i++;

				}

				else

					$i=$sep+1;

				$sep=-1;

				$j=$i;

				$l=0;

				$nl++;

			}

			else

				$i++;

		}

		return $nl;

	}



	function CheckPageBreak($h, $margin){

		//If the height h would cause an overflow, add a new page immediately

		if($this->GetY()+$h>$this->PageBreakTrigger){		

			$this->AddPage($this->CurOrientation);						

			$this->SetX($margin);			

		}

	}	



	function ReplaceHTML($html)

	{

		$html = str_replace( '<li>', "\n<br> - " , $html );

		$html = str_replace( '<LI>', "\n - " , $html );

		$html = str_replace( '</ul>', "\n\n" , $html );

		$html = str_replace( '<strong>', "<b>" , $html );

		$html = str_replace( '</strong>', "</b>" , $html );

		$html = str_replace( '<em>', "<i>" , $html );

		$html = str_replace( '</em>', "</i>" , $html );

		$html = str_replace( '<span style="text-decoration: underline;">', "<u>" , $html );

		$html = str_replace( '</span>', "</u>" , $html );				

		$html = str_replace( '&#160;', "\n" , $html );

		$html = str_replace( '&nbsp;', " " , $html );

		$html = str_replace( '&amp;', "&" , $html );		

		$html = str_replace( '&quot;', "\"" , $html ); 

		$html = str_replace( '&#39;', "'" , $html );

		$html = str_replace( '<br>', "\n<br>" , $html );

		$html = str_replace( '<h1>', "</h1>" , $html );

		$html = str_replace( '<center>', "</center>" , $html );

		$html = str_replace( '&ldquo;', "\" " , $html );

		$html = str_replace( '&rdquo;', " \"" , $html );

		

		return $html;

	}



	function ParseTable($Table)

	{

		$_var='';

		$htmlText = $Table;

		$parser = new HtmlParser ($htmlText);

		while ($parser->parse())

		{

			if(strtolower($parser->iNodeName)=='b')

			{			

				if($parser->iNodeType == NODE_TYPE_ENDELEMENT)

					$_var .='</b>';

				else

					$_var .='<b>';

			}

			

			if(strtolower($parser->iNodeName)=='i')

			{			

				if($parser->iNodeType == NODE_TYPE_ENDELEMENT)

					$_var .='</i>';

				else

					$_var .='<i>';

			}

			

			if(strtolower($parser->iNodeName)=='u')

			{			

				if($parser->iNodeType == NODE_TYPE_ENDELEMENT)

					$_var .='</u>';

				else

					$_var .='<u>';

			}

			

			if(strtolower($parser->iNodeName)=='table')

			{

				if($parser->iNodeType == NODE_TYPE_ENDELEMENT)

					$_var .='/::';

				else

					$_var .='::';

			}



			if(strtolower($parser->iNodeName)=='tr')

			{

				if($parser->iNodeType == NODE_TYPE_ENDELEMENT)

					$_var .='!-:'; //opening row

				else

					$_var .=':-!'; //closing row

			}

			if(strtolower($parser->iNodeName)=='td' && $parser->iNodeType == NODE_TYPE_ENDELEMENT)

			{				

				$_var .='#,#';

			}

			if ($parser->iNodeName=='Text' && isset($parser->iNodeValue))

			{

				$_var .= $parser->iNodeValue;

			}

		}

		//$_var = str_replace("&permil;",iconv('UTF-8', 'windows-1252', '‰'), $_var);

		$elems = explode(':-!',str_replace('::','',str_replace('!-:','',$_var)));

		foreach($elems as $key=>$value)

		{

			if(trim($value)!='')

			{

				$elems2 = explode('#,#',$value);

				

				array_pop($elems2);

				$data[] = $elems2;

			}

		}

		

		return $data;

	}



	function TableBorder($html){

		$arr = explode("<", $html);	

		$border = 0;

		if(is_array($arr)){

			reset($arr);

			while (list($id, $val) = each($arr)){

				if(substr($val,0,5) == "table"){

					$val = str_replace('>',"",$val);			

					$br = explode("border=", $val);

					$dta = str_replace('table border="',"",$br[1]);

					$dta = str_replace('"',"",trim($dta));

				

					$border = $dta;

				}

			}

		}

		return $border;

	}

	

	function TableAlign($html){

		$arr = explode("<", $html);	

		$align = 0;

		if(is_array($arr)){

			reset($arr);

			while (list($id, $val) = each($arr)){

				if(substr($val,0,5) == "table"){

					$val = str_replace('>',"",$val);			

					$br = explode("align=", $val);

					$dta = str_replace('table border="',"",$br[1]);

					$al = explode(" ", $dta);

					$align = str_replace('"',"",trim($al[0]));			

				}

			}

		}

		

		return $align;

	}



	

	function TdWidth($html){

		$dta = array();

		$arr = explode("<", $html);		

		if(is_array($arr)){

			reset($arr);

			while (list($id, $val) = each($arr)){

				if(substr($val,0,2) == "td"){

					$det = explode(">", $val);

					$td = str_replace('td',"",$det[0]);

					$width = explode('width="',$td);					

					$td = str_replace('"',"",$width[1]);

					$dt = explode('" ', $td);					

					$dta[] = trim($dt[count($dt) - 1])/4;

				}

			}

		}

		

		return $dta;

	}

	

	function TdClass($html){

		$dta = array();

		$arr = explode("<", $html);		

		if(is_array($arr)){

			reset($arr);

			while (list($id, $val) = each($arr)){

				if(substr($val,0,2) == "td"){

					$det = explode(">", $val);

					$td = str_replace('td',"",$det[0]);

					$td = str_replace('"',"",$td);

					$cl = explode('class=', $td);					

					if(is_array($cl)){

						reset($cl);

						while (list($i, $v) = each($cl)){

							list($c) = explode(" ", $v);

							$class = "";

							if(trim($c) == "clear") $class = "clear";

							if(trim($c) == "cell") $class = "cell";

							if(trim($c) == "none") $class = "none";

							if(trim($c) == "left") $class = "left";

							if(trim($c) == "right") $class = "right";

							if(trim($c) == "noleft") $class = "notop";

							if(trim($c) == "noright") $class = "notop";

							if(trim($c) == "notop") $class = "notop";
							if(trim($c) == "wkwk") $class = "wkwk";
							if(trim($c) == "total") $class = "total";



							if(trim($c) == "nobottom") $class = "nobottom";

							if(trim($c) == "topbottom") $class = "notop";

							if(trim($c) == "leftright") $class = "leftright";

							

							if(in_array(trim($c), array("clear", "cell", "none", "left", "right", "noleft", "noright", "notop","wkwk","total", "nobottom", "topbottom", "leftright")))

								$class = trim($c); 

							

							$dt[] = $class;

						}

					}						

					$dta[] = trim($dt[count($dt) - 1]);

				}

			}

		}		

		

		return $dta;		

	}

	

	function TdAlign($html){

		$dta = array();

		$arr = explode("<", $html);		

		if(is_array($arr)){

			reset($arr);

			while (list($id, $val) = each($arr)){

				if(substr($val,0,2) == "td"){

					$det = explode(">", $val);

					$td = str_replace('td',"",$det[0]);

					$td = str_replace('"',"",$td);

					$cl = explode('align=', $td);					

					if(is_array($cl)){

						reset($cl);

						while (list($i, $v) = each($cl)){							

							list($align) = explode(" ",$v);

							$a = strtolower($align);							

							if(in_array($a, array("left", "right", "center", "justify"))){

								$dt[] = substr(strtoupper($a),0,1);

							}else{

								$dt[] = "L";

							}

						}

					}	

					

					$dta[] = trim($dt[count($dt) - 1]);

				}

			}

		}		

		return $dta;		

	}

	

	function WriteHTML($html)

	{

		$html = $this->ReplaceHTML($html);

		//Search for a table

		$start = strpos(strtolower($html),'<table');

		$end = strpos(strtolower($html),'</table');

		if($start!==false && $end!==false)

		{

			$this->WriteHTML2(substr($html,0,$start));			

			

			$tableVar = substr($html,$start,$end-$start);

			$tableData = $this->ParseTable($tableVar);

			$tableBorder = $this->TableBorder($tableVar);

			$tableAlign = $this->TableAlign($tableVar);

			$tdWidth = $this->TdWidth($tableVar);

			$tdClass = $this->TdClass($tableVar);

			$tdAlign = $this->TdAlign($tableVar);

			

			for($i=1;$i<=count($tableData[0]);$i++)

			{								

				$width[] = round(190/count($tableData[0]),2);

			}

			

			$cntTd=0;

			if(is_array($width)){

				reset($width);

				while (list($id) = each($width)){										

					if(!empty($tdWidth[$id])) $cntTd++;

				}

			}

			

			$rWidth = round((190-array_sum($tdWidth))/(count($tableData[0])-$cntTd),2);

			if(is_array($width)){

				reset($width);

				while (list($id) = each($width)){										

					$w[] = empty($tdWidth[$id]) ? $rWidth : $tdWidth[$id];

				}

			}

			

			if(empty($this->MarginLeft)) $this->MarginLeft = 10;

			$tableMargin = $this->MarginLeft;

			if(strtolower($tableAlign) == "center")

				$tableMargin = round((200 - array_sum($w))/2);

			if(strtolower($tableAlign) == "right")

				$tableMargin = 200 - array_sum($w);

			

			$this->WriteTable($tableData,$tableBorder,$tableMargin,$tdClass,$tdAlign,$w);

			$this->WriteHTML2(substr($html,$end+8,strlen($html)-1));



		}

		else

		{

			$this->WriteHTML2($html);

		}

	}



	function SetHTML($html)

	{			

		$html = str_replace( '<tbody>', "" , $html );

		$html = str_replace( '</tbody>', "" , $html );		

		$html = str_replace( ' style="width: 100%;"', "" , $html );

		$arr = explode("</table>", $html);

		if(is_array($arr)){

			reset($arr);

			while (list($id, $val) = each($arr)){

				$this->WriteHTML($val."</table>");

			}

		}

	}	

}

?>
