<?php
// Carica il file xml
$xml = simplexml_load_file('');

// Crea le cartelle per articoli e immagini se non esistono
$articlesFolder = 'articles';
$imgFolder = 'IMG';

if (!file_exists($articlesFolder)) {
    mkdir($articlesFolder);
}

if (!file_exists($imgFolder)) {
    mkdir($imgFolder);
}

// Cicla per ogni <content> e crea array vuoto
foreach ($xml->content as $content){
    $data = [];
// Cicla nuovamente ma su ogni <component> e ne individua il valore dell' attribute "group"
    foreach($content->component as $component){
        $group = (string)$component['group'];
        $value = (string)$component; // Estrae il valore del componente

        switch ($group) {
            case 'Content':
                $data['Titolo'] = $value;
                break;
            case 'ContentId':
                $articoloID = $value;
                $data['ID articolo'] = $articoloID;
                break;
            case 'summary':
                $data['Abstract'] = $value;
                break;
            case 'text':
                $data['Testo'] = $value;
                break;
            case 'author':
                $data['Autore'] = $value;
                break;
            case 'pathsegment':
                $data['URL'] = $value;
                break;
            case 'security-parent':
                $categories = [];
                foreach ($component->externalid as $externalid) {
                    $categories[] = (string)$externalid->major;
                }
                $data['Categorie'] = implode('.', $categories);
                break;
                
        }
    }

    // Cicla la immagini e le decodifica (?)
    foreach ($content->file as $file) {
        $imgName = $file['name'];
        $imgDecoded = base64_decode((string)$file);
        $img = imagecreatefromstring($imgDecoded);
        file_put_contents("$imgFolder/$img", $imgDecoded);
       
    }
    
    // Genera i campi vuoti nell' array "data"
    $data += ['Titolo' => '', 'ID articolo' => '', 'Abstract' => '', 'Testo' => '', 'Autore' => '', 'URL' => '', 'Categorie' => ''];
    
    // Trasforma l'array in json
    $articleJSON = json_encode($data, JSON_PRETTY_PRINT);

    // Crea nome json e mette nella cartella
    $jsonFileName = $articlesFolder . "/articolo_" . $data['ID articolo'] . ".json";
    file_put_contents($jsonFileName, $articleJSON);
}
?>
