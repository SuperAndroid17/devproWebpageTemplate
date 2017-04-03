<?php
$klicks = substr(filter_input(INPUT_GET, 'klicks', FILTER_VALIDATE_REGEXP, array('options' => array('regexp' => "/^[0-9]+$/"))), 0, 25);
$verdienst = substr(filter_input(INPUT_GET, 'verdienst', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH), 0, 25);
$blogger = substr(filter_input(INPUT_GET, 'blogger', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH), 0, 25);
$month = substr(filter_input(INPUT_GET, 'month', FILTER_VALIDATE_REGEXP, array('options' => array('regexp' => "/^[0-9]+$/"))), 0, 5);

/*
 * outsource later in Database
 */
if($blogger = 'chen'){
    $absender = 'Zhexing Chen, Mülligerstrasse 16B, 5210 Windisch';
}

$now = date("d.m.Y");

require('../../../Engine/libary/fpdf/fpdf.php');

$pdf = new FPDF();
$pdf->AddPage();

// logo
$pdf->Image('http://158.69.116.140/de/wp-content/uploads/2014/01/logo-1-300x59.png',10,6,40);
$pdf->Ln(16);

$pdf->SetFont('Arial','',6);
$pdf->Cell(60,5,$absender,0,1);
$pdf->SetFont('Arial','',10);
$pdf->Cell(40,6,'YGOPro DevPro Online Knecht');
$pdf->Cell(0,6,'Datum '.$now,0,1,'R');
$pdf->Cell(40,6,'Weissenbühlweg 45',0,1);
$pdf->Cell(40,6,'3007 Bern',0,1);

$pdf->Ln(8);
//HeaderText
$pdf->SetFont('Arial','B',16);
$pdf->Cell(40,10,'Rechnung',0,1);

//Zeilenumbruch
$pdf->Ln(4);
$pdf->SetFont('Arial','',12);

// Table
// TableData and Header
$header = array('Pos.','Monat','Klicks', 'Blogger', 'Brutto CHF');
$data = array($klicks, $verdienst);

// Column widths
    $w = array(20,40,40,40,35);
    // Header
    for($i=0;$i<count($header);$i++)
        $pdf->Cell($w[$i],7,$header[$i],1,0,'C');
    $pdf->Ln();
    // Data
        $pdf->Cell($w[0],6,'1','LR',0,'R');
        $pdf->Cell($w[1],6,$month,'LR',0,'R');
        $pdf->Cell($w[2],6,$klicks,'LR',0,'R');
        $pdf->Cell($w[3],6,$blogger,'LR',0,'R');
        $pdf->Cell($w[4],6,$verdienst,'LR',0,'R');
        
        $pdf->Ln();
    
    // Closing line
    $pdf->Cell(array_sum($w),0,'','T');

    
    $pdf->Ln(16);
    
    /*
     * Berechnung Netto/Brutto
     */
    $p1 = ($verdienst / 100);
    $rtax = ($p1 * 8);
    $tax = round($rtax, 2);
    $netto = ($verdienst - $tax);

    // Zeilenabstand links
    $pdf->SetX(150);
    $pdf->Cell(40,5,'Netto:',0,0,'L');
    $pdf->Cell(0,5,$netto. ' chf',0,1,'R');
    $pdf->SetX(150);
    $pdf->Cell(40,5,'MwSt. 8%:',0,0,'L');
    $pdf->Cell(0,5,$tax. ' chf',0,1,'R');
    $pdf->SetX(150);
    $pdf->Cell(40,5,'Gesamt Brutto:',0,0,'L');
    $pdf->Cell(0,5,$verdienst. ' chf',0,1,'R');

$pdf->Output();




