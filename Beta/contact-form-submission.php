<?php
    // check for form submission - if it doesn't exist then send back to contact form  
    if (!isset($_POST['save']) || $_POST['save'] != 'contact') { 
        header('Location: contact.php'); exit; 
    } 
         
    // get the posted data 
    $name = $_POST['name']; 
    $website = $_POST['website']; 
    $skills = $_POST['skills']; 
    $availability = $_POST['availability'];
    $expectations = $_POST['expectations'];
    $cdetails = $_POST['cdetails']
    $headers = 'From: Open Apparatus';
    
         
    // check that a name was entered 
    if (empty($name)) 
        $error = 'You must enter your name.'; 
    // check that an email address was entered 
    
    // check that a message was entered 
    elseif (empty($cdetails)) 
        $error = 'You must enter someway to get in contact.'; 
             
    // check if an error was found - if there was, send the user back to the form 
    if (isset($error)) { 
        header('Location: contact.php?e='.urlencode($error)); exit; 
    } 
             
    // write the email content 
    $email_content = "Name: $name\n"; 
    $email_content = "Website: $website\n"; 
    $email_content .= "Skills:\n\n$skills\n\n"; 
    $email_content .= "Availability:\n$availability\n\n";
    $email_content .= "Contact Details:\n$cdetails";
         
    // send the email 
    mail ("john@openapparatus.com", "New Lead!", $email_content, $headers); 
         
    // send the user back to the form 
    
    header("Location: thankyou.php");
/* Make sure that code below does not get executed when we redirect. */
exit;

      
    ?>  