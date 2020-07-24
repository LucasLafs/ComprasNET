<?php 

set_time_limit(0);
ignore_user_abort(false);
require_once ("/var/www/html/ComprasNET/ajax/conexao.php");

if($_REQUEST['act']){
    if ($_REQUEST['act'] == 'requestLicitacoes'){
        return startWorker();
    } else if ($_REQUEST['act'] == 'requestLicitacoesApp') {
        return startWorker();
    } else if ($_REQUEST['act'] == 'getTimeout'){
        return getTimeout();
    } else if ($_REQUEST['act'] == 'saveTimeout'){
        return saveTimeout();
    } else {
        echo "404 NOT FOUND";
    }
}

function saveTimeout(){

    $time = $_REQUEST['time'];

    $con = bancoMysqli();
    $sql = "DELETE FROM timeout";
    if (!mysqli_query($con, $sql)) {
        echo "ERROR: " . mysqli_error($con);
        echo "<br>";
        echo $sql;
    }

    $sql = "INSERT INTO timeout (minutos) VALUES ($time)";
    if (!mysqli_query($con, $sql)) {
        echo "ERROR: " . mysqli_error($con);
        echo "<br>";
        echo $sql;
    } else {
        $op = $time / 60;
        $hour = '*';
        $min = '*';

        if ($op > 1) {
            $hour = "*/" . round($time / 60);
        } else {
            $min = "*/" . $time; 
        }

        // $cmd = 'for i in `ps aux| grep timeout | awk \'{print$2}\' `; do kill -9 "$i" ; done ';
        $cmd = "sudo chown www-data /etc/crontab";
        shell_exec($cmd);
        file_put_contents('/etc/crontab', "# /etc/crontab: system-wide crontab\n# Unlike any other crontab you don't have to run the `crontab'\n# command to install the new version when you edit this file\n# and files in /etc/cron.d. These files also have username fields,\n# that none of the other crontabs do.\nSHELL=/bin/sh\nPATH=/usr/local/sbin:/usr/local/bin:/sbin:/bin:/usr/sbin:/usr/bin\n# m h dom mon dow user  command\n#17 *    * * *   root    cd / && run-parts --report /etc/cron.hourly\n#25 6    * * *   root    test -x /usr/sbin/anacron || ( cd / && run-parts --report /etc/cron.daily )\n#47 6    * * 7   root    test -x /usr/sbin/anacron || ( cd / && run-parts --report /etc/cron.weekly )\n#52 6    1 * *   root    test -x /usr/sbin/anacron || ( cd / && run-parts --report /etc/cron.monthly )\n$min $hour   * * *   root    /var/www/html/ComprasNET/launcher.sh\n#5 0,2,4,6,8,10,12,14,16,18,20,22  * * *   root    /var/www/html/ComprasNET/rotate.sh\n#");
        $cmd = "sudo chown root.root /etc/crontab";
        shell_exec($cmd);
        $cmd = "sudo systemctl restart cron";
        shell_exec($cmd);

        echo '1';
        exit;
    }
}

function getTimeout(){

    $con = bancoMysqli();
    $sql = "SELECT minutos FROM timeout";

    $query = mysqli_query($con, $sql);
    $obj = array();
    if($query){

        while($row = mysqli_fetch_assoc($query)){
            $obj[] = $row['minutos'];
        }

        echo json_encode($obj);
    } else {
        echo '0';
        exit;
    }
}

function startWorker(){
    $cmd = "/var/www/html/ComprasNET/rotate.sh";
    shell_exec($cmd);

    $cmd = "/var/www/html/ComprasNET/launcher.sh";
    shell_exec($cmd);

    echo '1';
    exit; 
}

