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
                <div class="formrow">
                  <label for="name">
                    <input data-progression type="text" data-helper="Your first and last name please." name="name" value="" placeholder="Name">
                  </label>
                </div>

                <div class="formrow">
                  <label for="website">
                    <input data-progression type="text" data-helper="Your personal portfolio, personal social media site, any place that gives a representation of who you are." name="website" value="" placeholder="Website">
                  </label>
                </div>



                <div class="formrow">
                  <label for="skills">
                    <textarea data-progression data-helper="What makes you awesome?! Examples would be your unique talents, abilities or even relationships that you have." name="skills" value="" placeholder="Your Skills"></textarea>
                  </label>
                </div>


                <div class="formrow">
                  <label for="availability">
                    <textarea data-progression data-helper="How much time do you have to help build?  Is this a mere curiosity or are you willing to dedicate time and resources?" name="availability" value="" placeholder="Availability"></textarea>
                  </label>
                </div>
                  
                <div class="formrow">
                  <label for="expectations">
                    <textarea data-progression data-helper="What do you expect from your involvement? What kinds of projects would you like to work on and what types of people would you want on your team?" name="expectations" value="" placeholder="Expectations"></textarea>
                  </label>
                </div>

                <div class="formrow">
                  <label for="cdetails">
                    <input data-progression type="text" data-helper="How can we get in contact with you?  Do you prefer your email address, phone number, or skype? " name="cdetails" value="" placeholder="Contact Details">
                  </label>
                </div>

                  <input type="submit" class="btn btn-success" name="" value="Let's Go!" placeholder="">

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