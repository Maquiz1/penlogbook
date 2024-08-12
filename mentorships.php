<?php

require_once 'pdf.php';
$user = new User();
$override = new OverideData();
$email = new Email();
$random = new Random();

if ($user->isLoggedIn()) {
    try {
        $registered = $override->getCount('mentorship', 'status', 1);
        $screened = $override->getCount1('mentorship', 'site_id',1, 'status', 1);
        $enrolled = $override->getCount1('mentorship', 'site_id',2, 'status', 1);
        // $SiteData = $override->get('sites', 'status', 1);
        // $SiteName = $override->get('sites', 'status', 1);
        // $ListByMonthAllTables = $override->ListByMonthAllTables('clients', 'hiv_history_and_medication', 'eligibility', 'enrollments', 'risk_factors', 'medications', 'chronic_illnesses', 'laboratory_results', 'radiological_investigations', 'create_on');
        $successMessage = 'Report Successful Created';
    } catch (Exception $e) {
        die($e->getMessage());
    }
} else {
    Redirect::to('index.php');
}


$span0 = 4;
$span1 = 9;
$span2 = 9;

$site = 'Penplus Mentorships - TANZANIA';
$title = 'SUMMARY REPORT AS OF ' . date('Y-m-d');

$pdf = new Pdf();
$file_name = $title . '.pdf';

$output = ' ';

$output .= '
            <table width="100%" border="1" cellpadding="5" cellspacing="0">
                <tr>
                    <td colspan="' . $span0 . '" align="center" style="font-size: 18px">
                        <b>' . $site . '</b>
                    </td>
                </tr>
                <tr>
                    <td colspan="' . $span0 . '" align="center" style="font-size: 18px">
                        <b>' . $title . '</b>
                    </td>
                </tr>
                <tr>
                    <td colspan="' . $span0 . '" align="center" style="font-size: 18px">
                        <b>Total Mentorships ( ' . $registered . ' ): Knodoa ( ' . $screened . ' ):  Karatu ( ' . $enrolled . ' )</b>
                    </td>
                </tr>
                <tr>
                    <td colspan="' . $span0 . '">                        
                        <br />
                        <table width="100%" border="1" cellpadding="5" cellspacing="0">
                            <tr>
                                <th rowspan="1">No</th>
                                <th rowspan="1">PERIOD</th>
                                <th rowspan="1">SITES</th>
                                <th rowspan="1">COUNT</th>
                            </tr>
            ';

// Load HTML content into dompdf
$x = 1;
$mentorship = $override->ListByMonths('mentorship', 'site_id', 'create_on');

foreach ($mentorship as $row) {
    // $site = $override->ListByMonths('mentorship', $row['id'], 'create_on');
    // $crf2 = $override->getCount('mentorship', 'status', 1);
    // echo "<tr><td>" . $row["month"] . "</td><td>" . $row["site_column"] . "</td><td>" . $row["count"] . "</td></tr>";

    $output .= '
                <tr>
                    <td>' . $x . '</td>
                    <td>' . $row['month']  . '</td>
                    <td>' . $override->getNews('sites', 'status', 1, 'id', $row['site_id'])[0]['name']  . '</td>
                    <td>' . $row['count']  . '</td>
                </tr>
            ';

    $x += 1;
}

$output .= '
                <tr>
                    <td align="right" colspan="3"><b>Total</b></td>
                    <td align="center"><b>' . $registered . '</b></td>
                </tr>              
';

// $output = '<html><body><h1>Hello, dompdf!' . $row . '</h1></body></html>';
$pdf->loadHtml($output);

// SetPaper the HTML as PDF
// $pdf->setPaper('A4', 'portrait');
$pdf->setPaper('A4', 'landscape');

// Render the HTML as PDF
$pdf->render();

// Output the generated PDF
$pdf->stream($file_name, array("Attachment" => false));
