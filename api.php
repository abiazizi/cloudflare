<?php
$api 	= '35344e8127fd83ab14fb0980dde5e39b38d5c';
$email 	= 'abiazizi465@gmail.com';
$id 	= '942a0c545bf6fa7568124e1b4b28a248';

$header = ['X-Auth-Key: '.$api, 'X-Auth-Email: '.$email, 'Content-Type: application/json'];
$dns 	= 'vps.fella.id';
$list 	= file($argv[1], FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

foreach( $list as $k => $domain){
	echo '['.$k.'] '.$domain."\n";
	try {
		$add_domain = curl('https://api.cloudflare.com/client/v4/zones', '{"account": {"id": "'.$id.'"}, "name":"'.$domain.'","jump_start":true}', $header);

		if( $add_domain->success === false ){
			throw new Exception($add_domain, 1);
		}
		echo "> Status : Success Added to Cloudflare\n";

		$zoneid = $add_domain->result->id;
		$nameserver1 = $add_domain->result->name_servers[0];
		$nameserver2 = $add_domain->result->name_servers[1];
		echo "\t[Name Server 1] : ".$name_server1."\n";
		echo "\t[Name Server 2] : ".$name_server2."\n";

		$set_dns 	= curl('https://api.cloudflare.com/client/v4/zones/'.$zoneid.'/dns_records', '{"type":"CNAME","name":"*","content":"'.$dns.'","ttl":3600,"proxied":true}', $header);
		$set_dns 	= curl('https://api.cloudflare.com/client/v4/zones/'.$zoneid.'/dns_records', '{"type":"CNAME","name":"@","content":"'.$dns.'","ttl":3600,"proxied":true}', $header);

		if( $set_dns->success === false ){
			throw new Exception($set_dns, 1);
		}
		echo "> Status : Success Set DNS to ".$dns."\n";

		$set_flexible 	= curl_patch('https://api.cloudflare.com/client/v4/zones/'.$zoneid.'/settings/ssl', '{"value": "flexible"}', $header);

		if( $set_flexible->success === false ){
			throw new Exception($set_flexible, 1);
		}
		echo "> Status : Success Set SSL to Flexible\n";

		$set_https 	= curl_patch('https://api.cloudflare.com/client/v4/zones/'.$zoneid.'/settings/always_use_https', '{"value": "on"}', $header);

		if( $set_https->success === false ){
			throw new Exception($set_https, 1);
		}
		echo "> Status : Success Set Always HTTPS\n";
		
	} catch (Exception $e) {
		print_r(json_encode($e, JSON_PRETTY_PRINT));
		exit;
	}
}

function curl($link, $post = false, $header=false, $cookie=false){
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $link);
	// curl_setopt($ch, CURLOPT_HEADER, true);
	if( $cookie ){
	curl_setopt($ch, CURLOPT_COOKIEFILE, './aliexpress.cookie');
	curl_setopt($ch, CURLOPT_COOKIEJAR, './aliexpress.cookie');
	}
	if( $header ){
	curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
	}
	if( $post ){
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
	}
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);
	curl_setopt($ch, CURLOPT_TIMEOUT, 0);
	curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/94.0.4606.71 Mobile Safari/537.36");
	$response = curl_exec($ch);
	curl_close($ch);

	return json_decode($response);
}
function curl_patch($link, $post, $header){
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $link);
	if( $header ){
	curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
	}
	if( $post ){
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
	curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
	}
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);
	curl_setopt($ch, CURLOPT_TIMEOUT, 0);
	curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/94.0.4606.71 Mobile Safari/537.36");
	$response = curl_exec($ch);
	curl_close($ch);

	return json_decode($response);
}
