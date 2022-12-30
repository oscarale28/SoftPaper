<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;
use SMTPValidateEmail\Validator as SmtpEmailValidator;

require('../libraries/PHPMailer/src/Exception.php');
require('../libraries/PHPMailer/src/PHPMailer.php');
require('../libraries/PHPMailer/src/SMTP.php');
require('../libraries/SMTPMailValidator/src/Validator.php');

class Mail extends Validator
{
    //Seteamos atributos a usar en todos los mail que se manden
    protected static function getMail()
    {
        $mail = new PHPMailer();
        //Indicar a PHPMailer que utilice SMTP
        $mail->isSMTP();
        //Configurar el nombre del servidor de correo
        $mail->Host = 'smtp.gmail.com';
        //Si se utiliza la autenticación SMTP
        $mail->SMTPAuth = true;
        //Nombre de usuario a utilizar para la autenticación SMTP - utilice la dirección de correo electrónico completa para gmail
        $mail->Username = 'softpapersv@gmail.com';
        //Contraseña a utilizar para la autenticación SMTP
        $mail->Password = 'svsoioeohqbhiyon';
        //Establece el mecanismo de encriptación a utilizar:
        // - SMTPS (TLS implícito en el puerto 465) o
        // - STARTTLS (TLS explícito en el puerto 587)
        $mail->SMTPSecure = 'tls';
        //Establece el número de puerto SMTP:
        // - 465 para SMTP con TLS implícito, también conocido como RFC8314 SMTPS o
        // - 587 para SMTP+STARTTLS
        $mail->Port = 587;

        $mail->charSet = 'UTF-8';

        return $mail;
    }

    //Método para enviar mensaje de doble autenticación------------------------
    public function sendVerificationMessage($emailTo, $subject, $message)
    {
        //Obtenemos atributos generales
        $mail = self::getMail();

        //Establecer de quién se enviará el mensaje
        //Nota que con gmail sólo puedes usar la dirección de tu cuenta (igual que `Nombre de usuario`)
        //o los alias predefinidos que hayas configurado en tu cuenta.
        //No utilices aquí direcciones enviadas por el usuario
        $mail->setFrom('softpapersv@gmail.com', 'SoftPaper');

        //Establecer una dirección alternativa de respuesta
        //Este es un buen lugar para poner las direcciones enviadas por los usuarios
        //$mail->addReplyTo('replyto@example.com', 'First Last');
        //Establece a quién se debe enviar el mensaje
        if (is_array($emailTo)) {
            foreach ($emailTo as $key => $value) {
                $mail->addAddress($emailTo);
            }
        }
        else{
            $mail->addAddress($emailTo);
        }

        //Establecer la línea de asunto
        $mail->isHtml(true);
        $mail->Subject = utf8_decode($subject);

        //Sustituir el cuerpo de texto plano por uno creado manualmente
        $mail->Body = '<!DOCTYPE html>
        <html lang="en" xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
        <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="x-apple-disable-message-reformatting">
        <meta name="format-detection" content="telephone=no">
        <title></title>
        
        <link href="https://fonts.googleapis.com/css?family=Lato" rel="stylesheet" type="text/css">
        <link href="https://fonts.googleapis.com/css?family=Roboto%20Condensed" rel="stylesheet" type="text/css">
        <link href="https://fonts.googleapis.com/css?family=Roboto&amp;subset=latin-ext" rel="stylesheet" type="text/css">
        <!--##custom-font-resource##-->
        <!--[if gte mso 16]>
        <xml>
        <o:OfficeDocumentSettings>
        <o:AllowPNG/>
        <o:PixelsPerInch>96</o:PixelsPerInch>
        </o:OfficeDocumentSettings>
        </xml>
        <![endif]-->
        <style>
        html,body,table,tbody,tr,td,div,p,ul,ol,li,h1,h2,h3,h4,h5,h6 {
        margin: 0;
        padding: 0;
        }
        
        body {
        -ms-text-size-adjust: 100%;
        -webkit-text-size-adjust: 100%;
        }
        
        table {
        border-spacing: 0;
        mso-table-lspace: 0pt;
        mso-table-rspace: 0pt;
        }
        
        table td {
        border-collapse: collapse;
        }
        
        h1,h2,h3,h4,h5,h6 {
        font-family: Arial;
        }
        
        .ExternalClass {
        width: 100%;
        }
        
        .ExternalClass,
        .ExternalClass p,
        .ExternalClass span,
        .ExternalClass font,
        .ExternalClass td,
        .ExternalClass div {
        line-height: 100%;
        }
        
        /* Outermost container in Outlook.com */
        .ReadMsgBody {
        width: 100%;
        }
        
        img {
        -ms-interpolation-mode: bicubic;
        }
        
        </style>
        
        <style>
        a[x-apple-data-detectors=true]{
        color: inherit !important;
        text-decoration: inherit !important;
        }
        
        u + #body a {
        color: inherit;
        text-decoration: inherit !important;
        font-size: inherit;
        font-family: inherit;
        font-weight: inherit;
        line-height: inherit;
        }
        
        a, a:link, .no-detect-local a, .appleLinks a {
        color: inherit !important;
        text-decoration: inherit;
        }
        
        </style>
        
        <style>
        
        .width600 {
        width: 600px;
        max-width: 100%;
        }
        
        @media all and (max-width: 599px) {
        .width600 {
        width: 100% !important;
        }
        }
        
