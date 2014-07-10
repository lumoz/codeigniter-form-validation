CodeIgniter Form Validation
=================================

CodeIgniter library for fields form validation. It is independent of the standard library of CodeIgniter and can also be used for Ajax calls.

-----

###How to install

1. Add the file Validation.php to your /application/libraries folder.

###How to use

1. Load library:

		$this->load->library('validation');

2. Load data from:

	a. POST (default):

			$this->validation->set_post();

	b. GET:

			$this->validation->set_get();

	c. Array data:

			$this->validation->set_data($data);

3. Load your rules:

		$this->validation->required…
		
4. Change the error delimiters.
	
		$this->validation->set_error_delimiters($delimiter1,$delimiter2);

####Public functions List

- `set_data` Set fields data from array
- `set_post` Set fields data from POST
- `set_get` Set fields data from GET
- `get_error` Return error data: message and field (if exists)
- `get_error_message` Return error message
- `get_error_field` Return error field
- `is_valid` Check if form is valid
- `set_not_valid` Set form not valid ad assign message error
- `required` Check required fields
- `required_isset` Check required fields only if is set
- `email` Check if email fields are valid
- `regexp` Check that the fields meet a particular regular expression
- `url` Check URL fields are valid
- `maxlen` Check that fields are not longer than a defined value
- `minlen` Check that fields are not shorter than a defined value
- `alpha` Check that fields do not have characters other than letters of alphabet
- `alpha_s` Check that fields do not have characters other than letters of alphabet and space
- `num` Check that fields do not have characters other than numbers
- `num_s` Check that fields do not have characters other than numbers and space
- `alphanum` Check that fields do not have characters other than letters and numbers
- `alphanum_s` Check that fields do not have characters other than letters of alphabet, numbers and space
- `no_spaces` Check that fields do not have spaces
- `num_gt` Check if a numeric field is greater than a certain value
- `num_lt` Check if a numeric field is less than a certain value
- `date` Check that the fields have a date
- `date_gt` Check that a date is larger than other
- `date_lt` Check that a date is smaller than other
- `date_en` Check that the fields have a English date
- `datetime` Check that the fields have a datetime value
- `checked` Check that the field has been checked
- `selected` Check that the field has been selected
- `equal` Check that the two fields are equal
- `callback` Check the correctness of the field $param through the method $method.

###How to work

	public function save() {
		$this->load->library('validation');
		$this->validation->set_data($data);
		// $this->validation->set_post();

		$this->validation->required(array('email', 'username', 'firstname', 'lastname', 'city', 'password'), 'Fields are required'),
			->email('email', 'Email is not valid field')
			->maxlen('username', 32, 'Username cannot be longer than 32 characters')
			->minlen('username', 6, 'Username cannot be shorter than 6 characters')
			->regxp('username', '/^([a-zA-Z0-9\-]*)$/i', 'Username cannot have characters other than letters, numbers and hyphens')
			->callback('_unique', 'Username already exists.', 'username')
			->callback(array($your_model, 'method_name'), 'Error message', array('parameter1', 'parameter2', 'parameter3'))
			->callback('_other_func', 'Your error.');

		if ($this->validation->is_valid()) {
			if ($data['username'] == 'admin')
				$this->validation->set_not_valid('Username is already registered');

		}

		if ($this->validation->is_valid())
			echo 'success!';
		else
			echo $this->validation->get_error_message();
	}

	public function _unique($username) {
		$res = $this->db->query('…');
		return $res->id != 0;
	}

	public function _other_func() {
		// Check something
		return $error;
	}
