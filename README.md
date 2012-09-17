CodeIgniter Indie Form Validation
=================================

CodeIgniter library for fields form validation. It is independent of the standard library of CodeIgniter and can also be used for Ajax calls.

-----

###How to install:

1. Add the file IValidation.php to your /application/libraries folder.

###How to use:

1. Call this function:

		$this->load->library('validation', $this->input->post());

####Public functions List

- `get_error` Get error message
- `is_valid` Check if form is valid
- `set_not_valid` Set form not valid ad assign message error
- `required` Check required fields
- `email` Check if email fields are valid
- `regexp` Check that the fields meet a particular regular expression




