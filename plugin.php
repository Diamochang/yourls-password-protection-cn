<?php
/*
Plugin Name: YOURLS 短链接密码保护
Plugin URI: https://matc.io/yourls-password
Description: 使用本插件，您可以使用密码保护您想要的任何短链接！
Version: 1.5 (CN)
Author: Matthew (translated version by Mike Wang)
Author URI: https://matc.io
*/

// No direct call
if( !defined( 'YOURLS_ABSPATH' ) ) die();

// Hook our custom function into the 'pre_redirect' event
yourls_add_action( 'pre_redirect', 'warning_redirection' );

// Custom function that will be triggered when the event occurs
function warning_redirection( $args ) {
	$matthew_pwprotection_array = json_decode(yourls_get_option('matthew_pwprotection'), true);
	if ($matthew_pwprotection_array === false) {
		yourls_add_option('matthew_pwprotection', 'null');
		$matthew_pwprotection_array = json_decode(yourls_get_option('matthew_pwprotection'), true);
		if ($matthew_pwprotection_array === false) {
			die("由于数据库疑似存在问题，密码保护插件无法正确启用。");
		}
	}

	$matthew_pwprotection_fullurl = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
	$matthew_pwprotection_urlpath = parse_url( $matthew_pwprotection_fullurl, PHP_URL_PATH );
	$matthew_pwprotection_pathFragments = explode( '/', $matthew_pwprotection_urlpath );
	$matthew_pwprotection_short = end( $matthew_pwprotection_pathFragments );

	if( array_key_exists( $matthew_pwprotection_short, (array)$matthew_pwprotection_array ) ){
		// Check if password is submited, and if it matches the DB
		if( isset( $_POST[ 'password' ] ) && password_verify( $_POST[ 'password' ], $matthew_pwprotection_array[ $matthew_pwprotection_short ]) ){
			$url = $args[ 0 ];
			
			// Redirect client
			header("Location: $url");

			die();
		} else {
			$error = ( isset( $_POST[ 'password' ] ) ? "<script>alertify.error(\"密码不正确，请重试\")</script>" : "");
			$matthew_ppu =    yourls__( "受密码保护的短链接",                       "matthew_pwp" ); // Translate Password Title
			$matthew_ph =     yourls__( "密码"                                    , "matthew_pwp" ); // Translate the word Password
			$matthew_sm =     yourls__( "请在框内输入密码以继续。", "matthew_pwp" ); // Translate the main message
			$matthew_submit = yourls__( "已发送！"                                       , "matthew_pwp" ); // Translate the Submit button
			// Displays main "Insert Password" area
			echo <<<PWP
			<html>
				<head>
					<title>跳转提示</title>
					<style>
						@import url(https://weloveiconfonts.com/api/?family=fontawesome);
						@import url(https://meyerweb.com/eric/tools/css/reset/reset.css);
     @import url('https://fonts.loli.net/css2?family=Noto+Sans:wght@300;400;700&display=swap');
						[class*="fontawesome-"]:before {
						  font-family: 'FontAwesome', sans-serif;
						}
						* {
						  -moz-box-sizing: border-box;
							   box-sizing: border-box;
						}
						*:before, *:after {
						  -moz-box-sizing: border-box;
							   box-sizing: border-box;
						}

						body {
						  background: #2c3338;
						  color: #606468;
						  font: 87.5%/1.5em 'Noto Sans', sans-serif;
						  margin: 0;
						}

						a {
						  color: #eee;
						  text-decoration: none;
						}

						a:hover {
						  text-decoration: underline;
						}

						input {
						  border: none;
						  font-family: 'Noto Sans',  sans-serif;
						  font-size: 14px;
						  line-height: 1.5em;
						  padding: 0;
						  -webkit-appearance: none;
						}

						p {
						  line-height: 1.5em;
						}

						.clearfix {
						  *zoom: 1;
						}
						.clearfix:before, .clearfix:after {
						  content: ' ';
						  display: table;
						}
						.clearfix:after {
						  clear: both;
						}

						.container {
						  left: 50%;
						  position: fixed;
						  top: 50%;
						  -webkit-transform: translate(-50%, -50%);
							  -ms-transform: translate(-50%, -50%);
								  transform: translate(-50%, -50%);
						}
						#login {
						  width: 280px;
						}

						#login form span {
						  background-color: #363b41;
						  border-radius: 3px 0px 0px 3px;
						  color: #606468;
						  display: block;
						  float: left;
						  height: 50px;
						  line-height: 50px;
						  text-align: center;
						  width: 50px;
						}

						#login form input {
						  height: 50px;
						}

						#login form input[type="text"], input[type="password"] {
						  background-color: #3b4148;
						  border-radius: 0px 3px 3px 0px;
						  color: #606468;
						  margin-bottom: 1em;
						  padding: 0 16px;
						  width: 230px;
						}

						#login form input[type="submit"] {
						  border-radius: 3px;
						  -moz-border-radius: 3px;
						  -webkit-border-radius: 3px;
						  background-color: #ea4c88;
						  color: #eee;
						  font-weight: bold;
						  margin-bottom: 2em;
						  text-transform: uppercase;
						  width: 280px;
						}

						#login form input[type="submit"]:hover {
						  background-color: #d44179;
						}

						#login > p {
						  text-align: center;
						}

						#login > p span {
						  padding-left: 5px;
						}
					</style>
    <!-- 翻译者：JSDelivr 国内连接状况堪忧，故替换为 BootCDN。 -->
					<!-- JavaScript -->
					<script src="//cdn.bootcdn.net/ajax/libs/AlertifyJS/1.11.4/alertify.min.js"></script>

					<!-- CSS -->
					<link rel="stylesheet" href="//cdn.bootcdn.net/ajax/libs/AlertifyJS/1.11.4/css/alertify.min.css"/>
					<!-- Default theme -->
					<link rel="stylesheet" href="//cdn.bootcdn.net/ajax/libs/AlertifyJS/1.11.4/css/themes/default.min.css"/>
				</head>
				<body>
					<div class="container">
						<div id="login">
							<form method="post">
								<fieldset class="clearfix">
									<p><span class="fontawesome-lock"></span><input type="password" name="password" value="请输入密码..." onBlur="if(this.value == '') this.value = '请输入密码...'" onFocus="if(this.value == '请输入密码...') this.value = ''" required></p>
									<p><input type="submit" value="$matthew_submit"></p>
								</fieldset>
							</form>
						</div>
					</div>
					$error
				</body>
		</html>
