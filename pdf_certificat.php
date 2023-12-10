<?php
require('fpdf.php');
$isUTF8=TRUE;
class AlphaPDF extends FPDF
{
/********/
   protected $extgstates = array();

    // alpha: real value from 0 (transparent) to 1 (opaque)
    // bm:    blend mode, one of the following:
    //          Normal, Multiply, Screen, Overlay, Darken, Lighten, ColorDodge, ColorBurn,
    //          HardLight, SoftLight, Difference, Exclusion, Hue, Saturation, Color, Luminosity
    function SetAlpha($alpha, $bm='Normal')
    {
        // set alpha for stroking (CA) and non-stroking (ca) operations
        $gs = $this->AddExtGState(array('ca'=>$alpha, 'CA'=>$alpha, 'BM'=>'/'.$bm));
        $this->SetExtGState($gs);
    }

    function AddExtGState($parms)
    {
        $n = count($this->extgstates)+1;
        $this->extgstates[$n]['parms'] = $parms;
        return $n;
    }

    function SetExtGState($gs)
    {
        $this->_out(sprintf('/GS%d gs', $gs));
    }

    function _enddoc()
    {
        if(!empty($this->extgstates) && $this->PDFVersion<'1.4')
            $this->PDFVersion='1.4';
        parent::_enddoc();
    }

    function _putextgstates()
    {
        for ($i = 1; $i <= count($this->extgstates); $i++)
        {
            $this->_newobj();
            $this->extgstates[$i]['n'] = $this->n;
            $this->_put('<</Type /ExtGState');
            $parms = $this->extgstates[$i]['parms'];
            $this->_put(sprintf('/ca %.3F', $parms['ca']));
            $this->_put(sprintf('/CA %.3F', $parms['CA']));
            $this->_put('/BM '.$parms['BM']);
            $this->_put('>>');
            $this->_put('endobj');
        }
    }

    function _putresourcedict()
    {
        parent::_putresourcedict();
        $this->_put('/ExtGState <<');
        foreach($this->extgstates as $k=>$extgstate)
            $this->_put('/GS'.$k.' '.$extgstate['n'].' 0 R');
        $this->_put('>>');
    }

