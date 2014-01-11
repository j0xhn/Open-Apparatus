<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js" type="text/javascript"></script>
<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.0.2/css/bootstrap.min.css">

<!-- Optional theme -->
<link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.0.2/css/bootstrap-theme.min.css">

<!-- Latest compiled and minified JavaScript -->
<script src="//netdna.bootstrapcdn.com/bootstrap/3.0.2/js/bootstrap.min.js"></script>
<form name="contactform" method="post" action="/signup/submitForm">
	<table width="450px">
		<tr>
			<td valign="top">
				<label for="first_name">First Name *</label>
			</td>
			<td valign="top">
				<input  type="text" name="first_name" id='first_name' maxlength="50" size="30">
				<span id="firstNameError" style='display:none;'>Please provide a first name.</span>
			</td>
		</tr>
		<tr>
			<td valign="top"">
				<label for="last_name">Last Name *</label>
			</td>
			<td valign="top">
				<input  type="text" name="last_name" id='last_name' maxlength="50" size="30">
				<span id="lastNameError" style='display:none;'>Please provide a last name.</span>
			</td>
		</tr>
		<tr>
			<td valign="top">
				<label for="email">Email Address *</label>
			</td>
			<td valign="top">
				<input  type="text" name="email" id='email' maxlength="80" size="30">
				<span id="emailError" style='display:none;'>Please provide a email address.</span>
			</td>
		</tr>
		<tr>
			<td valign="top">
				<label for="phone">Phone Number</label>
			</td>
			<td valign="top">
				<input  type="text" name="phone" id='phone' maxlength="30" size="30">
				<span id="phoneError" style='display:none;'>Please provide a phone number.</span>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<input type="submit" id="submitForm" value="Submit">
			</td>
		</tr>
	</table>
</form>

<script>
	$(document).ready(function(){
		$('#submitForm').on('click', function(){
			var error = false;

			if($('#first_name').val() == ''){
				$('#firstNameError').show();
				error = true;
			} else{
				$('#firstNameError').hide();
			}
			if($('#last_name').val() == ''){
				$('#lastNameError').show();
				error = true;
			} else{
				$('#lastNameError').hide();
			}
			if($('#email').val() == ''){
				$('#emailError').show();
				error = true;
			} else{
				$('#emailError').hide();
			}
			if($('#phone').val() == ''){
				$('#phoneError').show();
				error = true;
			} else{
				$('#phoneError').hide();
			}

			if(error){
				return false;
			}
		})
	})
</script>
<!-- ENTER CSS HERE -->
<style>
	body{
		background-color:red;
	}
</style>