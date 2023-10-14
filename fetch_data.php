<?php
require 'vendor/autoload.php';



// open aplicanti.json file
$aplicanti = file_get_contents('aplicanti.json');

$total_entries = count(json_decode($aplicanti, true));

// split json file to 250 elements array
$aplicanti = array_chunk(json_decode($aplicanti, true), 250);

$current_applicant_index = 250;
foreach($aplicanti as $aplicanti_batch) {
    //extract cuis from array
    $cifs = array_column($aplicanti_batch, 'cui');
    $anaf = new \Itrack\Anaf\Client(); 
    $anaf->addCif($cifs);
    $records = $anaf->get();

    foreach($records as $company) {


        // organize all data into array
        $data = [
            'cui' => $company->getCIF(),
            'nume' => $company->getName(),
            'adresa' => $company->getFullAddress(),
            'judet' => $company->getAddress()->getCounty(),
            'localitate' => $company->getAddress()->getCity(),
            'strada' => $company->getAddress()->getStreet(),
            'numar' => $company->getAddress()->getStreetNumber(),
            'cod_postal' => $company->getAddress()->getPostalCode(),
            'telefon' => $company->getPhone(),
            'data_infiintarii' => $company->getReactivationDate(),
            'data_inactivarii' => $company->getInactivationDate(),
            'data_radiere' => $company->getDeletionDate(),
            'activ' => $company->isActive(),
            'tva' => $company->getTVA()->hasTVA(),
            'data_tva' => $company->getTVA()->getTVAEnrollDate(),
            'data_sfarsit_tva' => $company->getTVA()->getTVAEndDate(),
            'tva_split' => $company->getTVA()->hasTVASplit(),
            'data_tva_split' => $company->getTVA()->getTVASplitEnrollDate(),
            'data_sfarsit_tva_split' => $company->getTVA()->getTVASplitEndDate(),
            'iban_tva_split' => $company->getTVA()->getTVASplitIBAN(),
            'tva_incasare' => $company->getTVA()->hasTVACollection(),
            'data_tva_incasare' => $company->getTVA()->getTVACollectionEnrollDate(),
            'data_sfarsit_tva_incasare' => $company->getTVA()->getTVACollectionEndDate(),
            'data' => date('Y-m-d H:i:s')
        ];

        // append data to json file using json_encode and comma and pretty print
        file_put_contents('data.json', json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . ',', FILE_APPEND);

    }
        // output current progress using total entries and current increment value
        echo 'Progress: ' . $current_applicant_index . '/' . $total_entries . PHP_EOL;
        
        // increase current applicant index with batch size
        $current_applicant_index += count($aplicanti_batch);
        sleep(2);

}
