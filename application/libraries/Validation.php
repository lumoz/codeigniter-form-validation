<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

/**
 * Library for CodeIgniter to validate form via Ajax.
 * @author	Luigi Mozzillo <luigi@innato.it>
 * @link	http://innato.it
 * @version	1.1.9
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * THIS SOFTWARE AND DOCUMENTATION IS PROVIDED "AS IS," AND COPYRIGHT
 * HOLDERS MAKE NO REPRESENTATIONS OR WARRANTIES, EXPRESS OR IMPLIED,
 * INCLUDING BUT NOT LIMITED TO, WARRANTIES OF MERCHANTABILITY OR
 * FITNESS FOR ANY PARTICULAR PURPOSE OR THAT THE USE OF THE SOFTWARE
 * OR DOCUMENTATION WILL NOT INFRINGE ANY THIRD PARTY PATENTS,
 * COPYRIGHTS, TRADEMARKS OR OTHER RIGHTS.COPYRIGHT HOLDERS WILL NOT
 * BE LIABLE FOR ANY DIRECT, INDIRECT, SPECIAL OR CONSEQUENTIAL
 * DAMAGES ARISING OUT OF ANY USE OF THE SOFTWARE OR DOCUMENTATION.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://gnu.org/licenses/>.
 */
class Validation  {

	public $CI;

	protected $data		= array();
	protected $config	= array();
	protected $validate	= TRUE;
	protected $error	= '';

	/**
	 * Constructor.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct($config = NULL) {
		$this->CI =& get_instance();

		if ( ! empty($config)) {
			$this->initialize($config);
		}
		$this->set_post();	// Default data
	}

	// --------------------------------------------------------------------------

	/**
	 * Initialize library.
	 *
	 * @access public
	 * @param mixed $data
	 * @return void
	 */
	public function initialize($config) {
		$this->config = $config;
	}

	// --------------------------------------------------------------------------

	/**
	 * Set fields data from array.
	 *
	 * @access public
	 * @param mixed $data
	 * @return void
	 */
	public function set_data($data) {
		$this->data = (array) $data;
		return $this;
	}

	// --------------------------------------------------------------------------

	/**
	 * Set fields data from POST.
	 *
	 * @access public
	 * @return void
	 */
	public function set_post() {
		$this->set_data($this->CI->input->post());
		return $this;
	}

	// --------------------------------------------------------------------------

	/**
	 * Set fields data from GET.
	 *
	 * @access public
	 * @return void
	 */
	public function set_get() {
		$this->set_data($this->CI->input->get());
		return $this;
	}

	// --------------------------------------------------------------------------

	/**
	 * Exit due error.
	 *
	 * @access private
	 * @param mixed $error
	 * @param mixed $field (default: NULL)
	 * @return void
	 */
	private function _error($error, $field = NULL) {
		if ($this->validate) {
			$this->error = is_null($field) ? $error : sprintf($error, $field);
			$this->validate = FALSE;
		}
	}

	// --------------------------------------------------------------------------

	/**
	 * Return error message.
	 *
	 * @access public
	 * @return void
	 */
	public function get_error() {
		return $this->error;
	}

	// --------------------------------------------------------------------------

	/**
	 * Check if form is valid.
	 *
	 * @access public
	 * @return void
	 */
	public function is_valid() {
		return $this->validate;
	}

	// --------------------------------------------------------------------------

	/**
	 * Set form as invalid by passing the string corresponding error.
	 *
	 * @access public
	 * @param string $error (default: '')
	 * @return void
	 */
	public function set_not_valid($error = '') {
		$this->_error($error);
	}

	// --------------------------------------------------------------------------

	/**
	 * If you pass string parameter and not array, puts it in an array.
	 *
	 * @access private
	 * @param mixed &$param
	 * @return void
	 */
	private function _parse( & $param) {
		if ( ! is_array($param)) {
			$param = array($param);
		}
	}

	// --------------------------------------------------------------------------

	/**
	 * Check required fields.
	 *
	 * @access public
	 * @param mixed $fields
	 * @param string $err_msg (default: '')
	 * @return void
	 */
	public function required($fields, $err_msg = '') {
		$this->_parse($fields);
		foreach ($fields as $v) {
			if ($this->is_valid()) {
				$this->data[$v] = isset($this->data[$v]) ? trim($this->data[$v]) : '';
				if (empty($this->data[$v])) {
					$this->_error($err_msg, $v);
				}
			}
		}
		return $this;
	}

	// ------------------------------------------------------------------------

