<?php require_once $_SERVER['DOCUMENT_ROOT'] . '/system/App/init.php'; ?>
<?php
date_default_timezone_set('Asia/Manila');
use App\Model\User;
use App\Core\Functions;
$db = (new User)->connect();
$result = $db->query("
SELECT
    `examiner_info`.`firstname`,
    `examiner_info`.`middlename`,
    `examiner_info`.`lastname`,
    `examiner_info`.`sex`,
    `examiner_info`.`age`,
    `examiner_info`.`birthdate`,
    `entrace_rating`.`verbal_comprehension`,
    `entrace_rating`.`verbal_reasoning`,
    `entrace_rating`.`figurative_reasoning`,
    `entrace_rating`.`quantitative_reasoning`,
    `entrace_rating`.`verbal_total`,
    `entrace_rating`.`non_verbal_total`,
    `entrace_rating`.`over_all_total`, `course`.`course` as first_course,
    `course_2`.`course` as second_course ,`guidance_conselors`.`fullname` as guidance_counselor,
    `guidance_conselors`.`position` as position , `guidance_conselors`.`signature`
FROM
    admission_result
INNER JOIN examiner_info ON admission_result.examiner_info_id = examiner_info.id
LEFT JOIN entrace_rating ON admission_result.entrace_rating_id = entrace_rating.id
INNER JOIN course ON admission_result.preferred_course_id_1 = course.id
LEFT JOIN course AS course_2 ON admission_result.preferred_course_id_2 = course_2.id
LEFT JOIN guidance_conselors ON guidance_conselors.id = admission_result.guidance_counselor_id
WHERE
    admission_result.id = ' $_GET[id] '
")->fetch(PDO::FETCH_ASSOC);


class PDF extends FPDF
{
protected $B;
protected $I;
protected $U;
protected $HREF;
protected $fontList;
protected $issetfont;
protected $issetcolor;
function __construct($orientation='P', $unit='mm', $format='A4')
{
    //Call parent constructor
    parent::__construct($orientation,$unit,$format);

    //Initialization
    $this->B=0;
    $this->I=0;
    $this->U=0;
    $this->HREF='';

    $this->tableborder=0;
    $this->tdbegin=false;
    $this->tdwidth=0;
    $this->tdheight=0;
    $this->tdalign="L";
    $this->tdbgcolor=false;

    $this->oldx=0;
    $this->oldy=0;

    $this->fontlist=array("arial","times","courier","helvetica","symbol");
    $this->issetfont=false;
    $this->issetcolor=false;
}


// Page header
function Header()
{
    global $title;
    // Logo
    $this->Image($_SERVER['DOCUMENT_ROOT'] . '/system/assets/img/photos/sdssu.png',9,5,26);
    // Arial bold 15
    $this->SetFont('Arial','B',15);
    // Move to the right
    $this->Cell(80);
    // Title
    $this->MultiCell(0,0,false);
    $this->Cell(0,0,'SURIGAO DEL SUR STATE UNIVERSITY',0,1,'C');

    $this->SetFont('Arial','',10);
    $this->Cell(0,9,'Guidance Center',0,0,'C');

    $this->SetFont('Arial','B',10);
    $this->Cell(-20,0,'Reference Code',0,0,'C');
    $this->Cell(19,7,'FM-GC-005E',0,0,'C');

    $this->MultiCell(0,0,false);
    $this->SetFont('Arial','',10);
    $this->Cell(359,14,'Revision Number',0,0,'C');

    $this->MultiCell(0,0,false);
    $this->SetFont('Arial','B',10);
    $this->Cell(359,21,'000',0,0,'C');

    $this->MultiCell(0,0,false);
    $this->SetFont('Arial','',10);
    $this->Cell(359,28,'Date Effective',0,0,'C');

    $this->MultiCell(0,0,false);
    $this->SetFont('Arial','B',10);
    $this->Cell(359,35,date('m/d/Y',time()),0,0,'C');


    $this->SetFont('Arial','',10);
    $this->MultiCell(0,0,false);
    $this->Cell(0,17,'Tel. No. (086)214-2735',0,0,'C');
    $this->MultiCell(0,0,false);
    $this->Cell(0,25,'Tandag City, Surigal del Sur',0,0,'C');
    $this->MultiCell(0,0,false);
    $this->SetTextColor(135,206,250);
    $this->Cell(0,33,'www.sdssu.edu.ph',0,0,'C');
    $this->Cell(0,33,'','','','',false, "www.sdssu.edu.ph");
    // Line break
    $this->Ln(20);
}



    // Page footer
    function Footer()
    {
        // Position at 1.5 cm from bottom
        $this->SetY(-15);
        // Arial italic 8
        $this->SetFont('Arial','I',8);
        // Page number
        $this->Cell(0,10,'Page '.$this->PageNo(),0,0,'R');
    }
}



$pdf = new PDF();
$pdf->AliasNbPages();
$title = 'Admission Result';
$pdf->SetTitle($title);
$pdf->AddPage();
$pdf->SetFont('Arial','B',10);
$pdf->MultiCell(0,0,false);
$pdf->Cell(0,8,'UNIVERSITY ADMISSION TEST RESULT',0,1,'C');

$pdf->SetFont('Arial','I',9);
$pdf->MultiCell(0,0,false);
$pdf->Cell(0,0,'1st Semester AY ' . date('Y')  . ' - ' . date('Y',strtotime("+ 1 year")),0,1,'C');


$pdf->SetFont('Arial','',9);
$pdf->Line(20,55,190,55);
$pdf->Cell(40,30,ucfirst($result['lastname']),0,0,'C');
$pdf->Cell(110,30,ucfirst($result['firstname']),0,0,'C');
$pdf->Cell(45,30,ucfirst($result['middlename']),0,0,'C');
$pdf->MultiCell(0,0,false);
$pdf->Cell(40,40,'Last Name',0,0,'C');
$pdf->Cell(110,40,'Firstname ',0,0,'C');
$pdf->Cell(25,40,'M.I',0,1,'R');

$pdf->SetFont('Arial','',9);
$pdf->Ln(-22);
$pdf->Cell(40,23,ucfirst($result['sex']),0,0,'C');
$pdf->Cell(115,23,ucfirst($result['age']),0,0,'C');
$pdf->Cell(23,23,ucfirst($result['birthdate']),0,0,'C');
$pdf->MultiCell(0,0,false);
$pdf->Line(20,69,40,69);
$pdf->Line(95,69,119,69);
$pdf->Line(160,69,190,69);
$pdf->Cell(40,30,'Sex',0,0,'C');
$pdf->Cell(115,30,'Age',0,0,'C');
$pdf->Cell(19,30,'Birthdate',0,0,'R');

$pdf->MultiCell(0,0,false);
$pdf->setLeftMargin(20);
$pdf->Cell(0,57,'1st  : ' . $result['first_course'],0,0,'L');
$pdf->MultiCell(0,0,false);
$pdf->Cell(0,64,'2nd : ' . $result['second_course'],0,0,'L');
$pdf->MultiCell(0,0,false);
$pdf->Cell(0,50,'Preferred Courses',0,1,'L');
$pdf->SetFont('Arial','B',10);
$pdf->Cell(0,-25,'ENTRANCE EXAM RATING',0,0,'L');
$pdf->MultiCell(0,0,false);
$pdf->Ln(-8);
$pdf->setLeftMargin(35);

//START OF TABLE
    /*TABLE HEAD*/
    $pdf->Cell(40,5,'VERBAL',1,0,'C',0);
    $pdf->Cell(50,5,'NON VERBAL',1,0,'C',0);
    $pdf->Cell(50,5,'TOTAL',1,0,'C',0);
    /*END OF TABLE HEAD*/

$pdf->Ln();
$pdf->Cell(40,5,Functions::calculateEquivalent($result['verbal_comprehension'],[8,5]),1,0,'C',0);
$pdf->Cell(50,5,Functions::calculateEquivalent($result['non_verbal_total'],[24,13]),1,0,'C',0);
$pdf->Cell(50,5,'TOTAL',1,0,'C',0);

$pdf->Ln();
$pdf->Cell(40,5,Functions::calculateEquivalent($result['verbal_reasoning'],[13,8]),1,0,'C',0);
$pdf->Cell(50,5,Functions::calculateEquivalent($result['figurative_reasoning'],[11,6]),1,0,'C',0);
$pdf->Cell(50,5,'TOTAL',1,0,'C',0);


// END OF TABLE
$pdf->SetFont('Arial','',7);
$pdf->setLeftMargin(20);
$pdf->setRightMargin(24);
$pdf->setRightMargin(24);
$pdf->Ln(20);
$pdf->Cell(0,0,'Date Printed: ' . date('d/m/Y h:i A',time()),0,0,'L');
$pdf->SetFont('Arial','',10);
$pdf->Line(113,150,190,150);
$pdf->setRightMargin(87);
$pdf->Image($_SERVER['DOCUMENT_ROOT'] . '/system/assets/img/uploads/'.$result['signature'],145,137,15);
$pdf->Cell(0,0,$result['guidance_counselor'],0,0,'C');
$pdf->SetFont('Arial','',9);
$pdf->setRightMargin(40);
$pdf->Cell(0,11,$result['position'],0,0,'R');
$pdf->setLeftMargin(20);
$pdf->Ln(10.5);
$pdf->SetFont('Arial','I',7);
$pdf->SetTextColor(255,0,0);
$pdf->Cell(0,0,'NOT VALID IF THERE IS ANY ALTERATION',0,0,'L');
$pdf->setRightMargin(20);
$pdf->Cell(0,0,'VALID FOR ' . date('Y')  . ' - ' . date('Y',strtotime("+ 1 year")) . ' ONLY',0,0,'R');
$pdf->SetTextColor(0,0,0);
$pdf->Output();
?>