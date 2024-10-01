<?php
include('header.php');
include('MyController.php');

$myController = new MyController();

$users = $myController->list();



?>
<table class="table table-bordered">
    <thead>
        <tr>
            <th>ID</th>
            <th>First Name</th>
            <th>Last Name</th>
            <th>Email</th>
            <th>Timezone</th>
            <th>Hobbies</th>
            <th>Matches</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($users as $key => $user) { ?>
            <tr>
                <td>
                    <?php echo ++$key; ?>
                </td>
                <td><?php echo htmlspecialchars($user['first_name']); ?></td>
                <td><?php echo htmlspecialchars($user['last_name']); ?></td>
                <td><?php echo htmlspecialchars($user['email']) ?></td>
                <td><?php echo htmlspecialchars($user['timezone']) ?></td>
                <td>
                    <?php echo htmlspecialchars($user['hobbies']) ?>
                </td>
                <td>

                    <?php
                    if (count($user['matching_users']) > 0)
                        echo implode(', ', array_column($user['matching_users'], 'first_name'));
                    else {
                        echo "No matching users";
                    }
                    ?>
                </td>
            </tr>
        <?php } ?>

    </tbody>
</table>
<?php include('footer.php') ?>