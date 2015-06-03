<?

$log = file_get_contents("/var/log/apache2/access.log");

echo "/rex/ has been GET requested ".(substr_count($log, "GET /rex/ HTTP/1.1")-847)." times\n";;
echo "/rex/events.jon has been GET requested ".(substr_count($log, "GET /rex/events2.json HTTP/1.1")-(78+147))." times";

?>