        @media screen and (min-width: 600px) {
        .hide-on-desktop {
        display: none !important;
        }
        }
        
        @media all and (max-width: 599px),
        only screen and (max-device-width: 599px) {
        .main-container {
        width: 100% !important;
        }
        
        .col {
        width: 100%;
        }
        
        .fluid-on-mobile {
        width: 100% !important;
        height: auto !important;
        text-align:center;
        }
        
        .fluid-on-mobile img {
        width: 100% !important;
        }
        
        .hide-on-mobile {
        display:none !important;
        width:0px !important;
        height:0px !important;
        overflow:hidden;
        }
        }
        
        </style>
        
        
        <!--[if gte mso 9]>
        <style>
        
        .col {
        width: 100%;
        }
        
        .width600 {
        width: 600px;
        }
        
        .width90 {
        width: 90px;
        height: auto;
        }
        
        .hide-on-desktop {
        display: none;
        }
        
        .hide-on-desktop table {
        mso-hide: all;
        }
        
        .hide-on-desktop div {
        mso-hide: all;
        }
        
        .nounderline {text-decoration: none; }
        
        
        
        </style>
        <![endif]-->
        
        </head>
        <body id="body" leftmargin="0" marginwidth="0" topmargin="0" marginheight="0" offset="0" style="font-family:Arial, sans-serif; font-size:0px;margin:0;padding:0;background-color:#ffffff;">
        <style>
        @media screen and (min-width: 600px) {
        .hide-on-desktop {
        display: none;
        }
        }
        @media all and (max-width: 599px) {
        .hide-on-mobile {
        display:none !important;
        width:0px !important;
        height:0px !important;
        overflow:hidden;
        }
        .main-container {
        width: 100% !important;
        }
        .col {
        width: 100%;
        }
        .fluid-on-mobile {
        width: 100% !important;
        height: auto !important;
        text-align:center;
        }
        .fluid-on-mobile img {
        width: 100% !important;
        }
        }
        </style>
        <div style="background-color:#ffffff;">
        <table height="100%" width="100%" cellpadding="0" cellspacing="0" border="0">
        <tr>
        <td valign="top" align="left">
        <table cellpadding="0" cellspacing="0" border="0" width="100%">
        <tr>
        <td width="100%">
        <table cellpadding="0" cellspacing="0" border="0" width="100%">
        <tr>
        <td align="center" width="100%">
        <!--[if gte mso 9]><table width="600" cellpadding="0" cellspacing="0"><tr><td><![endif]-->
        <table class="width600 main-container" cellpadding="0" cellspacing="0" border="0" width="100%" style="max-width:600px;">
        <tr>
        <td width="100%">
        <table cellpadding="0" cellspacing="0" border="0" width="100%">
        <tr>
        <td valign="top">
        <table cellpadding="0" cellspacing="0" border="0" width="100%">
        <tr>
        <td valign="top" align="center" style="padding:20px;"><!--[if gte mso 9]><table width="90" cellpadding="0" cellspacing="0"><tr><td><![endif]-->
        <table cellpadding="0" cellspacing="0" border="0" style="max-width:100%;" class="img-wrap">
        <tr>
        <td valign="top" align="center"><img src="https://images.chamaileon.io/631a88a1ca74416a7f0746d3/631a88a1ca744152880746d5/1662954019795_logo gmail (1).png" width="90" height="90" alt="" border="0" style="display:block;font-size:14px;max-width:100%;height:auto;" class="width90" />
        </td>
        </tr>
        </table>
        <!--[if gte mso 9]></td></tr></table><![endif]-->
        </td>
        </tr>
        </table>
        <table cellpadding="0" cellspacing="0" border="0" width="100%">
        <tr>
        <td valign="top" style="padding-top:5px;padding-right:10px;padding-bottom:5px;padding-left:10px;"><div style="font-family:Roboto Condensed, Arial Narrow, Roboto, sans-serif;font-size:25px;color:#131313;line-height:25px;text-align:left;"><p style="padding: 0; margin: 0;text-align: center;"><span class="mso-font-fix-arial"><span style="color:#005070;">¡Código para iniciar sesión! </span></span></p>
        <p style="padding: 0; margin: 0;text-align: center;">&nbsp;</p></div>
        </td>
        </tr>
        </table>
        <table cellpadding="0" cellspacing="0" border="0" width="100%">
        <tr>
        <td valign="top" style="padding-top:5px;padding-right:10px;padding-bottom:5px;padding-left:10px;"><div style="font-family:Lato, Helvetica Neue, Helvetica, Arial, sans-serif;font-size:16px;color:#131313;line-height:25px;text-align:left;"><p style="padding: 0; margin: 0;"><span class="mso-font-fix-arial">¡Hola!</span></p>
        <p style="padding: 0; margin: 0;"><span class="mso-font-fix-arial">Este&nbsp;es el código de verificación&nbsp;de un solo uso para poder iniciar sesión:</span></p>
        <p style="padding: 0; margin: 0;">&nbsp;</p>
        <p style="padding: 0; margin: 0;"><span class="mso-font-fix-arial">Ten en&nbsp;cuenta&nbsp;que este código se puede utilizar solo una vez y expirará en 2&nbsp;minutos. Si no solicitaste el&nbsp;código&nbsp;es posible que otra persona quiera usar tu cuenta.</span></p>
        <p style="padding: 0; margin: 0;">&nbsp;</p></div>
        </td>
        </tr>
        </table>
        <table cellpadding="0" cellspacing="0" border="0" width="100%">
        <tr>
        <td valign="top" style="padding-top:5px;padding-right:10px;padding-bottom:5px;padding-left:10px;"><div style="font-family:Arial, Helvetica Neue, Helvetica, sans-serif;font-size:18px;color:#131313;line-height:25px;text-align:left;"><p style="padding: 0; margin: 0;text-align: center;"><span class="mso-font-fix-arial"><strong>Código: '.$message;'</strong></span></p></div>
        </td>
        </tr>
        </table>
        <table cellpadding="0" cellspacing="0" border="0" width="100%">
        <tr>
        <td valign="top" style="padding-top:35px;">
        <table cellpadding="0" cellspacing="0" border="0" width="100%">
        <tr>
        <td valign="top">
        </td>
        </tr>
        </table>
        </td>
        </tr>
        </table>
        </td>
        </tr>
        </table>
        </td>
        </tr>
        </table>
        <!--[if gte mso 9]></td></tr></table><![endif]-->
        </td>
        </tr>
        </table>
        </td>
        </tr>
        </table>
        </td>
        </tr>
        </table>
        </div>
        </body>
        </html>';