	/**
	 * Check required fields only if isset.
	 *
	 * @access public
	 * @param mixed $fields
	 * @param string $err_msg (default: '')
	 * @return void
	 */
	public function required_isset($fields, $err_msg = '') {
		$this->_parse($fields);
		foreach ($fields as $v) {
			if ($this->is_valid()) {
				if (isset($this->data[$v])) {
					$this->data[$v] = trim($this->data[$v]);
					if (empty($this->data[$v])) {
						$this->_error($err_msg, $v);
					}
				}

			}
		}
		return $this;
	}

	// --------------------------------------------------------------------------

	/**
	 * Check if email fields are valid.
	 *
	 * @access public
	 * @param mixed $fields
	 * @param string $err_msg (default: '')
	 * @return void
	 */
	public function email($fields, $err_msg = '') {
		$this->_parse($fields);
		$this->CI->load->helper('email');
		foreach ($fields as $v) {
			if ($this->is_valid()) {
				if ( ! empty($this->data[$v])) {
					if ( ! valid_email($this->data[$v])) {
						$this->_error($err_msg, $v);
					}
				}
			}
		}
		return $this;
	}

	// --------------------------------------------------------------------------

	/**
	 * Check that the fields meet a particular regular expression.
	 *
	 * @access public
	 * @param mixed $fields
	 * @param mixed $regexp
	 * @param string $err_msg (default: '')
	 * @return void
	 */
	public function regexp($fields, $regexp, $err_msg = '') {
		$this->_parse($fields);
		foreach ($fields as $v) {
			if ($this->is_valid()) {
				if ( ! empty($this->data[$v])) {
					if ( ! preg_match($regexp, $this->data[$v])) {
						$this->_error($err_msg, $v);
					}
				}
			}
		}
		return $this;
	}

	// --------------------------------------------------------------------------

	/**
	 * Check URL fields.
	 *
	 * @access public
	 * @param mixed $fields
	 * @param string $err_msg (default: '')
	 * @return void
	 */
	public function url($fields, $err_msg = '') {
		$regexp = '/^(https?\:\/\/){0,1}(www\.){0,1}([a-z0-9-_.]+)(\.{1})([a-z]{2,4})$/i';
		return $this->regexp($fields, $regexp, $err_msg);
	}

	// --------------------------------------------------------------------------

	/**
	 * Check that fields are not longer than a defined value.
	 *
	 * @access public
	 * @param mixed $fields
	 * @param mixed $len
	 * @param string $err_msg (default: '')
	 * @return void
	 */
	public function maxlen($fields, $len, $err_msg = '') {
		$this->_parse($fields);
		foreach ($fields as $v) {
			if ($this->is_valid()) {
				if ( ! empty($this->data[$v])) {
					if (strlen($this->data[$v]) > $len) {
						$this->_error($err_msg, $v);
					}
				}
			}
		}
		return $this;
	}

	// --------------------------------------------------------------------------

	/**
	 * Check that fields are not shorter than a defined value.
	 *
	 * @access public
	 * @param mixed $fields
	 * @param mixed $len
	 * @param string $err_msg (default: '')
	 * @return void
	 */
	public function minlen($fields, $len, $err_msg = '') {
		$this->_parse($fields);
		foreach ($fields as $v) {
			if ($this->is_valid()) {
				if ( ! empty($this->data[$v])) {
					if (strlen($this->data[$v]) < $len) {
						$this->_error($err_msg);
					}
				}
			}
		}
		return $this;
	}

	// --------------------------------------------------------------------------

	/**
	 * Check that fields do not have characters other than letters of the alphabet.
	 *
	 * @access public
	 * @param mixed $fields
	 * @param string $err_msg (default: '')
	 * @return void
	 */
	public function alpha($fields, $err_msg = '') {
		$regexp = '/^([A-Za-z]+)$/';
		return $this->regexp($fields, $regexp, $err_msg);
	}

	// --------------------------------------------------------------------------

	/**
	 * Check that fields do not have characters other than letters of the alphabet and space.
	 *
	 * @access public
	 * @param mixed $fields
	 * @param string $err_msg (default: '')
	 * @return void
	 */
	public function alpha_s($fields, $err_msg = '') {
		$regexp = '/^([A-Za-z\ ]+)$/';
		return $this->regexp($fields, $regexp, $err_msg);
	}

	// --------------------------------------------------------------------------

	/**
	 * Check that fields do not have characters other than numbers.
	 *
	 * @access public
	 * @param mixed $fields
	 * @param string $err_msg (default: '')
	 * @return void
	 */
	public function num($fields, $err_msg = '') {
		$regexp = '/^([0-9]+)$/';
		return $this->regexp($fields, $regexp, $err_msg);
	}

	// --------------------------------------------------------------------------

