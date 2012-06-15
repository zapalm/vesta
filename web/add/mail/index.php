<?php
// Init
error_reporting(NULL);
ob_start();
session_start();
$TAB = 'MAIL';
include($_SERVER['DOCUMENT_ROOT']."/inc/main.php");

// Header
include($_SERVER['DOCUMENT_ROOT'].'/templates/header.html');

// Panel
top_panel($user,$TAB);

// Are you admin?
if ($_SESSION['user'] == 'admin') {

    // Cancel
    if (!empty($_POST['cancel'])) {
        header("Location: /list/mail/");
    }

    // Mail Domain
    if (!empty($_POST['ok'])) {
        if (empty($_POST['v_domain'])) $errors[] = 'domain';
        if (!empty($_POST['v_antispam'])) {
            $v_antispam = 'yes';
        } else {
            $v_antispam = 'no';
        }

        if (!empty($_POST['v_antivirus'])) {
            $v_antivirus = 'yes';
        } else {
            $v_antivirus = 'no';
        }

        if (!empty($_POST['v_dkim'])) {
            $v_dkim = 'yes';
        } else {
            $v_dkim = 'no';
        }

        // Protect input
        $v_domain = preg_replace("/^www./i", "", $_POST['v_domain']);
        $v_domain = escapeshellarg($v_domain);

        // Check for errors
        if (!empty($errors[0])) {
            foreach ($errors as $i => $error) {
                if ( $i == 0 ) {
                    $error_msg = $error;
                } else {
                    $error_msg = $error_msg.", ".$error;
                }
            }
            $_SESSION['error_msg'] = "Error: field ".$error_msg." can not be blank.";
        } else {

            // Add mail domain
            exec (VESTA_CMD."v_add_mail_domain ".$user." ".$v_domain." ".$v_antispam." ".$v_antivirus." ".$v_dkim, $output, $return_var);
            if ($return_var != 0) {
                $error = implode('<br>', $output);
                if (empty($error)) $error = 'Error: vesta did not return any output.';
                $_SESSION['error_msg'] = $error;
            }
            unset($output);

            if (empty($_SESSION['error_msg'])) {
                $_SESSION['ok_msg'] = "OK: domain <b>".$_POST[v_domain]."</b> has been created successfully.";
                unset($v_domain);
            }
        }
    }


    // Mail Account
    if (!empty($_POST['ok_acc'])) {
        // Check input
        if (empty($_POST['v_domain'])) $errors[] = 'domain';
        if (empty($_POST['v_account'])) $errors[] = 'account';
        if (empty($_POST['v_password'])) $errors[] = 'password';

        // Protect input
        $v_domain = escapeshellarg($_POST['v_domain']);
        $v_account = escapeshellarg($_POST['v_account']);
        $v_password = escapeshellarg($_POST['v_password']);
        $v_quota = escapeshellarg($_POST['v_quota']);
        if (empty($_POST['v_quota'])) $v_quota = 0;

        // Check for errors
        if (!empty($errors[0])) {
            foreach ($errors as $i => $error) {
                if ( $i == 0 ) {
                    $error_msg = $error;
                } else {
                    $error_msg = $error_msg.", ".$error;
                }
            }
            $_SESSION['error_msg'] = "Error: field ".$error_msg." can not be blank.";
        } else {
            // Add Mail Account
            exec (VESTA_CMD."v_add_mail_account ".$user." ".$v_domain." ".$v_account." ".$v_password." ".$v_quota, $output, $return_var);
            if ($return_var != 0) {
                $error = implode('<br>', $output);
                if (empty($error)) $error = 'Error: vesta did not return any output.';
                $_SESSION['error_msg'] = $error;
            }
            unset($output);
            if (empty($_SESSION['error_msg'])) {
                $_SESSION['ok_msg'] = "OK: account <b>".$_POST['v_account']."</b> has been created successfully.";
                unset($v_account);
                unset($v_password);
            }
        }
    }


    if ((empty($_GET['domain'])) && (empty($_POST['domain'])))  {
        $v_domain = $_GET['domain'];
        include($_SERVER['DOCUMENT_ROOT'].'/templates/admin/menu_add_mail.html');
        include($_SERVER['DOCUMENT_ROOT'].'/templates/admin/add_mail.html');
        unset($_SESSION['error_msg']);
        unset($_SESSION['ok_msg']);
    } else {
        $v_domain = $_GET['domain'];
        include($_SERVER['DOCUMENT_ROOT'].'/templates/admin/menu_add_mail_acc.html');
        include($_SERVER['DOCUMENT_ROOT'].'/templates/admin/add_mail_acc.html');
        unset($_SESSION['error_msg']);
        unset($_SESSION['ok_msg']);
    }
}

// Footer
include($_SERVER['DOCUMENT_ROOT'].'/templates/footer.html');