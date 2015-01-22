<?php

abstract class MyCakeTestCase extends CakeTestCase {

	/**
	 * Opposite wrapper method of assertWithinMargin.
	 *
	 * @param float $result
	 * @param float $expected
	 * @param float $margin
	 * @param string $message
	 * @return void
	 */
	protected static function assertNotWithinMargin($result, $expected, $margin, $message = '') {
		$upper = $result + $margin;
		$lower = $result - $margin;
		return static::assertFalse((($expected <= $upper) && ($expected >= $lower)), $message);
	}

/*** Helper Functions **/

	/**
	 * Outputs debug information during a web tester (browser) test case
	 * since PHPUnit>=3.6 swallowes all output by default
	 * this is a convenience output handler since debug() or pr() have no effect
	 *
	 * @param mixed $data
	 * @param bool $force Should the output be flushed (forced)
	 * @param bool $showHtml
	 * @return void
	 */
	protected static function debug($data, $force = false, $showHtml = null) {
		if (!empty($_GET['debug']) || !empty($_SERVER['argv']) && (in_array('-v', $_SERVER['argv'], true) || in_array('-vv', $_SERVER['argv'], true))) {
			if ($showHtml === null && php_sapi_name() === 'cli') {
				$showHtml = true;
			}
			debug($data, $showHtml);
		} else {
			return;
		}
		if (!$force) {
			return;
		}
		ob_flush();
	}

	/**
	 * Outputs debug information during a web tester (browser) test case
	 * since PHPUnit>=3.6 swallowes all output by default
	 * this is a convenience output handler
	 *
	 * This method will not be part of 3.x! Please switch to debug().
	 *
	 * @param mixed $data
	 * @param bool $force Should the output be flushed (forced)
	 * @return void
	 */
	protected static function out($data, $plain = false, $force = false) {
		if (php_sapi_name() === 'cli') {
			return;
		}
		if (!$plain || is_array($data)) {
			pr($data);
		} else {
			echo '<div>' . $data . '</div>';
		}
		if (!$force) {
			return;
		}
		ob_flush();
	}

	/**
	 * MyCakeTestCase::isDebug()
	 *
	 * @return bool Success
	 */
	protected static function isDebug() {
		if (!empty($_GET['debug']) || !empty($_SERVER['argv']) && in_array('--debug', $_SERVER['argv'], true)) {
			return true;
		}
		return false;
	}

	protected function _basePath($full = false) {
		$phpSelf = $_SERVER['PHP_SELF'];
		if (strpos($phpSelf, 'webroot/test.php') !== false) {
			$pieces = explode('webroot/test.php', $phpSelf, 2);
		} else {
			$pieces = explode('test.php', $phpSelf, 2);
		}
		$url = array_shift($pieces);
		if ($full) {
			$pieces = explode('/', $_SERVER['SERVER_PROTOCOL'], 2);
			$protocol = array_shift($pieces);
			$url = strtolower($protocol) . '://' . $_SERVER['SERVER_NAME'] . $url;
		}
		return $url;
	}

	protected function _header($title) {
		if (strpos($title, 'test') === 0) {
			$title = substr($title, 4);
			$title = Inflector::humanize(Inflector::underscore($title));
		}
		return '<h3>' . $title . '</h3>';
	}

	/**
	 * Without trailing slash!?
	 * //TODO: test
	 */
	protected function _baseurl() {
		return current(split("webroot", $_SERVER['PHP_SELF']));
	}

	protected static $_startTime = null;

	protected function _microtime($precision = 8) {
		return round(microtime(true), $precision);
	}

	protected function _startClock($precision = 8) {
		static::$_startTime = static::_microtime();
	}

	protected function _elapsedTime($precision = 8, $restart = false) {
		$elapsed = static::_microtime() - static::$_startTime;
		if ($restart) {
			static::_startClock();
		}
		return round($elapsed, $precision);
	}

	/**
	 * @param float $time
	 * @param int precision
	 * @param bool $secs: usually in milliseconds (for long times set it to 'true')
	 */
	protected function _printElapsedTime($time = null, $precision = 8, $secs = false) {
		if ($time === null) {
			$time = static::_elapsedTime($precision);
		}
		if ($secs) {
			$unit = 's';
			$prec = 7;
		} else {
			$time = $time * 1000;
			$unit = 'ms';
			$prec = 4;
		}

		$precision = ($precision !== null) ? $precision : $prec;
		pr('elapsedTime: ' . number_format($time, $precision, ',', '.') . ' ' . $unit);
	}

	protected function _title($expectation, $title = null) {
		$eTitle = '{expects: ' . $expectation . '}';
		if (!empty($title)) {
			$eTitle = $title . ' ' . $eTitle;
		}
		return BR . BR . '<b>' . $eTitle . '</b>' . BR;
	}

	protected function _printTitle($expectation, $title = null) {
		if (empty($_SERVER['HTTP_HOST']) || !isset($_GET['show_passes']) || !$_GET['show_passes']) {
			return false;
		}
		echo static::_title($expectation, $title);
	}

	protected function _printResults($expected, $is, $pre = null, $status = false) {
		if (empty($_SERVER['HTTP_HOST']) || !isset($_GET['show_passes']) || !$_GET['show_passes']) {
			return false;
		}

		if ($pre !== null) {
			echo 'value:';
			pr($pre);
		}
		echo 'result is:';
		pr($is);
		if (!$status) {
			echo 'result expected:';
			pr($expected);
		}
	}

	protected function _printResult($is, $pre = null, $status = false) {
		if (empty($_SERVER['HTTP_HOST']) || !isset($_GET['show_passes']) || !$_GET['show_passes']) {
			return false;
		}

		if ($pre !== null) {
			echo 'value:';
			pr($pre);
		}
		echo 'result is:';
		pr($is);
	}

	/**
	 * OsFix method
	 *
	 * @param string $string
	 * @return string
	 */
	protected function _osFix($string) {
		return str_replace(["\r\n", "\r"], "\n", $string);
	}

}
