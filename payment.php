<?php

include 'admin/inc/db.php';

if (isset($_POST['apt_no']) && isset($_POST['amount'])) {

    $apt_no = mysqli_real_escape_string($con, validate($_POST['apt_no']));
    $amount = mysqli_real_escape_string($con, validate($_POST['amount']));

    // Dynamic domain detection
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https://" : "http://";
    $domain = $protocol . $_SERVER['HTTP_HOST'];
    $base_url = $domain . dirname($_SERVER['PHP_SELF']);

    $sql = "SELECT * FROM `appointments` WHERE `apt_no` = '{$apt_no}' ";
    $result = mysqli_query($con, $sql);
    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $apt_id = $row['id'];

        $userPatient = mysqli_query($con, "SELECT * FROM `users` INNER JOIN `appointments` ON `users`.`id` = `appointments`.`patient_id` WHERE `apt_no` = '{$apt_no}' ");
        $adminPatient = mysqli_query($con, "SELECT * FROM `admin` INNER JOIN `appointments` ON `admin`.`id` = `appointments`.`admin_id` WHERE `apt_no` = '{$apt_no}' ");

        if (mysqli_num_rows($userPatient) > 0) {
            $userPatientResult = mysqli_fetch_assoc($userPatient);

            $post_data = array();
            $post_data['store_id'] = 'medic65f5ce6be7c07';
            $post_data['store_passwd'] = 'medic65f5ce6be7c07@ssl';
            $post_data['total_amount'] = "{$amount}";
            $post_data['currency'] = 'BDT';
            $post_data['tran_id'] = 'SSLCZ_TEST_' . uniqid();
            $post_data['success_url'] = "{$base_url}/profile.php?status=appointments";
            $post_data['fail_url'] = "{$base_url}/profile.php?status=appointments";
            $post_data['cancel_url'] = "{$base_url}/profile.php?status=appointments";
            $post_data['emi_option'] = '1';
            $post_data['emi_max_inst_option'] = '9';
            $post_data['emi_selected_inst'] = '9';
            $post_data['cus_name'] = "{$userPatientResult['name']}";
            $post_data['cus_email'] = "{$userPatientResult['email']}";
            $post_data['cus_add1'] = 'Dhaka';
            $post_data['cus_city'] = 'Dhaka';
            $post_data['cus_country'] = 'Bangladesh';
            $post_data['cus_phone'] = "{$userPatientResult['contact']}";
            $post_data['value_a'] = "{$apt_no}";
            $post_data['value_b'] = "{$userPatientResult['email']}";
            $post_data['value_c'] = "{$apt_id}";
            $post_data['value_d'] = "{$userPatientResult['contact']}";
            $post_data['cart'] = json_encode(array(
                array('product' => 'DHK TO BRS AC A1', 'amount' => '200.00'),
                array('product' => 'DHK TO BRS AC A2', 'amount' => '200.00'),
            ));
            $post_data['product_amount'] = '100';
            $post_data['vat'] = '5';
            $post_data['discount_amount'] = '5';
            $post_data['convenience_fee'] = '3';

            $direct_api_url = 'https://sandbox.sslcommerz.com/gwprocess/v3/api.php';
            $handle = curl_init();
            curl_setopt($handle, CURLOPT_URL, $direct_api_url);
            curl_setopt($handle, CURLOPT_TIMEOUT, 30);
            curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, 30);
            curl_setopt($handle, CURLOPT_POST, 1);
            curl_setopt($handle, CURLOPT_POSTFIELDS, $post_data);
            curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false);

            $content = curl_exec($handle);
            $code = curl_getinfo($handle, CURLINFO_HTTP_CODE);

            if ($code == 200 && !(curl_errno($handle))) {
                curl_close($handle);
                $sslcommerzResponse = $content;
            } else {
                curl_close($handle);
                echo 'FAILED TO CONNECT WITH SSLCOMMERZ API';
                exit;
            }

            $sslcz = json_decode($sslcommerzResponse, true);

            if (isset($sslcz['GatewayPageURL']) && $sslcz['GatewayPageURL'] != '') {
                echo "<meta http-equiv='refresh' content='0;url=" . $sslcz['GatewayPageURL'] . "'>";
                exit;
            } else {
                echo 'JSON Data parsing error!';
            }
        } else {
            echo 'No user found for the given appointment number.';
        }
    } else {
        echo 'No appointment found with this apt_no.';
    }
}

function validate($value)
{
    $value = trim($value);
    $value = stripslashes($value);
    $value = htmlspecialchars($value);
    return $value;
}
?>