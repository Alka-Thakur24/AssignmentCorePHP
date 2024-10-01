<?php
include('header.php');
include('MyController.php'); // Make sure to adjust the path as necessary

// Create an instance of the TimeZoneList class
$myController = new MyController();

// Get all time zones
$timeZones = $myController->getTimezones();
// print_r($timeZones);

?>
<h2>Registration Form</h2>
<form action="javascript:;" id="registerForm" method="POST" enctype="multipart/form-data">
    <h1>Registration Form</h1>
    <label for="timezone">Timezone:</label>
    <select name="timezone" id="timezone">
        <?php
        foreach ($timeZones as $timeZone) {
            echo '<option value="' . htmlspecialchars($timeZone) . '">' . htmlspecialchars($timeZone) . '</option>';
        }
        ?>
    </select>

    <label for="fname">First Name:</label>
    <input type="text" id="fname" name="fname">
    <span class="error" id="fnameError"></span><br>

    <label for="lname">Last Name:</label>
    <input type="text" id="lname" name="lname">
    <span class="error" id="lnameError"></span><br>

    <label for="email">Email:</label>
    <input type="email" id="email" name="email">
    <span class="error" id="emailError"></span><br>

    <label for="hobbies">Select your hobbies:</label>
    <label for="reading">
        <input type="checkbox" id="reading" name="hobbies[]" value="Reading">
        Reading
    </label><br>

    <label for="gaming">
        <input type="checkbox" id="gaming" name="hobbies[]" value="Gaming">
        Gaming</label><br>

    <label for="hiking">
        <input type="checkbox" id="hiking" name="hobbies[]" value="Hiking">
        Hiking</label><br>

    <label for="photography">
        <input type="checkbox" id="photography" name="hobbies[]" value="Photography">
        Photography</label><br>

    <label for="cooking">
        <input type="checkbox" id="cooking" name="hobbies[]" value="Cooking">
        Cooking</label><br>
    <span class="error" id="hobbiesError"></span><br>

    <button type="submit" class="button">Register</button>
</form>
<?php include('footer.php') ?>
<script type="text/javascript">
    $('#registerForm').submit(function() {
        var formValues = $(this).serialize();
        console.log(formValues);
        $.ajax({
            'type': 'POST',
            'url': "/AssignmentCorePHP/register_save.php",
            'data': formValues,
            success: function(res) {
                console.log(res);
                if (res.success) {
                    window.location.href = "/AssignmentCorePHP/list.php";
                } else {
                    if (res.errors) {
                        $.each(res.errors, function(key, value) {
                            $('#' + key + 'Error').text(value);
                        });
                    } else {
                        $('#response').html('<p>' + res.message + '</p>');
                    }
                }
            },
            error: function(res) {
                console.log(res);
            }
        });

    });
</script>