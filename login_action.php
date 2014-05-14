<?
	// #### DESCRIPTION ####
	// This script is designed to be used as the 'action' for a semi-static login form, 
	//      which replaces the default Confluence screen (and can be branded/etc)
	//
	// The script expects the following form arguments (as GET or POST)
	//   - email     		<-- NOTE: this differs from the normal confluence form
	//   - password
	//   - os_destination	<-- stores the url of the page you were attempting to access, before being asked to log in
	//   - os_cookie		<-- true/false setting cookie persistence
	//
	// This script then:
	//     1. Looks up the email address in the confluence database, to find the username
	//     2. Does a behind-the-scenes form post to the normal Confluence login action url
	//     3. Extracts session cookie, and returns it to the user's browser
	//     4. Redirects the user back to Confluence
	//
	// Taking over Confluence login page:
	//   You can implement an Rpache RewriteRule like follows, which will transparently redirect 
	//   all requests to your custom login page. 
	// 
	//	    RewriteRule ^/login.action(.*)		/path/to/your/login.php$1 [noescape,R] [T] 


	// CONFIGURATION
	$CONFLUENCE_MYSQL_HOST = "127.0.0.1";
	$CONFLUENCE_MYSQL_USER = "root";
	$CONFLUENCE_MYSQL_PASS = "xxxxx";
	$CONFLUENCE_MYSQL_DB = "confluence";
	
	$FAILEDLOGIN_URL = "http://www.yourdomain.com/path/to/login.php?loginfailed=true";

	$CONFLUENCE_LOGINACTIONURL = "http://www.yourdomain.com/dologin.action";
	$CONFLUENCE_BASEURL = "http://www.yourdomain.com/";

	// Cache Control
	$expires = 0; # 0 seconds
	header("Pragma: public");
	header("Cache-Control: maxage=".$expires);
	header('Expires: ' . gmdate('D, d M Y H:i:s', time()+$expires) . ' GMT');


	if ( isset($_REQUEST["email"]) && isset($_REQUEST["password"]) ) { 

		# CONNECT TO *CONFLUENCE* DATABASE
		
			$dbh = mysqli_connect($CONFLUENCE_MYSQL_HOST, $CONFLUENCE_MYSQL_USER, $CONFLUENCE_MYSQL_PASS) or die('Could not connect to DB');
			mysqli_select_db($dbh, $CONFLUENCE_MYSQL_DB) or die('Could not select database');


		# SEARCH FOR CONFLUENCE USERNAME MATCHING SUPPLIED EMAIL ADDRESS
		# But also accept people who try using a username
		# ... Note: that if using HTML5 validation, you need to change form field from type="email" to type="text"

			$query = sprintf("SELECT user_name FROM cwd_user WHERE user_name like '%s' OR email_address LIKE '%s' ", 
				mysqli_escape_string($dbh, $_REQUEST["email"]), 
				mysqli_escape_string($dbh, $_REQUEST["email"]) 
			);
			$result = mysqli_query($dbh, $query);
			if ($row = @mysqli_fetch_assoc($result)) {
				$USERNAME = $row["user_name"];	
			} else { 
				header("Location:" . $FAILEDLOGIN_URL );
				exit;
			}
	
	
		# DO A HTTP-REQUEST TO THE DEFAULT CONFLUENCE LOGIN PAGE, PASSING ALONG USERNAME/PASSWORD
		#
		# If successful, headers of response will contain:
		#   X-Seraph-Loginreason: OK
		#   Set-Cookie: JSESSIONID=....
		#
		# If invalid username or password; will return in headers:
		#   X-Seraph-Loginreason: AUTHENTICATED_FAILED
		#
		# A valid session cookie can be then set in the client browser, which Confluence recognises and continues the session
		
			$postfields = array(
				'os_username' => urlencode($USERNAME),
				'os_password' => urlencode($_REQUEST["password"]),
				'os_cookie' => urlencode($_REQUEST["os_cookie"]),
				'os_destination' => $_REQUEST["os_destination"]
			);

			$ch = curl_init();
			$timeout = 20;
			curl_setopt($ch, CURLOPT_URL, $CONFLUENCE_LOGINACTIONURL);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
			curl_setopt($ch, CURLOPT_HEADER, TRUE);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
			curl_setopt($ch, CURLOPT_POST, count($fields));
			curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postfields) );

			// Getting binary header data
			$header = curl_exec($ch);
		
			// Parse the header
			$headerField = array();
			$fields = explode("\r\n", preg_replace('/\x0D\x0A[\x09\x20]+/', ' ', $header));
			foreach( $fields as $field ) {
				if( preg_match('/([^:]+): (.+)/m', $field, $match) ) {
					$match[1] = preg_replace('/(?<=^|[\x09\x20\x2D])./e', 'strtoupper("\0")', strtolower(trim($match[1])));
					if( isset($headerField[$match[1]]) ) {
						$headerField[$match[1]] = array($headerField[$match[1]], $match[2]);
					} else {
						$headerField[$match[1]] = trim($match[2]);
					}
				}
			}
			curl_close($ch);

			//echo '<pre>';
			//print_r($headerField);
			//echo '</pre>';
			//exit;


		# HANDLE SUCCESS/FAILURE RESPONSES

			if ( (isset($headerField['X-Seraph-Loginreason'])) && ($headerField["X-Seraph-Loginreason"] == "OK") ) {
				# Login OK

				// Store returned session cookie in client browser
				// Note: Can be more than one cookie returned (hence array check)
				if ( is_array($headerField["Set-Cookie"]) ) { 
					foreach ($headerField["Set-Cookie"] as $cookie) { 
						header("Set-Cookie: " . $cookie, false);
					}
				} else { 
					$cookie = $headerField["Set-Cookie"];
					header("Set-Cookie: " . $cookie, false);
				}
	
				// If specific destination is set, redirect there - else to Confluence base			
				if ($_REQUEST["os_destination"]) { 
					header("Location:" . $CONFLUENCE_BASEURL . $_REQUEST["os_destination"] );
				} else { 
					header("Location:" . $CONFLUENCE_BASEURL );
				}

			} else { 
				# Login Failed
				header("Location:" . $FAILEDLOGIN_URL );
			}
	
	} else { 
		# Username or Password not present in request
		header("Location:" . $FAILEDLOGIN_URL );
	}
	exit;
?>
<!-- A barebones, example login form -->
<!--
	<form method="post">
		<input type="hidden" name="os_destination" value="<?=urldecode($_REQUEST["os_destination"])?>">
		Email: <input name="email" value="<?=$_REQUEST["email"]?>" size="50"><br>
		Password: <input type="password" name="password"><br/>
		<input type="submit" value="Login"><br/>
	</form>
-->