<?php
// If the form was submitted - update the values in the database
if ( isset($_POST['Submit']) ) {
   update_option('irb_buttonText',$_POST['irb_buttonText']);
   update_option('irb_disclaimer',$_POST['irb_disclaimer']);
   update_option('irb_category',$_POST['irb_category']);
}

// Get the values from the database and store them in the variables
$buttonText = get_option('irb_buttonText');
$disclaimer = get_option('irb_disclaimer');

/* Some simple style to display the option page in the admin panel*/ ?>
<style type="text/css">
	.irb_indent h3 {
	  margin: 20px 0 5px 0; 
	}
	.irb_indent {
	  width: 75%;
	}

	.irb_indent textarea, .irb_indent input, .irb_indent select {
	  width: 100%;
	}
</style>

<div class="wrap">
<div id="icon-options-general" class="icon32"><br/></div>
<h2>Investor Relations Button</h2>
<form name="irb_form" method="post" action="options-general.php?page=irbutton/irb-options.php">
   
	<div class="irb_indent">

		<p>Hier werden der HTML-Text des Disclaimers und die Beschriftung des Login-Buttons definiert. Nach Best&auml;tigung durch den User kann auf alle gesch&uuml;tzten Seiten f&uuml;r die Dauer einer Browser-Sitzung zugegriffen werden.</p>
		<h3>HTML des Disclaimers</h3>
		<textarea name="irb_disclaimer" cols="50" rows="20"><?php echo $disclaimer; ?></textarea></p>

		<h3>Beschriftung des Buttons</h3>
		<input type="text" name="irb_buttonText" value="<?php echo empty( $buttonText ) ? htmlentities('Login') : htmlentities($buttonText); ?>" size="50"/>

	</div>

	<p class="submit">
	  <input class="button-primary" type="submit" name="Submit" value="Aktualisieren" />
	</p>
</form>
</div>