    function _putresources()
    {
        $this->_putextgstates();
        parent::_putresources();
    }
    
}
/********/
$image='images/groupe_essig_logo_fb.jpg';
//$image='images/laurier.jpg';
$dimMax=120;
$size = getimagesize($image);
$largeur=$size[0];
$hauteur=$size[1];
if ($hauteur>=$largeur) {
	$X=intval($dimMax*$largeur/$hauteur);
	$x_pos=intval((210-($dimMax*$largeur/$hauteur))/2);
	$y_pos=87;//(297-120)/2
	$Y=$dimMax;
}
else {
	$X=$dimMax;
	$x_pos=45;//(210-120)/2
	$y_pos=intval(40+(297-($dimMax*$hauteur/$largeur))/2);
	$Y=intval($dimMax*$hauteur/$largeur);
}
echo "debut ecriture du pdf avec l'image ", $image;
//$pdf = new FPDF();
$pdf = new AlphaPDF();
$pdf->AddPage();
$pdf->SetTitle('Certificat de scolarité',$isUTF8);
//
// passe en mode semi-transparent
$pdf->SetAlpha(0.3);
$pdf->image($image, $x_pos, $y_pos, $X,$Y);
// Opaque 100%
$pdf->SetAlpha(1);
// positionnement en haut de page
$pdf->SetY(0);
//
$pdf->SetFont('Times','',12);
$pdf->SetTextColor(0, 0, 0);
$pdf->Cell(0,7,'REPUBLIQUE GABONAISE',0,1,'C',false);
// Slogan multicolore à centrer
$pdf->SetFont('Helvetica','BI',12);
$pdf->Cell(75);
$pdf->SetTextColor(46, 184, 46);
$pdf->Write(7,'Union-');
$pdf->SetTextColor(240, 240, 0);
$pdf->Write(7,'Travail-');
$pdf->SetTextColor(0, 102, 204);
$pdf->Write(7,'Justice');
//
$pdf->Ln(10);
//
$pdf->SetFont('Helvetica','B',48);
$pdf->SetTextColor(33, 150, 243);
$pdf->Cell(85,20,'USIA',0,0,'R',false);
//
$pdf->Cell(20,20,'X',0,0,'C',false);//remplacer par le logo
//
//$pdf->Cell(0,20,'ESSIG',0,1,'L',false);
$pdf->Write(20,'ESSIG');
$pdf->Ln(15);
//
$pdf->SetFont('Helvetica','',7);
$pdf->SetTextColor(0, 0, 0);
$pdf->Cell(85,5,utf8_decode('Université des Sciences d\'Informatique Appliquée'),0,0,'R',false);
$pdf->Cell(20,5,'  ',0,0,'C',false);
$pdf->Cell(0,5,utf8_decode(' Ecole Supérieure des Sciences d\'Informatique et de Gestion'),0,1,'L',false);
//
$pdf->SetFont('Times','',12);
$pdf->SetTextColor(255, 0, 0);
$pdf->Cell(0,8,utf8_decode('Partenaire de l\'état gabonais sous le n°00263/MENESTFPCJS'),0,1,'C',false);
//
$pdf->SetFont('Times','',12);
$pdf->SetTextColor(0, 0, 0);
$pdf->Cell(0,7,utf8_decode('ETABLISSEMENT ARRIME AU LMD'),0,1,'C',false);
$pdf->Cell(0,8,utf8_decode('UNIVERSITE DES SCIENCES D\'INFORMATIQUE APPLIQUEES'),0,1,'C',false);
$pdf->Cell(0,8,utf8_decode('(USIA-ESSIG)'),0,1,'C',false);
$pdf->Cell(0,8,utf8_decode('BP : 26692 LIBREVILLE / TEL : 01-77-29-47 / 077-29-09-17'),0,1,'C',false);
$pdf->Cell(0,8,utf8_decode('E-mail : groupemaranouff@gmail.com / Site : www.groupe-essig.org'),0,1,'C',false);
//$pdf->Cell(0,8,utf8_decode('Partenaire de l\'état gabonais sous le n°00263/MENESTFPCJS'),0,1,'C',false);
$pdf->Cell(0,8,utf8_decode('Diplômes reconnus, certifiés et accrédités par le CAMES'),0,1,'C',false);
//
$pdf->Ln(10);
//
$pdf->SetFont('Helvetica','BIU',32);
$pdf->SetTextColor(33, 150, 243);
$pdf->Cell(0,20,utf8_decode('CERTIFICAT DE SCOLARITE'),1,1,'C',false);
//
$pdf->Ln(10);
//
$pdf->SetFont('Times','',12);
$pdf->SetTextColor(0, 0, 0);
$pdf->Cell(0,9,utf8_decode('Je soussigné, Directeur des Etudes de l\'USIA-ESSIG, atteste que'),0,1,'L',false);
$pdf->Cell(0,9,utf8_decode('l\'étudiant(e) <nom prenom>'),0,1,'L',false);
$pdf->Cell(0,9,utf8_decode('né(e) le <date naissance> à <lieu naissance> Matricule : <matricule>'),0,1,'L',false);
$pdf->Cell(0,9,utf8_decode(' est inscrit(e) en : <cursus> <filiere>'),0,1,'L',false);
//$pdf->Cell(0,9,utf8_decode('pour le compte de l\'année universitaire <periode[0]-periode[1]> a obtenu les soixante (60) crédits requis, comptant pour les semestres 5 et 6 avec une moyenne annuelle de <moyenne>/20.'),0,1,'C',false);
$pdf->Write(9,utf8_decode('pour le compte de l\'année universitaire <periode[0]-periode[1]>.'));
$pdf->Ln();
$pdf->Cell(0,9,utf8_decode('En foi de quoi, la présente attestation doit servir et valoir ce que de droit.'),0,1,'L',false);
$pdf->Ln(5);
$pdf->SetFont('Times','I',9);
$pdf->Cell(0,8,utf8_decode('fait à Libreville, le <date de validation des notes>'),0,1,'R',false);
$pdf->Ln(30);
$pdf->SetFont('Times','U',10);
$pdf->Cell(0,8,utf8_decode('Agossou Daniel HOUNYE'),0,1,'R',false);
//
$pdf->Ln(25);
//$pdf->SetFont('Times','',8);
//$pdf->Cell(0,7,utf8_decode('Avis important : il ne peut être délivré qu\'un seul exemplaire du présent attestation de réussite. Aucun duplicata ne sera fourni.'),0,1,'C',false);
//
$pdf->SetFont('Helvetica','B',6);
$pdf->SetTextColor(255, 255, 255);
$pdf->SetFillColor(33, 150, 243);
$pdf->Cell(0,7,utf8_decode('GROUPE ESSIG  TEL: 01.77.29.47 / 05.98.55.58 / 06.97.57.47 / 07.29.09.17 Compte bancaire N° 40001 09073 4016060002/56  www.groupe-essig.org'),0,1,'C',true);
//
$pdf->SetTextColor(0, 0, 0);
$pdf->Cell(0,7,utf8_decode('« Là où la volonté est grande, les difficultés diminuent » Nicolas MACHIAVEL'),0,1,'C',false);
//
//$pdf->Output('D','certificat_scolarite_USIA_ESSIG.pdf',true);
$pdf->Output('F','certificat_scolarite_USIA_ESSIG.pdf',true);


?>
