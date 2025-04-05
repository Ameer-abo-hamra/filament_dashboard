<?php
use App\Mail\Subscriber;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
// function makeCode($email): mixed
// {
//     $code = Str::random(6);
//     try {
//         Mail::to($email)->send(new Subscriber($code));
//     } catch (TransportExceptionInterface $e) {
//         return false;
//     }
//     return $code;
// }

function sendEmail($to)
{
    $code = Str::random(6);

    $subject = "Verification";
    $message = "Verification code = " . $code;

    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = 'mail.mary4tech.com';
        $mail->SMTPAuth = true;
        $mail->IsHTML(true);
        $mail->Username = 'info@mary4tech.com';
        $mail->Password = '2C4Vyr1Deo$F';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port = 465;
        $mail->setFrom('info@wemarketglobal.com', 'Adds');
        $mail->addAddress($to);
        $mail->Subject = $subject;
        $mail->Body = $message;
        $mail->send();
        return $code;

    } catch (Exception $e) {
        return false;
    }
}





function send_Email($to, $code)
{
    //$code = Str::random(6);

    $subject = "Verification";
    $message = "Verification code = " . $code;

    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = 'mail.mary4tech.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'info@mary4tech.com';
        $mail->Password = '2C4Vyr1Deo$F';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port = 465;
        $mail->setFrom('info@wemarketglobal.com', 'Adds');
        $mail->addAddress($to);
        $mail->Subject = $subject;
        $mail->Body = $message;
        $mail->send();
        return $code;

    } catch (Exception $e) {
        return false;
    }
}


function send_contact_us($to, $message, $from_email = null, $from_name = null)
{
    $mail = new PHPMailer(true);
    $subject = "New Contact Us Message";

    try {
        $mail->isSMTP();
        $mail->Host = 'mail.mary4tech.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'info@mary4tech.com';
        $mail->Password = '2C4Vyr1Deo$F';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port = 465;

        $mail->setFrom('info@wemarketglobal.com', 'Contact Us');

        if ($from_email) {
            $mail->addReplyTo($from_email, $from_name);
        }

        $mail->addAddress($to);

        if ($from_email) {
            $mail->addCC($from_email);
        }

        $mail->Subject = $subject;
        $mail->Body = $message;

        $mail->send();

        return true;
    } catch (Exception $e) {
        return false;
    }
}



function makeResetCode()
{
    $code = Str::random(6);
    return $code;
}

function photo($request, $diskName, $folderName)
{
    if ($request->file("image")) {
        $name = time() . $request->file("image")->getClientOriginalName();
        $path = $request->file("image")->store($folderName, $diskName);
        return $path;
    }


}

function updatePhoto($request, $diskName, $folderName, $oldImagePath)
{
    if ($request->file("image")) {
        if ($oldImagePath && Storage::disk($diskName)->exists($oldImagePath)) {
            Storage::disk($diskName)->delete($oldImagePath);
        }


        $name = time() . $request->file("image")->getClientOriginalName();
        $path = $request->file("image")->storeAs($folderName, $name, $diskName);

        return $path;
    }

    return $oldImagePath;
}


