<?php
//image section
function formatImage(&$img)
{
    $extension = pathinfo($img["name"])["extension"];
    $img["name"] = time() . "." . $extension;
}
function validateImage($img)
{

    //array usado para verificar as validações correram bem
    $valid = ["size" => false, "extension" => false, "dimensions" => false, "mime" => false, "width" => false, "height" => false];

    $extension = strtolower(pathinfo($img["name"])["extension"]);
    $extensions = ["jpg", "png", "webp", "jpeg"];

    $mimes = ["image/png", "image/jpg", "image/webp", "image/jpeg"];
    $mime_file = mime_content_type($img["tmp_name"]);

    //1KB=1024b
    //1MB=1KB*1KB
    $size_file = $img["size"];
    $size_max = 4 * 1024 * 1024;

    //dimensões das images
    $width_image = getimagesize($img["tmp_name"])[0];
    $height_image = getimagesize($img["tmp_name"])[1];

    //limites das dimensões
    $width = ["min" => 200, "max" => 1200];
    $height = ["min" => 200, "max" => 1200];
    $valid["extension"] = in_array($extension, $extensions);//retorna true ou false
    $valid["size"] = ($size_file <= $size_max);//mesma coisa de cima
    $valid["mime"] = in_array($mime_file, $mimes);

    $valid["width"] = ($width_image >= $width["min"] && $width_image <= $width["max"]);
    $valid["height"] = ($height_image >= $height["min"] && $height_image <= $height["max"]);

    $valid["dimensions"] = ($valid["width"] && $valid["height"]);//mesma coisa...

    if (!$valid["extension"])
        return ["success" => false, "message" => "O imagem deve ser JPG, PNG, WEBP ou JPEG!"];
    else if (!$valid["size"])
        return ["success" => false, "message" => "A imagem deve pesar menos de 4MB"];
    else if (!$valid["mime"])
        return ["success" => false, "message" => "Apenas ficheiros de imagem permitidos"];
    else if (!$valid["width"])
        return ["success" => false, "message" => "A largura da imagem deve conter entre {$width['min']} e {$width['max']} pixels"];
    else if (!$valid["height"])
        return ["success" => false, "message" => "A altura da imagem deve conter entre {$height['min']} e {$height['max']} pixels"];


    return ["success" => true];
}
function imagePath($enterprise, $image)
{
    if (str_contains($enterprise, " ")) {
        $enterprise = str_replace(" ", "_", $enterprise);
    }

    $savePath = "../private/files/images/$enterprise/";
    $getImagePath = "/stagely/backend/private/files/images/$enterprise/";
    if (!file_exists($savePath)) {
        mkdir($savePath, 0755, true);
    }
    return ["path" => $savePath . $image["name"], "getPath" => $getImagePath . $image["name"]];
}
/*
function filePath($user, $file)
{
    $path = "/stagely/backend/private/files/evaluations/$user/";
    if (!file_exists($path)) {
        mkdir($path, 0755, true);
    }
    return $path . $file;
}
    */
function saveFile($archive, $path)
{

    if (empty($archive)) {
        return ["success" => false, 'message' => 'algo deu errado ao receber o seu arquivo'];
    } else {
        $res = validateImage($archive);

        if ($res) {
            $upload = move_uploaded_file($archive["tmp_name"], $path);

            if (!$upload) {
                return ["success" => false, 'message' => 'Ocorreu algum erro ao salvar o arquivo. Tente novamente!'];
            }
            return ["success" => true, 'message' => 'Arquivo salvo com sucesso!'];

        } else {
            return ["success" => false, 'message' => 'Formato de arquivo inválido!'];
        }
    }
}
function doAllImageThings($image, $enterprise = null)
{
    $validImage = validateImage($image);
    if (!$validImage["success"])
        return $validImage;

    formatImage($image);
    //save and get paths
    $path = imagePath($enterprise, $image);

    $save = saveFile($image, $path["path"]);
    if (!$save["success"])
        return $save;

    return ["success" => true, "path" => $path["path"], "getPath" => $path["getPath"]];
}

function deleteEnterpriseFiles($enterprise)
{
    $path = "../private/files/images/$enterprise/";
    if (!file_exists($path) || !is_dir($path))
        return ["success" => true];
    $files = scandir($path);
    foreach ($files as $file) {

        if ($file == "." || $file == "..")
            continue;

        $res = unlink("$path/$file");
        if (!$res)
            return ["success" => false];
    }

    $delFolder = rmdir($path);
    if (!$delFolder)
        return ["success" => false];

    return ["success" => true];
}

