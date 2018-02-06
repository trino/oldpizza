<?php
    $root = left(public_path(), strlen(public_path()) - 6);
    if(!isset($files)){$files = $_GET["files"];}
    if(!is_array($files)){$files = explode(",", $files);}
    if (in_array("api", $files)){//api must always go first
        $index = array_search("api", $files);
        unset( $files[$index] );
        $files = array_merge(array("api"), $files);
    }

    $path = "resources/assets/scripts/";
    if(isset($_GET["path"])){
        $path = $_GET["path"];
        if(right($_GET["path"],1) != '/' && right($_GET["path"],1) != "\\"){$path .= '/';}
    }
    $workingfile = $root . $path . str_replace(".js", "", implode("-", $files)) . "-min.js";

    $workingtimestamp = 0;
    $forcenew = isset($_GET["forcenew"]) || isset($forcenew);
    $myfilename = myself($view_name);
    $mytimestamp = filemtime($myfilename);
    if(isset($_GET["leavealone"])){$leavealone=true;}
    $minimize = !isset($leavealone);

    $minimize=false;//comment out in post.

    if(file_exists($workingfile) && !$forcenew){
        $workingtimestamp = filemtime($workingfile);
        if($mytimestamp >= $workingtimestamp){
            $workingtimestamp = 0;
        } else {
            foreach($files as $file){
                if(strpos($file, ".") === false){
                    $file .= ".js";
                }
                $file = $root . $path . $file;
                if(file_exists($file)){
                    $timestamp = filemtime($file);
                    if($timestamp > $workingtimestamp){
                        $workingtimestamp = 0;
                    }
                }
            }
            if($workingtimestamp){//file is up-to-date, just spit it out
                echo file_get_contents($workingfile);
            }
        }
    }

    if($workingtimestamp == 0){//file is not up-to-date or does not exist, update it
        $entirefile = "/* Generated at " . time () . " */";
        foreach($files as $file){
            $orig = $file;
            if(strpos($file, ".") === false){
                $file .= ".js";
            }
            $file = $root . $path . $file;
            if(file_exists($file)){
                $entirefile .= " /*" . $orig . "*/ " . minify_JS(file_get_contents($file), $minimize);
            } else {
                $entirefile .= " /*" . $orig . " NOT FOUND!*/ ";
            }
        }
        file_put_contents($workingfile, $entirefile);
        echo $entirefile;
    }

    function myself($view_name){
        return resource_path() . "/views/" . str_replace(".", "/", $view_name) . ".blade.php";
    }

    function minify_JS($code, $minimize = true){
        if(!$minimize){return $code;}
        $code = str_replace(array(";", "//"), array(" ; ", " // "), $code);//error handling
        $code = preg_replace( "/(?<!\:)\/\/(.*)\\n/", "", $code );//single line comments
        $code = preg_replace('!/\*.*?\*/!s', '', $code);//multi line comments
        $code = preg_replace('/\n\s*\n/', "\n", $code);//multi line comments

        $code = str_replace(array("\n","\r", "\t"), '',$code);// make it into one long line
        $code = filterduplicates($code, "   ", "  ");// replace all triple spaces with two spaces (two spaces are needed)
        $code = str_replace(array('new Array()', 'new Array', "{ ", "} "), array('[]', '[]', "{", "}"),$code);

        foreach(array(")", "+", "=", "<", ">", "||", ";", ";", "{", "}", "&&") as $char){//",", "(", ":", and double spaces must remain
            $code = str_replace(array($char . " ", " " . $char, " " . $char . " "), $char, $code);
        }

        return $code;// replace some unneeded spaces, modify as needed
    }

    function filterduplicates($text, $filter = "  ", $withwhat = " "){
        while (strpos($text, $filter) !== false){
            $text = str_replace($filter, $withwhat, $text);
        }
        return $text;
    }
?>