<?php include('header.php');?>

        <div class="container">  
          
            <div class="page-header">  
                <h1>Get in Touch</h1>  
            </div>  
    <?php  
    
      
            // check for a successful form post  
            if (isset($_GET['s'])) echo "<div class=\"alert alert-success\">".$_GET['s']."</div>";  
      
            // check for a form error  
            elseif (isset($_GET['e'])) echo "<div class=\"alert alert-error\">".$_GET['e']."</div>";  
      
    ?>  

	<form id="contact_oa" method="POST" action="contact-form-submission.php" class="form-vertical"> 
		<p>
			<label for="">Name</label>
			<input  data-progression type="text" data-helper="Your first and last name." name="name" value="" placeholder="">
		</p>

		<p>
			<label for="">Website</label>
			<input data-progression type="text" data-helper="Your personal portfolio, personal social media site, any place that gives a representation of who you are." name="email" value="" placeholder="">
		</p>



			<p class="left">
			<label for="">Passions and Skills</label>
			<input data-progression type="text" data-helper="What makes you awesome?! Examples would be your unique talents, abilities or even relationships that you have." name="mytel" value="" placeholder="">
		</p>
			



			<p class="right">
			<label for="">Availability</label>
			<input data-progression type="text" data-helper="How much time do you have to help build?  Is this a mere curiosity or are you willing to dedicate time and resources?" name="mytel" value="" placeholder="">
		</p>
			


		<p>
			<label for="">Hopes</label>
			<textarea data-progression name="" data-helper="What do you hope to achieve?  What kinds of people do you want to surround yourself with?"></textarea>
		</p>

		<p>
			<label for="">Contact Details</label>
			<input data-progression name="" type="text" data-helper="How can we get in contact with you?  Do you prefer your email address, phone number, or skype?">
		</p>

		<p>
			<input type="submit" class="button" name="" value="Lets Go!" placeholder="">
		</p>


	</form>
</div>
<script type="text/javascript">
      $(document).ready(function($) {

          $("#contact_oa").progression({
            tooltipWidth: '300',
            tooltipPosition: 'right',
            tooltipOffset: '50',
            showProgressBar: true,
            showHelper: true,
            tooltipFontSize: '14',
            tooltipFontColor: 'fff',
            progressBarBackground: 'fff',
            progressBarColor: '6EA5E1',
            tooltipBackgroundColor:'a2cbfa',
            tooltipPadding: '10',
            tooltipAnimate: true
          });

     });
    </script>  
            

 
<?php
include('footer.php');
?>
		</div>
        
    </body>  
    </html>  	

</body>
</html>