////////////////file section//////////////////////
//////////////////////////////////////////////////
//////////////////////////////////////////////////
//////////////////////////////////////////////////
function validateFile($file)
{
    $valid = ["extension" => false, "mime" => false, "size" => false, "fullfiled" => false];
    $extension = strtolower(pathinfo($file["name"])["extension"]);
    $validExtension = ["pdf"];

    $validMime = ["application/pdf"];
    $fileMime = mime_content_type($file["tmp_name"]);

    $fileSize = $file["size"];
    $maxSize = 10 * 1024 * 1024;

    $valid["extension"] = in_array($extension, $validExtension);
    $valid["mime"] = in_array($fileMime, $validMime);
    $valid["size"] = ($fileSize <= $maxSize);
    $valid["fullfiled"] = (isset($file) && !empty($file));

    if (!$valid["extension"])
        return ["success" => false, "message" => "Apenas arquivos PDF"];
    else if (!$valid["mime"])
        return ["success" => false, "message" => "Apenas arquivos PDF"];
    else if (!$valid["size"])
        return ["success" => false, "message" => "O tamanho não deve exceder 20MB"];
    else if (!$valid["fullfiled"])
        return ["success" => false, "message" => "Erro ao ler arquivo"];
    return ["success" => true];
}
function saveInternerFile($file, $path)
{
    /*if (file_exists($path))
        return ["success" => false, "message" => "Já existe"];*/

    if (empty($file)) {
        return ["success" => false, 'message' => 'algo deu errado ao receber o seu arquivo'];
    } else {
        $res = validateFile($file);

        if ($res) {
            $upload = move_uploaded_file($file["tmp_name"], $path);

            if (!$upload) {
                return ["success" => false, 'message' => 'Ocorreu algum erro'];
            }
            return ["success" => true, 'message' => 'Arquivo salvo com sucesso!'];

        } else {
            return ["success" => false, 'message' => 'Ocorreu algum erro'];
        }
    }
}
function filePath($file, $course)
{
    $savePath = "../private/files/evaluations/$course/";
    $getFilePath = "/stagely/backend/private/files/evaluations/$course/";
    if (!file_exists($savePath)) {
        mkdir($savePath, 0755, true);
    }
    return ["path" => $savePath . $file["name"], "getPath" => $getFilePath . $file["name"]];
}
function formatFile(&$file, $name)
{
    $extension = pathinfo($file["name"])["extension"];
    $file["name"] = $name . "." . $extension;
}
function doAllEvaluationThings($file, $name, $course)
{
    if (!$file)
        return ["success" => false, "message" => "Erro ao ler arquivo"];

    $validFile = validateFile($file);
    if (!$validFile["success"])
        return $validFile;

    formatFile($file, $name);

    $path = filePath($file, $course);

    $save = saveInternerFile($file, $path["path"]);
    if (!$save)
        return $save;

    return ["success" => true, "path" => $path["path"], "getPath" => $path["getPath"]];
}

//--------------------PEGAR AVALIAÇÕES DO DIRETÓRIO POR CURSO------------------------------
/*
function getFilesDirPerCourse($course)
{
    $path = "../private/files/evaluations";
    $evaluationsDir = scandir($path);
    foreach ($evaluationsDir as $dir) {
        if (str_contains(strtolower($dir), strtolower($course))) {
            return ["success" => true, "path" => "$path/$dir"];
        }
        else
            return ["success"=>false,"path"=>null];
    }
    return ["success" => false];
}
function getFilesFromDir($dir)
{
    if($dir["path"]==null)
        return ["success"=>false,"paths"=>null];
    $files = scandir($dir["path"]);
    $filesPath = [];
    $filesNames = [];
    foreach ($files as $f) {
        if ($f === "." || $f === "..")
            continue;
        $filesNames[] = $f;
        $filesPath[] = $dir["path"] . "/$f";
    }
    return ["names" => $filesNames, "paths" => $filesPath];
}
function formatFilesPath($filesArr)
{
    if($filesArr["paths"]==null)
        return ["success"=>false];
    foreach ($filesArr["paths"] as $i => $f) {
        $filesArr["paths"][$i] = str_replace("../", "/stagely/backend/", $filesArr["paths"][$i]);
    }
    return ["success"=>true,"data"=>$filesArr];
}
function getFormatedEvaluationsPath($course)
{
    $coursesDir = getFilesDirPerCourse($course);
    $coursesDirFiles = getFilesFromDir($coursesDir);
    $formatedDirFilePaths = formatFilesPath($coursesDirFiles);
    return $formatedDirFilePaths;
}
    */
//---------------------------------------------------------------------------------
function getExistentEvaluationsFromDir($evaluations)
{
    $newEvaluationsArr=[];
    if($evaluations["success"]===true && count($evaluations["data"])===0)
        return ["success"=>true,"data"=>[]];

    foreach ($evaluations["data"] as $i => $eva) {
        $evaluations["data"][$i]["caminho_arquivo"] = str_replace("/stagely/backend/", "../", $evaluations["data"][$i]["caminho_arquivo"]);
        
        if(file_exists($evaluations["data"][$i]["caminho_arquivo"])){
            $evaluations["data"][$i]["caminho_arquivo"] = str_replace("../", "/stagely/backend/", $evaluations["data"][$i]["caminho_arquivo"]);
            $newEvaluationsArr[]=$evaluations["data"][$i];
        }
    }
    return ["success"=>true,"data"=>$newEvaluationsArr];
}