	/**
	 * Check that fields do not have characters other than numbers and space.
	 *
	 * @access public
	 * @param mixed $fields
	 * @param string $err_msg (default: '')
	 * @return void
	 */
	public function num_s($fields, $err_msg = '') {
		$regexp = '/^([0-9\ ]+)$/';
		return $this->regexp($fields, $regexp, $err_msg);
	}

	// --------------------------------------------------------------------------

	/**
	 * Check that fields do not have characters other than letters and numbers.
	 *
	 * @access public
	 * @param mixed $fields
	 * @param string $err_msg (default: '')
	 * @return void
	 */
	public function alphanum($fields, $err_msg = '') {
		$regexp = '/^([A-Za-z0-9]+)$/';
		return $this->regexp($fields, $regexp, $err_msg);
	}

	// --------------------------------------------------------------------------

	/**
	 * Check that fields do not have characters other than letters of the alphabet, numbers and space.
	 *
	 * @access public
	 * @param mixed $fields
	 * @param string $err_msg (default: '')
	 * @return void
	 */
	public function alphanum_s($fields, $err_msg = '') {
		$regexp = '/^([A-Za-z0-9\ ]+)$/';
		return $this->regexp($fields, $regexp, $err_msg);
	}

	// --------------------------------------------------------------------------

	/**
	 * Check that fields do not have spaces.
	 *
	 * @access public
	 * @param mixed $fields
	 * @param string $err_msg (default: '')
	 * @return void
	 */
	public function no_spaces($fields, $err_msg = '') {
		$regexp = '/^([^\ ]+)$/';
		return $this->regexp($fields, $regexp, $err_msg);
	}

	// --------------------------------------------------------------------------

	/**
	 * Check if a numeric field is greater than a certain value.
	 *
	 * @access public
	 * @param mixed $fields
	 * @param mixed $num
	 * @param string $err_msg (default: '')
	 * @return void
	 */
	public function num_gt($fields, $num, $err_msg = '') {
		$this->_parse($fields);
		foreach ($fields as $v) {
			if ($this->is_valid()) {
				if ( ! $this->num($v, $err_msg) && $this->data[$v] < $num) {
					$this->_error($err_msg, $v);
				}
			}
		}
		return $this;
	}

	// --------------------------------------------------------------------------

	/**
	 * Check if a numeric field is less than a certain value.
	 *
	 * @access public
	 * @param mixed $fields
	 * @param mixed $num
	 * @param string $err_msg (default: '')
	 * @return void
	 */
	public function num_lt($fields, $num, $err_msg = '') {
		$this->_parse($fields);
		foreach ($fields as $v) {
			if ($this->is_valid()) {
				if ( ! $this->num($v, $err_msg) && $this->data[$v] > $num) {
					$this->_error($err_msg, $v);
				}
			}
		}
		return $this;
	}

	// --------------------------------------------------------------------------

	/**
	 * Check that the fields have a date.
	 *
	 * @access public
	 * @param mixed $fields
	 * @param string $err_msg (default: '')
	 * @return void
	 */
	public function date($fields, $err_msg = '') {
		$this->_parse($fields);
		foreach ($fields as $v) {
			if ($this->is_valid()) {
				if ( ! empty($this->data[$v])) {
					$match = array();
					if ( ! preg_match('/^([0-9]{2})([^A-Za-z0-9]{1})([0-9]{2})([^A-Za-z0-9]{1})([0-9]{4})$/', $this->data[$v], $match)) {
						$this->_error($err_msg, $v);
					} elseif ( ! checkdate($match[3], $match[1], $match[5])) {
						$this->_error($err_msg, $v);
					}
				}
			}
		}
		return $this;
	}

	// --------------------------------------------------------------------------

	/**
	 * Check the difference between two dates.
	 *
	 * @access private
	 * @param mixed $date_1
	 * @param mixed $date_2
	 * @return void
	 */
	private function date_diff($date_1, $date_2) {
		$d1 = strtotime($this->data[$date_1]);
		$d2 = strtotime($this->data[$date_2]);
		return round(($d1 - $d2)/60/60/24);
	}

	// --------------------------------------------------------------------------

	/**
	 * Check that a date is larger than other.
	 *
	 * @access public
	 * @param mixed $date_1
	 * @param mixed $date_2
	 * @param string $err_msg (default: '')
	 * @return void
	 */
	public function date_gt($date_1, $date_2, $err_msg = '') {
		if ($this->is_valid()) {
			if ( ! empty($date_1) && ! empty($date_2)) {
				if ($this->date_diff($date_1, $date_2, $err_msg) > 0) {
					$this->_error($err_msg);
				}
			}
		}
		return $this;
	}

	// --------------------------------------------------------------------------

