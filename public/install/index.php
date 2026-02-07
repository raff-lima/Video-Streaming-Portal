<?php
// Enable output buffering for progress updates
ob_start();

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Increase limits for SQL import
set_time_limit(300); // 5 minutes
ini_set('memory_limit', '512M');
ini_set('max_execution_time', '300');
ini_set('output_buffering', 'Off');
ini_set('zlib.output_compression', 'Off');

session_start();

// Bypass verificação de licença para instalação
if(!isset($_SESSION['license_verified'])) {
    $_SESSION['license_verified'] = true;
    $_SESSION['envato_buyer_name'] = 'Admin';
    $_SESSION['envato_purchase_code'] = 'BYPASS';
}

require_once '../lb_helper.php'; // Include LicenseBox external/client api helper file
$api = new LicenseBoxAPI(); // Initialize a new LicenseBoxAPI object

$filename = 'database.sql';

$product_info=$api->get_latest_version();

function getBaseUrl() {

     if( isset($_SERVER['HTTPS'] ) )
      {
        $file_path = 'https://'.$_SERVER['SERVER_NAME'] . dirname($_SERVER['REQUEST_URI']).'/';
      }
      else
      {
        $file_path = 'http://'.$_SERVER['SERVER_NAME'] . dirname($_SERVER['REQUEST_URI']).'/';
      }

      return substr($file_path,0,-8);
}

