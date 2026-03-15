<?php
require_once __DIR__ . '/../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

function sendOrderEmail($toEmail, $toName, $orderId, $items, $subtotal, $shipping, $discount, $total) {
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'celeste142025@gmail.com';
        $mail->Password   = 'zmzffpcmjaszcrqe';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        $mail->setFrom('celeste142025@gmail.com', 'YIMYIM Notebooks');
        $mail->addAddress($toEmail, $toName);

        $mail->isHTML(true);
        $mail->Subject = "Order Confirmation #$orderId - YIMYIM Notebooks";

        $itemsHTML = '';
        foreach ($items as $item) {
            $itemTotal = $item['price'] * $item['quantity'];
            $itemsHTML .= "<tr>
                <td style='padding:10px; border-bottom:1px solid #eee;'>{$item['name']}</td>
                <td style='padding:10px; border-bottom:1px solid #eee; text-align:center;'>{$item['quantity']}</td>
                <td style='padding:10px; border-bottom:1px solid #eee; text-align:right;'>EGP " . number_format($itemTotal, 2) . "</td>
            </tr>";
        }

        $discountRow = '';
        if ($discount > 0) {
            $discountRow = "<tr><td colspan='2' style='padding:8px; text-align:right;'>Discount</td><td style='padding:8px; text-align:right; color:#e74c3c;'>-EGP " . number_format($discount, 2) . "</td></tr>";
        }

        $mail->Body = "
        <div style='font-family: Segoe UI, sans-serif; max-width: 600px; margin: 0 auto;'>
            <div style='background: #FFB6C1; padding: 20px; text-align: center;'>
                <h1 style='color: white; margin: 0;'>YIMYIM</h1>
            </div>
            <div style='padding: 30px; background: #f9f6f1;'>
                <h2 style='color: #333;'>Thank you for your order, $toName!</h2>
                <p style='color: #666;'>Your order <strong>#$orderId</strong> has been placed successfully.</p>
                
                <table style='width: 100%; border-collapse: collapse; margin: 20px 0;'>
                    <tr style='background: #FFB6C1; color: white;'>
                        <th style='padding: 10px; text-align: left;'>Product</th>
                        <th style='padding: 10px; text-align: center;'>Qty</th>
                        <th style='padding: 10px; text-align: right;'>Price</th>
                    </tr>
                    $itemsHTML
                </table>
                
                <table style='width: 100%; margin-top: 15px;'>
                    <tr><td colspan='2' style='padding:8px; text-align:right;'>Subtotal</td><td style='padding:8px; text-align:right;'>EGP " . number_format($subtotal, 2) . "</td></tr>
                    <tr><td colspan='2' style='padding:8px; text-align:right;'>Shipping</td><td style='padding:8px; text-align:right;'>EGP " . number_format($shipping, 2) . "</td></tr>
                    $discountRow
                    <tr style='font-weight: bold; font-size: 18px;'><td colspan='2' style='padding:12px; text-align:right; border-top:2px solid #FFB6C1;'>Total</td><td style='padding:12px; text-align:right; border-top:2px solid #FFB6C1;'>EGP " . number_format($total, 2) . "</td></tr>
                </table>
                
                <p style='color: #888; margin-top: 30px; font-size: 14px;'>You will receive your order within 3-5 business days.</p>
            </div>
            <div style='background: #333; color: white; padding: 15px; text-align: center; font-size: 12px;'>
                &copy; " . date('Y') . " YIMYIM Notebooks. All rights reserved.
            </div>
        </div>";

        $mail->AltBody = "Order Confirmation #$orderId\n\nThank you, $toName!\n\nSubtotal: EGP " . number_format($subtotal, 2) . "\nShipping: EGP " . number_format($shipping, 2) . "\nTotal: EGP " . number_format($total, 2);

        $mail->send();
        return true;
    } catch (\Exception $e) {
        error_log("Email send failed: " . $mail->ErrorInfo);
        return false;
    }
}
