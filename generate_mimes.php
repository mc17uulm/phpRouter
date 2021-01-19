<?php

$list = file_get_contents('http://svn.apache.org/repos/asf/httpd/httpd/trunk/docs/conf/mime.types');
$lines = explode("\n", $list);
$lines = array_filter($lines, function(string $line) {
    if(strlen($line) === 0) return false;
    return substr($line, 0, 1) !== "#";
});

$result = "<?php\n\n return [\n";

foreach($lines as $line) {
    $stripped = preg_replace('/\s+/', '|', $line);
    $parts = explode("|", $stripped);
    if(count($parts) > 1) {
        for($i = 1; $i < count($parts); $i++) {
            $result .= "\t\"" . $parts[$i] . "\" => \"" . $parts[0] . "\",\n";
        }
    }
}

$result .= "];";

file_put_contents(__DIR__ . "/mimes.php", $result);