        //Enviar el mensaje, comprobar si hay errores
        if (!$mail->send()) {
            echo $mail->ErrorInfo;
            return $mail->ErrorInfo;
        } else {
            return true;
        }
    }

    //Método para enviar código de confirmación de recuperación de contraseña------------------------
    public function sendVerificationMessageR($emailTo, $subject, $message)
    {
        $mail = self::getMail();

        //Establecer de quién se enviará el mensaje
        //Nota que con gmail sólo puedes usar la dirección de tu cuenta (igual que `Nombre de usuario`)
        //o los alias predefinidos que hayas configurado en tu cuenta.
        //No utilices aquí direcciones enviadas por el usuario
        $mail->setFrom('softpapersv@gmail.com', 'SoftPaper');

        //Establecer una dirección alternativa de respuesta
        //Este es un buen lugar para poner las direcciones enviadas por los usuarios
        //$mail->addReplyTo('replyto@example.com', 'First Last');
        //Establece a quién se debe enviar el mensaje
        if (is_array($emailTo)) {
            foreach ($emailTo as $key => $value) {
                $mail->addAddress($emailTo);
            }
        }
        else{
            $mail->addAddress($emailTo);
        }

        //Establecer la línea de asunto
        $mail->isHtml(true);
        $mail->Subject = utf8_decode($subject);

        //Sustituir el cuerpo de texto plano por uno creado manualmente
        $mail->Body = '<!DOCTYPE html>
        <html lang="en" xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
        <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="x-apple-disable-message-reformatting">
        <meta name="format-detection" content="telephone=no">
        <title></title>
        
        <link href="https://fonts.googleapis.com/css?family=Lato" rel="stylesheet" type="text/css">
        <link href="https://fonts.googleapis.com/css?family=Roboto%20Condensed" rel="stylesheet" type="text/css">
        <link href="https://fonts.googleapis.com/css?family=Roboto&amp;subset=latin-ext" rel="stylesheet" type="text/css">
        <!--##custom-font-resource##-->
        <!--[if gte mso 16]>
        <xml>
        <o:OfficeDocumentSettings>
        <o:AllowPNG/>
        <o:PixelsPerInch>96</o:PixelsPerInch>
        </o:OfficeDocumentSettings>
        </xml>
        <![endif]-->
        <style>
        html,body,table,tbody,tr,td,div,p,ul,ol,li,h1,h2,h3,h4,h5,h6 {
        margin: 0;
        padding: 0;
        }
        
        body {
        -ms-text-size-adjust: 100%;
        -webkit-text-size-adjust: 100%;
        }
        
        table {
        border-spacing: 0;
        mso-table-lspace: 0pt;
        mso-table-rspace: 0pt;
        }
        
        table td {
        border-collapse: collapse;
        }
        
        h1,h2,h3,h4,h5,h6 {
        font-family: Arial;
        }
        
        .ExternalClass {
        width: 100%;
        }
        
        .ExternalClass,
        .ExternalClass p,
        .ExternalClass span,
        .ExternalClass font,
        .ExternalClass td,
        .ExternalClass div {
        line-height: 100%;
        }
        
        /* Outermost container in Outlook.com */
        .ReadMsgBody {
        width: 100%;
        }
        
        img {
        -ms-interpolation-mode: bicubic;
        }
        
        </style>
        
        <style>
        a[x-apple-data-detectors=true]{
        color: inherit !important;
        text-decoration: inherit !important;
        }
        
        u + #body a {
        color: inherit;
        text-decoration: inherit !important;
        font-size: inherit;
        font-family: inherit;
        font-weight: inherit;
        line-height: inherit;
        }
        
        a, a:link, .no-detect-local a, .appleLinks a {
        color: inherit !important;
        text-decoration: inherit;
        }
        
        </style>
        
        <style>
        
        .width600 {
        width: 600px;
        max-width: 100%;
        }
        
        @media all and (max-width: 599px) {
        .width600 {
        width: 100% !important;
        }
        }
        
        @media screen and (min-width: 600px) {
        .hide-on-desktop {
        display: none !important;
        }
        }
        
        @media all and (max-width: 599px),
        only screen and (max-device-width: 599px) {
        .main-container {
        width: 100% !important;
        }
        
        .col {
        width: 100%;
        }
        
        .fluid-on-mobile {
        width: 100% !important;
        height: auto !important;
        text-align:center;
        }
        
        .fluid-on-mobile img {
        width: 100% !important;
        }
        
        .hide-on-mobile {
        display:none !important;
        width:0px !important;
        height:0px !important;
        overflow:hidden;
        }
        }
        
        </style>
        
        
        <!--[if gte mso 9]>
        <style>
        
        .col {
        width: 100%;
        }
        
        .width600 {
        width: 600px;
        }
        
        .width90 {
        width: 90px;
        height: auto;
        }
        
        .hide-on-desktop {
        display: none;
        }
        
        .hide-on-desktop table {
        mso-hide: all;
        }
        
        .hide-on-desktop div {
        mso-hide: all;
        }
        
        .nounderline {text-decoration: none; }
        
        
        </style>
        <![endif]-->
        
        </head>
        <body id="body" leftmargin="0" marginwidth="0" topmargin="0" marginheight="0" offset="0" style="font-family:Arial, sans-serif; font-size:0px;margin:0;padding:0;background-color:#ffffff;">
        <style>
        @media screen and (min-width: 600px) {
        .hide-on-desktop {
        display: none;
        }
        }
        @media all and (max-width: 599px) {
        .hide-on-mobile {
        display:none !important;
        width:0px !important;
        height:0px !important;
        overflow:hidden;
        }
        .main-container {
        width: 100% !important;
        }
        .col {
        width: 100%;
        }
        .fluid-on-mobile {
        width: 100% !important;
        height: auto !important;
        text-align:center;
        }
        .fluid-on-mobile img {
        width: 100% !important;
        }
        }
        </style>
        <div style="background-color:#ffffff;">
        <table height="100%" width="100%" cellpadding="0" cellspacing="0" border="0">
        <tr>
        <td valign="top" align="left">
        <table cellpadding="0" cellspacing="0" border="0" width="100%">
        <tr>
        <td width="100%">
        <table cellpadding="0" cellspacing="0" border="0" width="100%">
        <tr>
        <td align="center" width="100%">
        <!--[if gte mso 9]><table width="600" cellpadding="0" cellspacing="0"><tr><td><![endif]-->
        <table class="width600 main-container" cellpadding="0" cellspacing="0" border="0" width="100%" style="max-width:600px;">
        <tr>
        <td width="100%">
        <table cellpadding="0" cellspacing="0" border="0" width="100%">
        <tr>
        <td valign="top">
        <table cellpadding="0" cellspacing="0" border="0" width="100%">
        <tr>
        <td valign="top" align="center" style="padding:20px;"><!--[if gte mso 9]><table width="90" cellpadding="0" cellspacing="0"><tr><td><![endif]-->
        <table cellpadding="0" cellspacing="0" border="0" style="max-width:100%;" class="img-wrap">
        <tr>
        <td valign="top" align="center"><img src="https://images.chamaileon.io/631a88a1ca74416a7f0746d3/631a88a1ca744152880746d5/1662954019795_logo gmail (1).png" width="90" height="90" alt="" border="0" style="display:block;font-size:14px;max-width:100%;height:auto;" class="width90" />
        </td>
        </tr>
        </table>
        <!--[if gte mso 9]></td></tr></table><![endif]-->
        </td>
        </tr>
        </table>
        <table cellpadding="0" cellspacing="0" border="0" width="100%">
        <tr>
        <td valign="top" style="padding-top:5px;padding-right:10px;padding-bottom:5px;padding-left:10px;"><div style="font-family:Roboto Condensed, Arial Narrow, Roboto, sans-serif;font-size:25px;color:#131313;line-height:25px;text-align:left;"><p style="padding: 0; margin: 0;text-align: center;"><span class="mso-font-fix-arial"><span style="color:#005070;">¡Código para recuperar tu contraseña! </span></span></p>
        <p style="padding: 0; margin: 0;text-align: center;">&nbsp;</p></div>
        </td>
        </tr>
        </table>
        <table cellpadding="0" cellspacing="0" border="0" width="100%">
        <tr>
        <td valign="top" style="padding-top:5px;padding-right:10px;padding-bottom:5px;padding-left:10px;"><div style="font-family:Lato, Helvetica Neue, Helvetica, Arial, sans-serif;font-size:16px;color:#131313;line-height:25px;text-align:left;"><p style="padding: 0; margin: 0;"><span class="mso-font-fix-arial">¡Hola!</span></p>
        <p style="padding: 0; margin: 0;"><span class="mso-font-fix-arial">Este&nbsp;es el código de verificación&nbsp;de un solo uso para restablecer tu contraseña:</span></p>
        <p style="padding: 0; margin: 0;">&nbsp;</p>
        <p style="padding: 0; margin: 0;"><span class="mso-font-fix-arial">Ten en&nbsp;cuenta&nbsp;que este código se puede utilizar solo una vez y expirará en 2&nbsp;minutos. Si no solicitaste un código o si ya iniciaste sesión en&nbsp;tu&nbsp;cuenta&nbsp;con otro método, solo omite este mensaje y nada cambiará.</span></p>
        <p style="padding: 0; margin: 0;">&nbsp;</p></div>
        </td>
        </tr>
        </table>
        <table cellpadding="0" cellspacing="0" border="0" width="100%">
        <tr>
        <td valign="top" style="padding-top:5px;padding-right:10px;padding-bottom:5px;padding-left:10px;"><div style="font-family:Arial, Helvetica Neue, Helvetica, sans-serif;font-size:18px;color:#131313;line-height:25px;text-align:left;"><p style="padding: 0; margin: 0;text-align: center;"><span class="mso-font-fix-arial"><strong>Código: '.$message;'</strong></span></p></div>
        </td>
        </tr>
        </table>
        <table cellpadding="0" cellspacing="0" border="0" width="100%">
        <tr>
        <td valign="top" style="padding-top:35px;">
        <table cellpadding="0" cellspacing="0" border="0" width="100%">
        <tr>
        <td valign="top">
        </td>
        </tr>
        </table>
        </td>
        </tr>
        </table>
        </td>
        </tr>
        </table>
        </td>
        </tr>
        </table>
        <!--[if gte mso 9]></td></tr></table><![endif]-->
        </td>
        </tr>
        </table>
        </td>
        </tr>
        </table>
        </td>
        </tr>
        </table>
        </div>
        </body>
        </html>';

        //Enviar el mensaje, comprobar si hay errores
        if (!$mail->send()) {
            echo $mail->ErrorInfo;
            return $mail->ErrorInfo;
        } else {
            return true;
        }
    }

    //Método para enviar un mensaje de verificacion de recuperación de contraseña------------------------
    public function sendVerificationMessageC($emailTo, $subject)
    {
        $mail = self::getMail();

        //Establecer de quién se enviará el mensaje
        //Nota que con gmail sólo puedes usar la dirección de tu cuenta (igual que `Nombre de usuario`)
        //o los alias predefinidos que hayas configurado en tu cuenta.
        //No utilices aquí direcciones enviadas por el usuario
        $mail->setFrom('softpapersv@gmail.com', 'SoftPaper');

        //Establecer una dirección alternativa de respuesta
        //Este es un buen lugar para poner las direcciones enviadas por los usuarios
        //$mail->addReplyTo('replyto@example.com', 'First Last');
        //Establece a quién se debe enviar el mensaje
        if (is_array($emailTo)) {
            foreach ($emailTo as $key => $value) {
                $mail->addAddress($emailTo);
            }
        }
        else{
            $mail->addAddress($emailTo);
        }

        //Establecer la línea de asunto
        $mail->isHtml(true);
        $mail->Subject = utf8_decode($subject);

        //Sustituir el cuerpo de texto plano por uno creado manualmente
        $mail->Body = '<!DOCTYPE html>
        <html lang="en" xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
        <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="x-apple-disable-message-reformatting">
        <meta name="format-detection" content="telephone=no">
        <title></title>
        
        <link href="https://fonts.googleapis.com/css?family=Lato" rel="stylesheet" type="text/css">
        <link href="https://fonts.googleapis.com/css?family=Roboto%20Condensed" rel="stylesheet" type="text/css">
        <link href="https://fonts.googleapis.com/css?family=Roboto&amp;subset=latin-ext" rel="stylesheet" type="text/css">
        <!--##custom-font-resource##-->
        <!--[if gte mso 16]>
        <xml>
        <o:OfficeDocumentSettings>
        <o:AllowPNG/>
        <o:PixelsPerInch>96</o:PixelsPerInch>
        </o:OfficeDocumentSettings>
        </xml>
        <![endif]-->
        <style>
        html,body,table,tbody,tr,td,div,p,ul,ol,li,h1,h2,h3,h4,h5,h6 {
        margin: 0;
        padding: 0;
        }
        
        body {
        -ms-text-size-adjust: 100%;
        -webkit-text-size-adjust: 100%;
        }
        
        table {
        border-spacing: 0;
        mso-table-lspace: 0pt;
        mso-table-rspace: 0pt;
        }
        
        table td {
        border-collapse: collapse;
        }
        
        h1,h2,h3,h4,h5,h6 {
        font-family: Arial;
        }
        
        .ExternalClass {
        width: 100%;
        }
        
        .ExternalClass,
        .ExternalClass p,
        .ExternalClass span,
        .ExternalClass font,
        .ExternalClass td,
        .ExternalClass div {
        line-height: 100%;
        }
        
        /* Outermost container in Outlook.com */
        .ReadMsgBody {
        width: 100%;
        }
        
        img {
        -ms-interpolation-mode: bicubic;
        }
        
        </style>
        
        <style>
        a[x-apple-data-detectors=true]{
        color: inherit !important;
        text-decoration: inherit !important;
        }
        
        u + #body a {
        color: inherit;
        text-decoration: inherit !important;
        font-size: inherit;
        font-family: inherit;
        font-weight: inherit;
        line-height: inherit;
        }
        
        a, a:link, .no-detect-local a, .appleLinks a {
        color: inherit !important;
        text-decoration: inherit;
        }
        
        </style>
        
        <style>
        
        .width320 {
        width: 320px;
        max-width: 100%;
        }
        
        @media all and (max-width: 319px) {
        .width320 {
        width: 100% !important;
        }
        }
        
        @media screen and (min-width: 600px) {
        .hide-on-desktop {
        display: none !important;
        }
        }
        
        @media all and (max-width: 599px),
        only screen and (max-device-width: 599px) {
        .main-container {
        width: 100% !important;
        }
        
        .col {
        width: 100%;
        }
        
        .fluid-on-mobile {
        width: 100% !important;
        height: auto !important;
        text-align:center;
        }
        
        .fluid-on-mobile img {
        width: 100% !important;
        }
        
        .hide-on-mobile {
        display:none !important;
        width:0px !important;
        height:0px !important;
        overflow:hidden;
        }
        }
        
        </style>
        
        
        <!--[if gte mso 9]>
        <style>
        
        .col {
        width: 100%;
        }
        
        .width320 {
        width: 320px;
        }
        
        .width90 {
        width: 90px;
        height: auto;
        }
        
        .hide-on-desktop {
        display: none;
        }
        
        .hide-on-desktop table {
        mso-hide: all;
        }
        
        .hide-on-desktop div {
        mso-hide: all;
        }
        
        .nounderline {text-decoration: none; }
        
        
        </style>
        <![endif]-->
        
        </head>
        <body id="body" leftmargin="0" marginwidth="0" topmargin="0" marginheight="0" offset="0" style="font-family:Arial, sans-serif; font-size:0px;margin:0;padding:0;background-color:#ffffff;">
        <style>
        @media screen and (min-width: 600px) {
        .hide-on-desktop {
        display: none;
        }
        }
        @media all and (max-width: 599px) {
        .hide-on-mobile {
        display:none !important;
        width:0px !important;
        height:0px !important;
        overflow:hidden;
        }
        .main-container {
        width: 100% !important;
        }
        .col {
        width: 100%;
        }
        .fluid-on-mobile {
        width: 100% !important;
        height: auto !important;
        text-align:center;
        }
        .fluid-on-mobile img {
        width: 100% !important;
        }
        }
        </style>
        <div style="background-color:#ffffff;">
        <table height="100%" width="100%" cellpadding="0" cellspacing="0" border="0">
        <tr>
        <td valign="top" align="left">
        <table cellpadding="0" cellspacing="0" border="0" width="100%">
        <tr>
        <td width="100%">
        <table cellpadding="0" cellspacing="0" border="0" width="100%">
        <tr>
        <td align="center" width="100%">
        <!--[if gte mso 9]><table width="320" cellpadding="0" cellspacing="0"><tr><td><![endif]-->
        <table class="width320 main-container" cellpadding="0" cellspacing="0" border="0" width="100%" style="max-width:320px;">
        <tr>
        <td width="100%">
        <table cellpadding="0" cellspacing="0" border="0" width="100%">
        <tr>
        <td valign="top">
        <table cellpadding="0" cellspacing="0" border="0" width="100%">
        <tr>
        <td valign="top" align="center" style="padding:20px;"><!--[if gte mso 9]><table width="90" cellpadding="0" cellspacing="0"><tr><td><![endif]-->
        <table cellpadding="0" cellspacing="0" border="0" style="max-width:100%;" class="img-wrap">
        <tr>
        <td valign="top" align="center"><img src="https://images.chamaileon.io/631a88a1ca74416a7f0746d3/631a88a1ca744152880746d5/1662954019795_logo gmail (1).png" width="90" height="90" alt="" border="0" style="display:block;font-size:14px;max-width:100%;height:auto;" class="width90" />
        </td>
        </tr>
        </table>
        <!--[if gte mso 9]></td></tr></table><![endif]-->
        </td>
        </tr>
        </table>
        <table cellpadding="0" cellspacing="0" border="0" width="100%">
        <tr>
        <td valign="top" style="padding-top:5px;padding-right:10px;padding-bottom:5px;padding-left:10px;"><div style="font-family:Roboto Condensed, Arial Narrow, Roboto, sans-serif;font-size:25px;color:#131313;line-height:25px;text-align:left;"><p style="padding: 0; margin: 0;text-align: center;"><span class="mso-font-fix-arial"><span style="color:#005070;">¡La contraseña de la cuenta se ha cambiado! </span></span></p>
        <p style="padding: 0; margin: 0;text-align: center;">&nbsp;</p></div>
        </td>
        </tr>
        </table>
        <table cellpadding="0" cellspacing="0" border="0" width="100%">
        <tr>
        <td valign="top" style="padding-top:5px;padding-right:10px;padding-bottom:5px;padding-left:10px;"><div style="font-family:Lato, Helvetica Neue, Helvetica, Arial, sans-serif;font-size:16px;color:#131313;line-height:25px;text-align:left;"><p style="padding: 0; margin: 0;"><span class="mso-font-fix-arial">Si has sido tú, puedes descartar tranquilamente este correo electrónico.</span></p>
        <p style="padding: 0; margin: 0;">&nbsp;</p>
        <p style="padding: 0; margin: 0;"><span class="mso-font-fix-arial">Si no has sido tú, la seguridad de tu cuenta está en peligro.&nbsp;Puedes comunicarte con el encargado.</span></p>
        <p style="padding: 0; margin: 0;">&nbsp;</p>
        <p style="padding: 0; margin: 0;"><span class="mso-ffont-fix-arial">¡Gracias!&nbsp;</span></p>
        <p style="padding: 0; margin: 0;"><span class="mso-font-fix-arial">Libreria Económica.</span></p></div>
        </td>
        </tr>
        </table>
        <table cellpadding="0" cellspacing="0" border="0" width="100%">
        <tr>
        <td valign="top" style="padding-top:35px;">
        <table cellpadding="0" cellspacing="0" border="0" width="100%">
        <tr>
        <td valign="top">
        </td>
        </tr>
        </table>
        </td>
        </tr>
        </table>
        </td>
        </tr>
        </table>
        </td>
        </tr>
        </table>
        <!--[if gte mso 9]></td></tr></table><![endif]-->
        </td>
        </tr>
        </table>
        </td>
        </tr>
        </table>
        </td>
        </tr>
        </table>
        </div>
        </body>
        </html> ';

        //Enviar el mensaje, comprobar si hay errores
        if (!$mail->send()) {
            echo $mail->ErrorInfo;
            return $mail->ErrorInfo;
        } else {
            return true;
        }
    }

    //Alerta de seguridad de sesión en un dispositivo nuevo--------------------------
    public function sendSessionMessage($emailTo, $subject, $ciudad, $dispositivo, $ip)
    {
        $mail = self::getMail();

        //Establecer de quién se enviará el mensaje
        //Nota que con gmail sólo puedes usar la dirección de tu cuenta (igual que `Nombre de usuario`)
        //o los alias predefinidos que hayas configurado en tu cuenta.
        //No utilices aquí direcciones enviadas por el usuario
        $mail->setFrom('softpapersv@gmail.com', 'SoftPaper');

        //Establecer una dirección alternativa de respuesta
        //Este es un buen lugar para poner las direcciones enviadas por los usuarios
        //$mail->addReplyTo('replyto@example.com', 'First Last');
        //Establece a quién se debe enviar el mensaje
        if (is_array($emailTo)) {
            foreach ($emailTo as $key => $value) {
                $mail->addAddress($emailTo);
            }
        }
        else{
            $mail->addAddress($emailTo);
        }

        //Establecer la línea de asunto
        $mail->isHtml(true);
        $mail->Subject = utf8_decode($subject);

        //Sustituir el cuerpo de texto plano por uno creado manualmente
        // Se establece la zona horaria a utilizar
        ini_set('date.timezone', 'America/El_Salvador');
        $mail->Body = '<!DOCTYPE html>
        <html lang="en" xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
        <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="x-apple-disable-message-reformatting">
        <meta name="format-detection" content="telephone=no">
        <title></title>
        
        <link href="https://fonts.googleapis.com/css?family=Lato" rel="stylesheet" type="text/css">
        <link href="https://fonts.googleapis.com/css?family=Roboto%20Condensed" rel="stylesheet" type="text/css">
        <link href="https://fonts.googleapis.com/css?family=Roboto&amp;subset=latin-ext" rel="stylesheet" type="text/css">
        <!--##custom-font-resource##-->
        <!--[if gte mso 16]>
        <xml>
        <o:OfficeDocumentSettings>
        <o:AllowPNG/>
        <o:PixelsPerInch>96</o:PixelsPerInch>
        </o:OfficeDocumentSettings>
        </xml>
        <![endif]-->
        <style>
        html,body,table,tbody,tr,td,div,p,ul,ol,li,h1,h2,h3,h4,h5,h6 {
        margin: 0;
        padding: 0;
        }
        
        body {
        -ms-text-size-adjust: 100%;
        -webkit-text-size-adjust: 100%;
        }
        
        table {
        border-spacing: 0;
        mso-table-lspace: 0pt;
        mso-table-rspace: 0pt;
        }
        
        table td {
        border-collapse: collapse;
        }
        
        h1,h2,h3,h4,h5,h6 {
        font-family: Arial;
        }
        
        .ExternalClass {
        width: 100%;
        }
        
        .ExternalClass,
        .ExternalClass p,
        .ExternalClass span,
        .ExternalClass font,
        .ExternalClass td,
        .ExternalClass div {
        line-height: 100%;
        }
        
        /* Outermost container in Outlook.com */
        .ReadMsgBody {
        width: 100%;
        }
        
        img {
        -ms-interpolation-mode: bicubic;
        }
        
        </style>
        
        <style>
        a[x-apple-data-detectors=true]{
        color: inherit !important;
        text-decoration: inherit !important;
        }
        
        u + #body a {
        color: inherit;
        text-decoration: inherit !important;
        font-size: inherit;
        font-family: inherit;
        font-weight: inherit;
        line-height: inherit;
        }
        
        a, a:link, .no-detect-local a, .appleLinks a {
        color: inherit !important;
        text-decoration: inherit;
        }
        
        </style>
        
        <style>
        
        .width600 {
        width: 600px;
        max-width: 100%;
        }
        
        @media all and (max-width: 599px) {
        .width600 {
        width: 100% !important;
        }
        }
        
        @media screen and (min-width: 600px) {
        .hide-on-desktop {
        display: none !important;
        }
        }
        
        @media all and (max-width: 599px),
        only screen and (max-device-width: 599px) {
        .main-container {
        width: 100% !important;
        }
        
        .col {
        width: 100%;
        }
        
        .fluid-on-mobile {
        width: 100% !important;
        height: auto !important;
        text-align:center;
        }
        
        .fluid-on-mobile img {
        width: 100% !important;
        }
        
        .hide-on-mobile {
        display:none !important;
        width:0px !important;
        height:0px !important;
        overflow:hidden;
        }
        }
        
        </style>
        
        
        <!--[if gte mso 9]>
        <style>
        
        .col {
        width: 100%;
        }
        
        .width600 {
        width: 600px;
        }
        
        .width90 {
        width: 90px;
        height: auto;
        }
        
        .hide-on-desktop {
        display: none;
        }
        
        .hide-on-desktop table {
        mso-hide: all;
        }
        
        .hide-on-desktop div {
        mso-hide: all;
        }
        
        .nounderline {text-decoration: none; }
    
        
        </style>
        <![endif]-->
        
        </head>
        <body id="body" leftmargin="0" marginwidth="0" topmargin="0" marginheight="0" offset="0" style="font-family:Arial, sans-serif; font-size:0px;margin:0;padding:0;background-color:#ffffff;">
        <style>
        @media screen and (min-width: 600px) {
        .hide-on-desktop {
        display: none;
        }
        }
        @media all and (max-width: 599px) {
        .hide-on-mobile {
        display:none !important;
        width:0px !important;
        height:0px !important;
        overflow:hidden;
        }
        .main-container {
        width: 100% !important;
        }
        .col {
        width: 100%;
        }
        .fluid-on-mobile {
        width: 100% !important;
        height: auto !important;
        text-align:center;
        }
        .fluid-on-mobile img {
        width: 100% !important;
        }
        }
        </style>
        <div style="background-color:#ffffff;">
        <table height="100%" width="100%" cellpadding="0" cellspacing="0" border="0">
        <tr>
        <td valign="top" align="left">
        <table cellpadding="0" cellspacing="0" border="0" width="100%">
        <tr>
        <td width="100%">
        <table cellpadding="0" cellspacing="0" border="0" width="100%">
        <tr>
        <td align="center" width="100%">
        <!--[if gte mso 9]><table width="600" cellpadding="0" cellspacing="0"><tr><td><![endif]-->
        <table class="width600 main-container" cellpadding="0" cellspacing="0" border="0" width="100%" style="max-width:600px;">
        <tr>
        <td width="100%">
        <table cellpadding="0" cellspacing="0" border="0" width="100%">
        <tr>
        <td valign="top">
        <table cellpadding="0" cellspacing="0" border="0" width="100%">
        <tr>
        <td valign="top" align="center"><!--[if gte mso 9]><table width="90" cellpadding="0" cellspacing="0"><tr><td><![endif]-->
        <table cellpadding="0" cellspacing="0" border="0" style="max-width:100%;" class="img-wrap">
        <tr>
        <td valign="top" align="center"><img src="https://images.chamaileon.io/631a88a1ca74416a7f0746d3/631a88a1ca744152880746d5/1662954019795_logo gmail (1).png" width="90" height="90" alt="" border="0" style="display:block;font-size:14px;max-width:100%;height:auto;" class="width90" />
        </td>
        </tr>
        </table>
        <!--[if gte mso 9]></td></tr></table><![endif]-->
        </td>
        </tr>
        </table>
        <table cellpadding="0" cellspacing="0" border="0" width="100%">
        <tr>
        <td valign="top" style="padding-top:5px;padding-right:10px;padding-bottom:5px;padding-left:10px;"><div style="font-family:Roboto Condensed, Arial Narrow, Roboto, sans-serif;font-size:25px;color:#131313;line-height:25px;text-align:left;"><p style="padding: 0; margin: 0;text-align: center;"><span class="mso-font-fix-arial"><span style="color:#005070;">¡Detectamos un nuevo dispositivo! </span></span></p>
        <p style="padding: 0; margin: 0;text-align: center;">&nbsp;</p></div>
        </td>
        </tr>
        </table>
        <table cellpadding="0" cellspacing="0" border="0" width="100%">
        <tr>
        <td valign="top" style="padding-top:5px;padding-right:10px;padding-bottom:5px;padding-left:10px;"><div style="font-family:Lato, Helvetica Neue, Helvetica, Arial, sans-serif;font-size:16px;color:#131313;line-height:25px;text-align:left;"><p style="padding: 0; margin: 0;"><span class="mso-font-fix-arial">Detectamos un nuevo acceso de su cuenta en un dispositivo ' .$dispositivo.'.</span><br><span class="mso-font-fix-arial">
        desde: ' . $ciudad.'</span><br><span class="mso-font-fix-arial">
        Ip identificada: ' . $ip .'</span><br><span class="mso-font-fix-arial">
        Contacte con un administrador si no se trata de usted.</span><br><span class="mso-font-fix-arial">
        Hora del registro:' .date('d-m-Y H:i:s');'</span></p>
        <p style="padding: 0; margin: 0;">&nbsp;</p></div>
        </td>
        </tr>
        </table>
        <table cellpadding="0" cellspacing="0" border="0" width="100%">
        <tr>
        <td valign="top" style="padding-top:35px;">
        <table cellpadding="0" cellspacing="0" border="0" width="100%">
        <tr>
        <td valign="top">
        </td>
        </tr>
        </table>
        </td>
        </tr>
        </table>
        </td>
        </tr>
        </table>
        </td>
        </tr>
        </table>
        <!--[if gte mso 9]></td></tr></table><![endif]-->
        </td>
        </tr>
        </table>
        </td>
        </tr>
        </table>
        </td>
        </tr>
        </table>
        </div>
        </body>
        </html>';

        //Enviar el mensaje, comprobar si hay errores
        if (!$mail->send()) {
            echo $mail->ErrorInfo;
            return $mail->ErrorInfo;
        } else {
            return true;
        }
    }
    
    //Algoritmo para obtener un token aleatorio-------------------------.
    public function Obtener_token($cantidadCaracteres)
    {
        $Caracteres = 'ABCDEFGHJKMNOPQRSTUVWXYZabcdefghjkmnopqrstuvwxyz0123456789';
        $ca = strlen($Caracteres);
        $ca--;
        $Hash = '';
        for ($x = 1; $x <= $cantidadCaracteres; $x++) {
            $Posicao = rand(0, $ca);
            $Hash .= substr($Caracteres, $Posicao, 1);
        }
        return $Hash;
    }
}