	/**
	 * Check that a date is smaller than other.
	 *
	 * @access public
	 * @param mixed $date_1
	 * @param mixed $date_2
	 * @param string $err_msg (default: '')
	 * @return void
	 */
	public function date_lt($date_1, $date_2, $err_msg = '') {
		if ($this->is_valid()) {
			if ( ! empty($date_1) && ! empty($date_2)) {
				if ($this->date_diff($date_1, $date_2, $err_msg) < 0) {
					$this->_error($err_msg);
				}
			}
		}
		return $this;
	}

	// --------------------------------------------------------------------------

	/**
	 * Check that the fields have a English date.
	 *
	 * @access public
	 * @param mixed $fields
	 * @param string $err_msg (default: '')
	 * @return void
	 */
	public function date_en($fields, $err_msg = '') {
		$this->_parse($fields);
		foreach ($fields as $v) {
			if ($this->is_valid()) {
				if ( ! empty($this->data[$v]) && $this->data[$v] != '0000-00-00') {
					$match = array();
					if ( ! preg_match('/^([0-9]{4})([^A-Za-z0-9]{1})([0-9]{2})([^A-Za-z0-9]{1})([0-9]{2})$/', $this->data[$v], $match)) {
						$this->_error($err_msg, $v);
					} elseif ( ! checkdate($match[3], $match[5], $match[1])) {
						$this->_error($err_msg, $v);
					}
				}
			}
		}
		return $this;
	}

	// --------------------------------------------------------------------------

	/**
	 * Check that the fields have a datetime value.
	 *
	 * @access public
	 * @param mixed $fields
	 * @param string $err_msg (default: '')
	 * @return void
	 */
	public function datetime($fields, $err_msg = '') {
		$this->_parse($fields);
		$exp = '/^([0-9]{4})([\-])([0-9]{2})([\-])([0-9]{2})[\ ]([0-9]{2})[\:]([0-9]{2})[\:]([0-9]{2})$/';
		foreach ($fields as $v) {
			if ($this->is_valid()) {
				if ( ! empty($this->data[$v]) && $this->data[$v] != '0000-00-00 00:00:00') {
					$match = array();
					if ( ! preg_match($exp, $this->data[$v], $match)) {
						$this->_error($err_msg, $v);
					} elseif ( ! checkdate($match[3], $match[5], $match[1])) {
						$this->_error($err_msg, $v);
					}
				}
			}
		}
		return $this;
	}

	// --------------------------------------------------------------------------

	/**
	 * Check that the field has been checked.
	 *
	 * @access public
	 * @param mixed $field
	 * @param mixed $checked_value
	 * @param string $err_msg (default: '')
	 * @return void
	 */
	public function checked($field, $checked_value, $err_msg = '') {
		if ($this->is_valid()) {
			if (strcmp($this->data[$field], $checked_value) != 0) {
				$this->_error($err_msg);
			}
		}
		return $this;
	}

	// --------------------------------------------------------------------------

	/**
	 * Check that the field has been selected.
	 *
	 * @access public
	 * @param mixed $field
	 * @param string $err_msg (default: '')
	 * @param string $empty_value (default: '')
	 * @return void
	 */
	public function selected($field, $err_msg = '', $empty_value = '') {
		if ($this->is_valid()) {
			if (strcmp($this->data[$field], $empty_value) != 0) {
				$this->_error($err_msg);
			}
		}
		return $this;
	}

	// --------------------------------------------------------------------------

	/**
	 * Check that the two fields are equal.
	 *
	 * @access public
	 * @param mixed $field_1
	 * @param mixed $field_2
	 * @param string $err_msg (default: '')
	 * @return void
	 */
	public function equal($field_1, $field_2, $err_msg = '') {
		if ($this->is_valid()) {
			if (strcmp($this->data[$field_1], $this->data[$field_2]) != 0) {
				$this->_error($err_msg);
			}
		}
		return $this;
	}

	// --------------------------------------------------------------------------

	/**
	 * Check the correctness of the field $param through the method $method
	 *
	 * @access public
	 * @param mixed $callback
	 * @param mixed $err_msg
	 * @param mixed $parameters (default: array())
	 * @return void
	 */
	public function callback($callback, $err_msg, $parameters = array()) {
		if ($this->is_valid()) {

			// If $callback is a string, transform to array
			if ( ! is_array($callback)) {
				$callback = array($this->CI, $callback);
			}

			if ( ! method_exists($callback[0], $callback[1])) {
				$this->_error('Method `'. $callback[1] .'()` not exists.');
			} else {

				// If method exists, call func with data parameters
				if ( ! is_array($parameters)) {
					$parameters = array($parameters);
				}

				// Call method
				if ( ! call_user_func_array($callback, $parameters)) {
					$this->_error($err_msg);
				}
			}
		}
		return $this;
	}
}

/* End of file Validation.php */
/* Location: ./application/libraries/Validation.php */