PWP;
			die();
		}
	}
}

// Register plugin page in admin page
yourls_add_action( 'plugins_loaded', 'matthew_pwprotection_display_panel' );
function matthew_pwprotection_display_panel() {
	yourls_register_plugin_page( 'matthew_pwp', '密码保护', 'matthew_pwprotection_display_page' );
}

// Function which will draw the admin page
function matthew_pwprotection_display_page() {
	if( isset( $_POST[ 'checked' ] ) && isset( $_POST[ 'password' ] ) || isset( $_POST[ 'unchecked' ] ) ) {
		matthew_pwprotection_process_new();
		matthew_pwprotection_process_display();
	} else {
		if(yourls_get_option('matthew_pwprotection') !== false){
			yourls_add_option( 'matthew_pwprotection', 'null' );
		}
		matthew_pwprotection_process_display();
	}
}

// Set/Delete password from DB
function matthew_pwprotection_process_new() {
	// Verify nonce token.
	yourls_verify_nonce( "matthew_pwprotection_update" );

	$matthew_pwprotection_array =  json_decode(yourls_get_option('matthew_pwprotection'), true);

	foreach( $_POST[ 'password' ] as $url => $url_password) {
		if($url_password != "DONOTCHANGE_8fggwrFrRXvqndzw") {
			$_POST[ 'password' ][ $url ] = password_hash($url_password, PASSWORD_BCRYPT);
		} else {
			$_POST[ 'password' ][ $url ] = $matthew_pwprotection_array[ $url ];
		}
	}

	// Update database
	yourls_update_option( 'matthew_pwprotection', json_encode( $_POST[ 'password' ] ) );
	
	echo "<p style='color: green'>成功！</p>";
}