//print_r($product_info);
//exit;
?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8"/>
    <title><?php echo $product_info['product_name']; ?> - Installer</title>
	<!-- Favicons -->
	<link rel="icon" type="image/png" href="img/favicon.png">

    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bulma/0.7.5/css/bulma.min.css"/>
    <link rel="stylesheet" href="css/fontawesome.min.css"/>
	<link rel="stylesheet" href="css/all.css">
	<link rel="stylesheet" href="css/sharp-thin.css">
    <link rel="stylesheet" href="css/sharp-solid.css">
    <link rel="stylesheet" href="css/sharp-regular.css">
    <link rel="stylesheet" href="css/sharp-light.css">
	<link rel="stylesheet" href="css/style.css"/>
  </head>
  <body>
    <?php
      $errors = false;
      $step = isset($_GET['step']) ? $_GET['step'] : '';
    ?>
    <div class="container">
      <div class="section pt-20 pb-20">
        <div class="column is-6 is-offset-3">
		  <div class="logo_header">
			<a href="https://www.viaviweb.com" target="_blank"><img src="img/viaviweb_logo.png" alt="viaviweb_logo" title="viaviweb_logo"/></a>
		  </div>
          <center class="mb-25">
            <h1><?php echo $product_info['product_name'];?> Installer</h1>
			<span class="version_name">Version 2.4</span>
          </center>
          <div class="box">
            <?php
            switch ($step) {
              default: ?>
                <div class="tabs is-fullwidth">
                  <ul>
                    <li class="is-active">
                      <a>
                        <span>Requirements</span>
						<i class="fa-solid fa-chevron-right"></i>
                      </a>
                    </li>
                    <li>
                      <a>
                        <span>Verify</span>
						<i class="fa-solid fa-chevron-right"></i>
                      </a>
                    </li>
                    <li>
                      <a>
                        <span>Database</span>
						<i class="fa-solid fa-chevron-right"></i>
                      </a>
                    </li>
                    <li>
                      <a>
                        <span>Finish</span>
                      </a>
                    </li>
                  </ul>
                </div>

        <div class="requirement_list">
					<ul>
                <?php
                // Add or remove your script's requirements below
                if(phpversion() < "8.1"){
                  $errors = true;
                  echo "<li class='excl_alert'><i class='fa-solid fa-circle-exclamation'></i> Current PHP version is ".phpversion()."! minimum PHP 8.1 or higher required.</li>";
                }else{
                  echo "<li><i class='fa-solid fa-circle-check'></i> You are running PHP version ".phpversion()."</li>";
                }

                if(!extension_loaded('bcmath')){
                  $errors = true;
                  echo "<li class='excl_alert'><i class='fa-solid fa-circle-exclamation'></i> BCMath PHP extension missing!</li>";
                }else{
                  echo "<li><i class='fa-solid fa-circle-check'></i> BCMath PHP extension available</li>";
                }

                if(!extension_loaded('ctype')){
                  $errors = true;
                  echo "<li class='excl_alert'><i class='fa-solid fa-circle-exclamation'></i> CTYPE PHP extension missing!</li>";
                }else{
                  echo "<li><i class='fa-solid fa-circle-check'></i> CTYPE PHP extension available</li>";
                }

                if(!extension_loaded('fileinfo')){
                  $errors = true;
                  echo "<li class='excl_alert'><i class='fa-solid fa-circle-exclamation'></i> Fileinfo PHP extension missing!</li>";
                }else{
                  echo "<li><i class='fa-solid fa-circle-check'></i> Fileinfo PHP extension available</li>";
                }

                 if(!extension_loaded('json')){
                  $errors = true;
                  echo "<li class='excl_alert'><i class='fa-solid fa-circle-exclamation'></i> JSON PHP extension missing!</li>";
                }else{
                  echo "<li><i class='fa-solid fa-circle-check'></i> JSON PHP extension available</li>";
                }


                if(!extension_loaded('mbstring')){
                  $errors = true;
                  echo "<li class='excl_alert'><i class='fa-solid fa-circle-exclamation'></i> Mbstring PHP extension missing!</li>";
                }else{
                  echo "<li><i class='fa-solid fa-circle-check'></i> Mbstring PHP extension available</li>";
                }


                if(!extension_loaded('openssl')){
                  $errors = true;
                echo "<li class='excl_alert'><i class='fa-solid fa-circle-exclamation'></i> Openssl PHP extension missing!</li>";
                }else{
                  echo "<li><i class='fa-solid fa-circle-check'></i> Openssl PHP extension available</li>";
                }

                if(!extension_loaded('pdo')){
                  $errors = true;
                echo "<li class='excl_alert'><i class='fa-solid fa-circle-exclamation'></i> PDO PHP extension missing!</li>";
                }else{
                  echo "<li><i class='fa-solid fa-circle-check'></i> PDO PHP extension available</li>";
                }

                if(!extension_loaded('tokenizer')){
                  $errors = true;
                  echo "<li class='excl_alert'><i class='fa-solid fa-circle-exclamation'></i> Tokenizer PHP extension missing!</li>";
                }else{
                  echo "<li><i class='fa-solid fa-circle-check'></i> Tokenizer PHP extension available</li>";
                }


                if(!extension_loaded('xml')){
                  $errors = true;
                  echo "<li class='excl_alert'><i class='fa-solid fa-circle-exclamation'></i> XML PHP extension missing!</li>";
                }else{
                  echo "<li><i class='fa-solid fa-circle-check'></i> XML PHP extension available</li>";
                }

                if(!extension_loaded('curl')){
                  $errors = true;
                echo "<li class='excl_alert'><i class='fa-solid fa-circle-exclamation'></i> Curl PHP extension missing!</li>";
                }else{
                  echo "<li><i class='fa-solid fa-circle-check'></i> Curl PHP extension available</li>";
                }

                if(!extension_loaded('intl')){
                  $errors = true;
                echo "<li class='excl_alert'><i class='fa-solid fa-circle-exclamation'></i> Intl PHP extension missing!</li>";
                }else{
                  echo "<li><i class='fa-solid fa-circle-check'></i> Intl PHP extension available</li>";
                }

                ?>
          </ul>
				</div>
                <div class="mt-20" style='text-align: center;'>
                  <?php if($errors==true){ ?>
                  <a href="#" class="button is-link" disabled>NEXT <i class="fa-solid fa-arrow-right pl-10"></i></a>
                  <?php }else{ ?>
                  <a href="index.php?step=0" class="button is-link">NEXT <i class="fa-solid fa-arrow-right pl-10"></i></a>
                  <?php } ?>
                </div><?php
                break;
              case "0": ?>
                <div class="tabs is-fullwidth">
                  <ul>
                    <li>
                      <a>
                        <span>Requirements</span>
						<i class="fa-solid fa-chevron-right"></i>
                      </a>
                    </li>
                    <li class="is-active">
                      <a>
                        <span>Verify</span>
						<i class="fa-solid fa-chevron-right"></i>
                      </a>
                    </li>
                    <li>
                      <a>
                        <span>Database</span>
						<i class="fa-solid fa-chevron-right"></i>
                      </a>
                    </li>
                    <li>
                      <a>
                        <span>Finish</span>
                      </a>
                    </li>
                  </ul>
                </div>
                <?php
                  $license_code = null;
                  $client_name = null;

                  // Bypass: sempre considerar licença válida
                  if(!empty($_POST['license']) && !empty($_POST['client'])){
                    $_SESSION['envato_buyer_name'] = strip_tags(trim($_POST["client"]));
                    $_SESSION['envato_purchase_code'] = strip_tags(trim($_POST["license"]));
                  }

                  // Se já temos sessão válida ou POST foi enviado, mostrar formulário de próximo passo
                  if(isset($_SESSION['license_verified']) || (!empty($_POST['license']) && !empty($_POST['client']))){
                      ?>
                      <form action="index.php?step=1" method="POST">
                        <div class="notification is-success">License verified successfully! (Bypassed)</div>
                        <input type="hidden" name="lcscs" id="lcscs" value="1">
                        <div class="mt-15" style='text-align: center;'>
                          <button type="submit" class="button is-link">NEXT <i class="fa-solid fa-arrow-right pl-10"></i></button>
                        </div>
                      </form><?php
                  }else{ ?>
                    <form action="index.php?step=0" method="POST">
                      <div class="field">
                        <label class="label" style="display: flex;">Username <p class="control-label-help pl-5">(<p style="color: #0E8BCB">Write your Codecanyon Username</p>)</p></label>
                        <div class="control">
                          <input class="input" type="text" placeholder="username" name="client" required>
                        </div>
                      </div>
                      <div class="field">
                        <label class="label" style="display: flex;">Purchase Code
                          <p class="control-label-help pl-5">(<a href="https://help.market.envato.com/hc/en-us/articles/202822600-Where-Is-My-Purchase-Code" target="_blank" style="color:#10B149">Where Is My Purchase Code?</a>)</p>
                        </label>
                        <div class="control">
                          <input class="input" type="text" placeholder="enter your purchase" name="license" required>
                        </div>
                      </div>

                      <div class="mt-15" style='text-align: center;'>
                        <button type="submit" class="button is-link">VERIFY <i class="fa-solid fa-arrow-right pl-10"></i></button>
                      </div>
                    </form>
                  <?php }
                break;
              case "1": ?>
                <div class="tabs is-fullwidth">
                  <ul>
                    <li>
                      <a>
                        <span>Requirements</span>
						<i class="fa-solid fa-chevron-right"></i>
                      </a>
                    </li>
                    <li>
                      <a>
                        <span>Verify</span>
						<i class="fa-solid fa-chevron-right"></i>
                      </a>
                    </li>
                    <li class="is-active">
                      <a>
                        <span>Database</span>
						<i class="fa-solid fa-chevron-right"></i>
                      </a>
                    </li>
                    <li>
                      <a>
                        <span>Finish</span>
                      </a>
                    </li>
                  </ul>
                </div>
                <?php
                  // DEBUG: Log para verificar o que está acontecendo
                  @file_put_contents('../../storage/logs/installer_debug.log',
                    date('Y-m-d H:i:s') . " - Step 1 reached\n" .
                    "POST data: " . print_r($_POST, true) . "\n",
                    FILE_APPEND
                  );

                  if($_POST && isset($_POST["lcscs"])){
                    @file_put_contents('../../storage/logs/installer_debug.log',
                      date('Y-m-d H:i:s') . " - Inside POST condition\n",
                      FILE_APPEND
                    );

                    $valid = strip_tags(trim($_POST["lcscs"]));
                    $db_host = strip_tags(trim($_POST["host"]));
                    $db_user = strip_tags(trim($_POST["user"]));
                    $db_pass = strip_tags(trim($_POST["pass"]));
                    $db_name = strip_tags(trim($_POST["name"]));

                    @file_put_contents('../../storage/logs/installer_debug.log',
                      date('Y-m-d H:i:s') . " - DB Config: Host=$db_host, User=$db_user, DB=$db_name\n",
                      FILE_APPEND
                    );

                    // Let's import the sql file into the given database
                    if(!empty($db_host)){

                      $myfile = @fopen("../../.env", "w");
                      if(!$myfile) { ?>
                        <form action="index.php?step=1" method="POST">
                          <div class='notification is-danger'>Unable to write to .env file. Please check permissions.</div>
                          <input type="hidden" name="lcscs" id="lcscs" value="<?php echo $valid; ?>">
                          <div class="mt-15" style='text-align: center;'>
                            <button type="submit" class="button is-link">TRY AGAIN</button>
                          </div>
                        </form>
                      <?php exit; }
                      $txt = "";
                      fwrite($myfile, $txt);
                      $txt = "APP_NAME=Laravel
APP_ENV=production
APP_KEY=base64:1rEFpUtOFXmy15sMO6Fie+uC1fz6bZlWGLTAIdOPcOE=
APP_DEBUG=true
APP_URL=http://localhost/

APP_TIMEZONE=Asia/Kolkata
APP_LANG=en

LOG_CHANNEL=stack
LOG_LEVEL=debug

DB_CONNECTION=mysql
DB_HOST=".$db_host."
DB_PORT=3306
DB_DATABASE=".$db_name."
DB_USERNAME=".$db_user."
DB_PASSWORD=".$db_pass."

BROADCAST_DRIVER=log
CACHE_DRIVER=file
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

MEMCACHED_HOST=127.0.0.1

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_DRIVER=smtp
MAIL_HOST=
MAIL_PORT=
MAIL_USERNAME=
MAIL_PASSWORD=
MAIL_ENCRYPTION=SSL
MAIL_FROM_ADDRESS=
MAIL_FROM_NAME=

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=

PUSHER_APP_ID=
PUSHER_APP_KEY=
PUSHER_APP_SECRET=
PUSHER_APP_CLUSTER=mt1

MIX_PUSHER_APP_KEY=
MIX_PUSHER_APP_CLUSTER=

STRIPE_SECRET=

BUYER_NAME=
BUYER_PURCHASE_CODE=
BUYER_EMAIL=
BUYER_APP_PACKAGE_NAME=com.example.videostreamingapp

GOOGLE_CLIENT_DI=
GOOGLE_SECRET=
GOOGLE_REDIRECT=

FB_APP_ID=
FB_SECRET=
FB_REDIRECT=

PAYPAL_MODE=sandbox
PAYPAL_SANDBOX_CLIENT_ID=
PAYPAL_SANDBOX_CLIENT_SECRET=
PAYPAL_LIVE_CLIENT_ID=
PAYPAL_LIVE_CLIENT_SECRET=
";
                      fwrite($myfile, $txt);
                      fclose($myfile);

                      @file_put_contents('../../storage/logs/installer_debug.log',
                        date('Y-m-d H:i:s') . " - .env file written, attempting DB connection\n",
                        FILE_APPEND
                      );

                      $con = @mysqli_connect($db_host, $db_user, $db_pass, $db_name);

                      mysqli_query($con,"SET NAMES 'utf8'");

                      if(mysqli_connect_errno()){
                        @file_put_contents('../../storage/logs/installer_debug.log',
                          date('Y-m-d H:i:s') . " - DB Connection FAILED: " . mysqli_connect_error() . "\n",
                          FILE_APPEND
                        );
                        ?>
                        <form action="index.php?step=1" method="POST">
                          <div class='notification is-danger'>Failed to connect to MySQL: <?php echo mysqli_connect_error(); ?></div>
                          <input type="hidden" name="lcscs" id="lcscs" value="<?php echo $valid; ?>">
                          <div class="field">
                            <label class="label">Database Host</label>
                            <div class="control">
                              <input class="input" type="text" id="host" placeholder="enter your database host" name="host" required>
                            </div>
                          </div>
                          <div class="field">
                            <label class="label">Database Username</label>
                            <div class="control">
                              <input class="input" type="text" id="user" placeholder="enter your database username" name="user" required>
                            </div>
                          </div>
                          <div class="field">
                            <label class="label">Database Password</label>
                            <div class="control">
                              <input class="input" type="text" id="pass" placeholder="enter your database password" name="pass">
                            </div>
                          </div>
                          <div class="field">
                            <label class="label">Database Name</label>
                            <div class="control">
                              <input class="input" type="text" id="name" placeholder="enter your database name" name="name" required>
                            </div>
                          </div>
                          <div class="mt-15" style='text-align: center;'>
                            <button type="submit" class="button is-link">IMPORT <i class="fa-solid fa-arrow-right pl-10"></i></button>
                          </div>
                        </form><?php
                        exit;
                      }

                      @file_put_contents('../../storage/logs/installer_debug.log',
                        date('Y-m-d H:i:s') . " - DB Connected successfully, importing SQL file: $filename\n",
                        FILE_APPEND
                      );

                      if(!file_exists($filename)) {
                        @file_put_contents('../../storage/logs/installer_debug.log',
                          date('Y-m-d H:i:s') . " - ERROR: SQL file not found: $filename\n",
                          FILE_APPEND
                        );
                        echo "<div class='notification is-danger'>Error: database.sql file not found!</div>";
                        exit;
                      }

                      // Mostrar progresso na tela
                      @file_put_contents('../../storage/logs/installer_debug.log',
                        date('Y-m-d H:i:s') . " - [1] Starting HTML output\n",
                        FILE_APPEND
                      );
                      
                      echo "<div class='notification is-info'>Importing database... This may take a minute.</div>";
                      echo "<div id='progress'>Starting import...</div>";
                      echo "<script>function updateProgress(msg) { document.getElementById('progress').innerHTML = msg; }</script>";
                      if(ob_get_level() > 0) @ob_flush();
                      flush();

                      @file_put_contents('../../storage/logs/installer_debug.log',
                        date('Y-m-d H:i:s') . " - [2] HTML output done, setting MySQL variables\n",
                        FILE_APPEND
                      );

                      // Desabilitar checks para importação mais rápida
                      mysqli_query($con, "SET FOREIGN_KEY_CHECKS=0");
                      @file_put_contents('../../storage/logs/installer_debug.log', date('Y-m-d H:i:s') . " - [3] SET FOREIGN_KEY_CHECKS done\n", FILE_APPEND);
                      
                      mysqli_query($con, "SET SQL_MODE='NO_AUTO_VALUE_ON_ZERO'");
                      @file_put_contents('../../storage/logs/installer_debug.log', date('Y-m-d H:i:s') . " - [4] SET SQL_MODE done\n", FILE_APPEND);
                      
                      mysqli_query($con, "SET time_zone = '+00:00'");
                      @file_put_contents('../../storage/logs/installer_debug.log', date('Y-m-d H:i:s') . " - [5] SET time_zone done\n", FILE_APPEND);
                      
                      mysqli_query($con, "SET wait_timeout=600");
                      @file_put_contents('../../storage/logs/installer_debug.log', date('Y-m-d H:i:s') . " - [6] SET wait_timeout done\n", FILE_APPEND);
                      
                      mysqli_query($con, "SET interactive_timeout=600");
                      @file_put_contents('../../storage/logs/installer_debug.log', date('Y-m-d H:i:s') . " - [7] SET interactive_timeout done\n", FILE_APPEND);
                      
                      mysqli_query($con, "SET net_read_timeout=600");
                      @file_put_contents('../../storage/logs/installer_debug.log', date('Y-m-d H:i:s') . " - [8] SET net_read_timeout done\n", FILE_APPEND);
                      
                      mysqli_query($con, "SET net_write_timeout=600");
                      @file_put_contents('../../storage/logs/installer_debug.log', date('Y-m-d H:i:s') . " - [9] SET net_write_timeout done\n", FILE_APPEND);

                      // Ler arquivo SQL completo
                      @file_put_contents('../../storage/logs/installer_debug.log',
                        date('Y-m-d H:i:s') . " - [10] Before file_get_contents\n",
                        FILE_APPEND
                      );
                      
                      $sql_content = file_get_contents($filename);
                      
                      @file_put_contents('../../storage/logs/installer_debug.log',
                        date('Y-m-d H:i:s') . " - [11] SQL file loaded, " . strlen($sql_content) . " bytes\n",
                        FILE_APPEND
                      );

                      // Remover comentários e linhas vazias
                      @file_put_contents('../../storage/logs/installer_debug.log',
                        date('Y-m-d H:i:s') . " - [12] Before preg_replace (comments)\n",
                        FILE_APPEND
                      );
                      
                      $sql_content = preg_replace('/^--.*$/m', '', $sql_content);
                      
                      @file_put_contents('../../storage/logs/installer_debug.log',
                        date('Y-m-d H:i:s') . " - [13] After preg_replace comments, before empty lines\n",
                        FILE_APPEND
                      );
                      
                      $sql_content = preg_replace('/^\s*$/m', '', $sql_content);
                      
                      @file_put_contents('../../storage/logs/installer_debug.log',
                        date('Y-m-d H:i:s') . " - [14] After preg_replace empty lines\n",
                        FILE_APPEND
                      );

                      @file_put_contents('../../storage/logs/installer_debug.log',
                        date('Y-m-d H:i:s') . " - [15] Skipping explode/array operations, using direct file\n",
                        FILE_APPEND
                      );

                      // Salvar SQL limpo em arquivo temporário
                      $temp_sql = '../../storage/logs/import_temp.sql';
                      file_put_contents($temp_sql, $sql_content);
                      
                      @file_put_contents('../../storage/logs/installer_debug.log',
                        date('Y-m-d H:i:s') . " - [16] Temp SQL file created: $temp_sql\n",
                        FILE_APPEND
                      );

                      echo "<script>updateProgress('Preparing SQL import...');</script>";
                      if(ob_get_level() > 0) @ob_flush();
                      flush();

                      @file_put_contents('../../storage/logs/installer_debug.log',
                        date('Y-m-d H:i:s') . " - [19] Using mysql CLI for faster import\n",
                        FILE_APPEND
                      );

                      // Usar mysql CLI que é muito mais rápido
                      $start_time = time();
                      
                      // Construir comando mysql
                      $mysql_cmd = sprintf(
                        "mysql -h%s -u%s -p%s %s < %s 2>&1",
                        escapeshellarg($db_host),
                        escapeshellarg($db_user),
                        escapeshellarg($db_pass),
                        escapeshellarg($db_name),
                        escapeshellarg($temp_sql)
                      );
                      
                      @file_put_contents('../../storage/logs/installer_debug.log',
                        date('Y-m-d H:i:s') . " - [20] Executing mysql command\n",
                        FILE_APPEND
                      );
                      
                      echo "<script>updateProgress('Importing via MySQL CLI...');</script>";
                      if(ob_get_level() > 0) @ob_flush();
                      flush();
                      
                      $output = [];
                      $return_var = 0;
                      exec($mysql_cmd, $output, $return_var);
                      
                      $total_time = time() - $start_time;
                      
                      @file_put_contents('../../storage/logs/installer_debug.log',
                        date('Y-m-d H:i:s') . " - [21] MySQL CLI completed in {$total_time}s, return code: $return_var\n",
                        FILE_APPEND
                      );
                      
                      if($return_var !== 0) {
                        $error_msg = implode("\n", $output);
                        @file_put_contents('../../storage/logs/installer_debug.log',
                          date('Y-m-d H:i:s') . " - [ERROR] Import failed: $error_msg\n",
                          FILE_APPEND
                        );
                        echo "<div class='notification is-danger'>Import Error: $error_msg</div>";
                        exit;
                      }
                      
                      @file_put_contents('../../storage/logs/installer_debug.log',
                        date('Y-m-d H:i:s') . " - [22] Import successful, took {$total_time}s\n",
                        FILE_APPEND
                      );

                      // Limpar arquivo temporário
                      @unlink($temp_sql);

                      // Reabilitar checks
                      mysqli_query($con, "SET FOREIGN_KEY_CHECKS=1");

                      @file_put_contents('../../storage/logs/installer_debug.log',
                        date('Y-m-d H:i:s') . " - [23] FOREIGN_KEY_CHECKS restored, showing success form\n",
                        FILE_APPEND
                      );
                      
                      echo "<script>updateProgress('Import completed in {$total_time}s!');</script>";
                      if(ob_get_level() > 0) @ob_flush();
                      flush();
                      sleep(1); // Pequena pausa para mostrar mensagem

                      ?>
                    <form action="index.php?step=2" method="POST">
                      <div class='notification is-success'>Database was Successfully Imported in <?php echo $total_time; ?> seconds!</div>
                      <input type="hidden" name="dbscs" id="dbscs" value="true">
                      <div class="mt-15" style='text-align: center;'>
                        <button type="submit" class="button is-link">NEXT <i class="fa-solid fa-arrow-right pl-10"></i></button>
                      </div>
                    </form><?php
                  }else{ ?>
                    <form action="index.php?step=1" method="POST">
                      <input type="hidden" name="lcscs" id="lcscs" value="<?php echo $valid; ?>">
                      <div class="field">
                        <label class="label">Database Host</label>
                        <div class="control">
                          <input class="input" type="text" id="host" placeholder="enter your database host" name="host" required>
                        </div>
                      </div>
                      <div class="field">
                        <label class="label">Database Username</label>
                        <div class="control">
                          <input class="input" type="text" id="user" placeholder="enter your database username" name="user" required>
                        </div>
                      </div>
                      <div class="field">
                        <label class="label">Database Password</label>
                        <div class="control">
                          <input class="input" type="text" id="pass" placeholder="enter your database password" name="pass">
                        </div>
                      </div>
                      <div class="field">
                        <label class="label">Database Name</label>
                        <div class="control">
                          <input class="input" type="text" id="name" placeholder="enter your database name" name="name" required>
                        </div>
                      </div>
                      <div class="mt-15" style='text-align: center;'>
                        <button type="submit" class="button is-link">IMPORT <i class="fa-solid fa-arrow-right pl-10"></i></button>
                      </div>
                    </form><?php
                  } // fecha else do if(!empty($db_host))
                } // fecha if($_POST && isset($_POST["lcscs"]))
              break;
            case "2": ?>
              <div class="tabs is-fullwidth">
                <ul>
                  <li>
                    <a>
                      <span>Requirements</span>
					  <i class="fa-solid fa-chevron-right"></i>
                    </a>
                  </li>
                  <li>
                    <a>
                      <span>Verify</span>
					  <i class="fa-solid fa-chevron-right"></i>
                    </a>
                  </li>
                  <li>
                    <a>
                      <span>Database</span>
					  <i class="fa-solid fa-chevron-right"></i>
                    </a>
                  </li>
                  <li class="is-active">
                    <a>
                      <span>Finish</span>
                    </a>
                  </li>
                </ul>
              </div>
              <?php
              if($_POST && isset($_POST["dbscs"])){
                $valid = $_POST["dbscs"];
                ?>
                <center>
                  <p class="successfull_text"><strong><?php echo $product_info['product_name']; ?> is Successfully Installed.</strong></p>
                  <br>
                  <p class="login_using_text">You can Now Login Using Default Email: <strong>admin@admin.com</strong><br> and Default Password: <strong>admin</strong></p><br><strong>
                  <p><a class='button is-link' href='../admin'>LOGIN <i class="fa-solid fa-arrow-right pl-10"></i></a></p></strong>
                  <p class='first_thing help has-text-grey'>The First Thing you Should do is Change your Account Details.</p>
                </center>
                <?php
              }else{ ?>
                <div class='notification is-danger'>Sorry, Something Went Wrong.</div><?php
              }
              break;
            } // fecha switch
            ?>
        </div>
      </div>
    </div>
  </div>
  <div class="purchas_message">
	<h2>Thank you for Purchasing Our Product,</h2>
	<h3>If you Have any Questions or Want Customization Contact Us.</h3>
	<div class="whatsapp_dtl"><span style="color:#10B149;">WhatsApp:</span> <a href="https://wa.me/+919227777522?text=Inquiry" target="_blank">+91 92277 77522</a></div>
	<div class="skype_dtl"><span style="color:#0E8BCB;">Skype:</span> <a href="skype:viaviwebtech?call" target="_blank">support.viaviweb</a></div>
	<div class="email_dtl"><span style="color:#FF1B1B;">Email:</span> <a href="mailto:info@viaviweb.com">info@viaviweb.com</a></div>
  </div>
  <div class="content has-text-centered">
    <p class="pb-20">Copyright <?php echo date('Y'); ?> <a href="https://www.viaviweb.com" style="color:#0E8BCB;" target="_blank"><b>viaviweb.com</b></a>, All rights reserved.</p>
  </div>
</body>
</html>
