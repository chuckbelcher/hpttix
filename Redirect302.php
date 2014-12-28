<?php 
					
function Redirect302( $location ) {
	$sname = session_name();
	$sid = session_id();
	if( strlen( $sid ) < 1 ) {
		Header( $location );
		return;
	}
	if( isset( $_COOKIE[ $sname ] ) || strpos( $location, $sname."=".$sid ) !== false ) {
		Header( $location );
		return;
    } else {
		if( strpos( $location, "?" ) > 0 ) {
			$separator = "&";
		} else {
			$separator = "?";
		}
		$fixed = $location . $separator . $sname."=".$sid;
		Header( $fixed );
		return;
	}
}

?>