// Display Form
function matthew_pwprotection_process_display() {
	$ydb = yourls_get_db();

	// get limit and offset for pagination
	$limit = 50;
	$offset = @$_GET['p'];
	if ($offset == NULL){
		$offset = 0;
	}else{
		if ((int)$offset < 0){
			$offset = 1;
		}
		$offset = ((int)$offset - 1) * $limit;
	}

	$where = '1=1';
	$binds = array(
		'limit'=> $limit,
		'offset'=> $offset,
	);

	$short_url_to_filter = @$_GET['q'];
	if ($short_url_to_filter != NULL && strlen($short_url_to_filter)>0){
		$where = 'keyword LIKE :keyword';
		$binds['keyword'] = '%'.$short_url_to_filter.'%';
	}

	$table = YOURLS_DB_TABLE_URL;
	$sql = "SELECT * FROM `$table` WHERE $where LIMIT :limit OFFSET :offset";
	
	$query = $ydb->fetchAll($sql, $binds);

	$matthew_su = yourls__( "短链接"   , "matthew_pwp" ); // Translate "Short URL"
	$matthew_ou = yourls__( "源链接", "matthew_pwp" ); // Translate "Original URL"
	$matthew_pw = yourls__( "密码"    , "matthew_pwp" ); // Translate "Password"

	// Protect action with nonce
	$matthew_pwprotection_noncefield = yourls_nonce_field( "matthew_pwprotection_update" );

	echo <<<TB
	<style>
	table {
		border-collapse: collapse;
		width: 100%;
	}

	th, td {
		text-align: left;
		padding: 8px;
	}

	tr:nth-child(even){background-color: #f2f2f2}
	tr:nth-child(odd){background-color: #fff}
	</style>
	<div style="overflow-x:auto;">
		<form method="post" id="form_submit">
		<label>搜索短链接：</label>
		<input type="text" id="txt_search" size="20">
		<input id="btn_search" type="button" value="搜索">
			<table>
				<tr>
					<th>$matthew_su</th>
					<th>$matthew_ou</th>
					<th>$matthew_pw</th>
				</tr>
TB;

	foreach( $query as $link ) { // Displays all shorturls in the YOURLS DB
		$short = $link["keyword"];
		$url = $link["url"];
		$matthew_pwprotection_array =  json_decode(yourls_get_option('matthew_pwprotection'), true); // Get array of currently active Password Protected URLs
		if( strlen( $url ) > 51 ) { // If URL is too long, shorten it with '...'
			$sURL = substr( $url, 0, 30 ). "...";
		} else {
			$sURL = $url;
		}
		if( array_key_exists( $short, (array)$matthew_pwprotection_array ) ){ // Check if URL is currently password protected or not
			$text = yourls__( "启用密码？" );
			$password = "DONOTCHANGE_8fggwrFrRXvqndzw";
			$checked = " checked";
			$unchecked = '';
			$style = '';
			$disabled = '';
		} else {
			$text = yourls__( "启用密码？" );
			$password = '';
			$checked = '';
			$unchecked = ' disabled';
			$style = 'display: none';
			$disabled = ' disabled';
		}

		echo <<<TABLE
				<tr>
					<td>$short</td>
					<td><span title="$url">$sURL</span></td>
					<td>
						<input type="checkbox" name="checked[{$short}]" class="matthew_pwprotection_checkbox" value="enable" data-input="$short"$checked> $text
						<input type="hidden" name="unchecked[{$short}]" id="{$short}_hidden" value="true"$unchecked>
						<input id="$short" type="password" name="password[$short]" style="$style" value="$password" onkeypress="return checkIfSubmitPassword(event);" placeholder="密码..."$disabled ><br>
					</td>
				</tr>
TABLE;
	}

	$current_page = $offset/$limit+1;
	$previous_page = $current_page-1;
	$next_page = $current_page+1;
	$total_data = count($query);

	echo <<<END
			</table>
			$matthew_pwprotection_noncefield
			<input id="btn_previous" type="button" value="前一页">
			<input id="btn_next" type="button" value="下一页">
			<p><input id="btn_submit" type="button" value="发送"></p>
		</form>
	</div>
	<script>
		$("#txt_search").val("$short_url_to_filter");
		$("#txt_search").focus();

		function filterShortURL(){
			var current_url = window.location.href;
			current_url = current_url.replace(/\&p\=\d+/, "");
			let shortURLToFind = $("#txt_search").val();
			if (current_url.includes("&q=")){
				window.location.href = current_url.replace("&q=$short_url_to_filter", "&q="+shortURLToFind);
			}else{
				window.location.href += "&q="+shortURLToFind;
			}
		}

		function formSubmit(){
			$('#form_submit').submit();
		}

		function checkIfSubmitPassword(e) {
			e = e || window.event;
			if (e.which === 13) {
				formSubmit()
			}
			return true;
		}

		$(document).ready(function(){
			let total_data = $total_data;
			let current_page = $current_page;

			$( ".matthew_pwprotection_checkbox" ).click(function() {
				var dataAttr = "#" + this.dataset.input;
				$( dataAttr ).toggle();
				if( $( dataAttr ).attr( 'disabled' ) ) {
					$( dataAttr ).removeAttr( 'disabled' );

					$( dataAttr + "_hidden" ).attr( 'disabled' );
					$( dataAttr + "_hidden" ).prop('disabled', true);
				} else {
					$( dataAttr ).attr( 'disabled' );
					$( dataAttr ).prop('disabled', true);

					$( dataAttr + "_hidden" ).removeAttr( 'disabled' );
				}
			});

			$( "#btn_previous" ).click(function() {
				if (current_page > 1 && window.location.href.includes("&p=$current_page")){
					window.location.href = window.location.href.replace( "&p=$current_page", "&p=$previous_page" );
				}
			});

			$( "#btn_next" ).click(function() {
				if (window.location.href.includes("&p=")){
					window.location.href = window.location.href.replace( "&p=$current_page", "&p=$next_page" );
				}else{
					window.location.href += "&p=$next_page";
				}
			});

			$( "#btn_search" ).click(function() {
				filterShortURL();
			});

			$( "#txt_search" ).on('keypress',function(e) {
				if(e.which === 13) {
					e.preventDefault();
					filterShortURL();
					e.stopPropagation();
				}
			});

			$( "#btn_submit" ).click(function() {
				formSubmit();
			});
			

			// go to previus page when not data
			if (current_page > 1 && total_data == 0){
				$("#btn_previous").trigger("click");
			}
		});
	</script>
END;
}
?>
