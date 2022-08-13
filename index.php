<?php
    header("Content-type:application/json;charset=utf-8");
    header('Access-Control-Allow-Origin:*');
	header('Access-Control-Allow-Method:POST,GET');
    $id = trim($_REQUEST["id"]);

function get_constellation($birth_month,$birth_date){
    $birth_month = strval($birth_month);
    $constellation_name = array('水瓶座','双鱼座','白羊座','金牛座','双子座','巨蟹座','狮子座','处女座','天秤座','天蝎座','射手座','摩羯座');
    if ($birth_date <= 22){
        if ('1' !== $birth_month){
            $constellation = $constellation_name[$birth_month-2];
        }else{
            $constellation = $constellation_name[11];
        }
    }else{
        $constellation = $constellation_name[$birth_month-1];
    }
    return $constellation;
}

function checkIdCard($idcard){
    if(strlen($idcard)!=18){
        return false;
    }
    $idcard_base = substr($idcard, 0, 17);
    $verify_code = substr($idcard, 17, 1);
    $factor = array(7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2);
    $verify_code_list = array('1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2');
    $total = 0;
    for($i=0; $i<17; $i++){
        $total += substr($idcard_base, $i, 1)*$factor[$i];
    }
    $mod = $total % 11;
    if($verify_code == $verify_code_list[$mod]){
        return true;
    }else{
        return false;
    }
}

if(checkIdCard($id) == true){
    $address_code = substr($id,0,6);
    $conn = new mysqli('localhost', 'root', 'root2021', 'idcard');
	$sql = "SELECT address FROM fullinfo where numid='$address_code' LIMIT 1";
	$res = $conn->query($sql);
	if ($res -> num_rows > 0) {
	    $row =  mysqli_fetch_array($res);
	    $address =  $row['address'];
	}else{
	    $address = '未知地区';
	}
	$address = str_replace("\n","",$address);
	$birth_code = substr($id,6,8);
    $birth_year = substr($birth_code,0,4);
	$birth_month = preg_replace('/^0+/','',substr($birth_code,4,2));
	$birth_day = preg_replace('/^0+/','',substr($birth_code,6,2));
    $constellations = get_constellation($birth_month,$birth_day);
    $sex_code = substr($id,16,1);
    if($sex_code % 2 == 0){
        $sex = '女';
    }else{
        $sex = '男';
    }
    $info = array(
        'code' => 200,
        'verify' => '核验通过',
        'idcard' => $id,
        'address' => $address,
        'constellations' => $constellations,
        'birthday' => $birth_year.'年'.$birth_month.'月'.$birth_day.'日',
        'gender' => $sex,
        'author' => 'Vincent Young',
        'contact' => 'https://t.me/missuo'
        );
}else{
    $info = array(
        'code' => 500,
        'verify' => '核验失败',
        'idcard' => $id,
        'author' => 'Vincent Young',
        'contact' => 'https://t.me/missuo'
        );
}
echo json_encode($info,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
?>
