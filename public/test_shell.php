<?php
$cmd = '"C:\\Program Files\\LibreOffice\\program\\soffice.exe" --version';
$p = proc_open($cmd, [['pipe','r'],['pipe','w'],['pipe','w']], $pipes);

if (is_resource($p)) {
    $stdout = stream_get_contents($pipes[1]);
    $stderr = stream_get_contents($pipes[2]);
    proc_close($p);
    echo 'stdout: ' . ($stdout ?: 'vacío');
    echo '<br>stderr: ' . ($stderr ?: 'vacío');
} else {
    echo 'proc_open FALLÓ';
}
