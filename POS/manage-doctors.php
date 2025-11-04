<?php
session_start();
if (!isset($_SESSION['store_id'])) {
    header("location:login.php");
    exit();
} else {
    include('config/db.php');
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Manage Doctors</title>

    <!-- Data Table CSS -->
    <?php include("part/data-table-css.php"); ?>
    <!-- Data Table CSS end -->

    <!-- All CSS -->
    <?php include("part/all-css.php"); ?>
    <!-- All CSS end -->

</head>

<body class="hold-transition sidebar-mini">
    <div class="wrapper">
        <!-- Navbar -->
        <?php include("part/navbar.php"); ?>
        <!-- Navbar end -->

        <!-- Sidebar -->
        <?php include("part/sidebar.php"); ?>
        <!--  Sidebar end -->

        <div class="content-wrapper bg-dark">
            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <div class="card bg-dark">
                                <div class="card-header ">
                                    <h3 class="card-title">Manage Doctors</h3>
                                </div>
                                <div class="card-body row">
                                    <label for="doctor_name" class="form-label col-12">Doctor Name</label>
                                    <div class="input-group col-12">
                                        <input
                                            type="text"
                                            class="form-control col-4"
                                            id="doctor_name"
                                            name="doctor_name"
                                            placeholder="Enter doctor name">
                                        <button type="submit" class="btn btn-primary col-2" id="submit_doctor" onclick="submitDoctor()">Submit</button>
                                    </div>
                                </div>
                                <div class="card-body bg-dark">

                                    <table id="doctor_table" class="table table-bordered">
                                        <thead>
                                            <tr class="bg-info">
                                                <th>#</th>
                                                <th>Doctor Name</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $sql = $conn->query("SELECT * FROM doctors ORDER BY name ASC");
                                            $row_id = 0;
                                            while ($row = mysqli_fetch_assoc($sql)) {
                                            ?>
                                                <tr>
                                                    <td><?php echo ++$row_id; ?></td>
                                                    <td class="text-capitalize"><?php echo $row['name']; ?></td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
        <!-- Footer -->
        <?php include("part/footer.php"); ?>
        <!-- Footer End -->
    </div>

    <!-- Alert -->
    <?php include("part/alert.php"); ?>
    <!-- Alert end -->

    <!-- All JS -->
    <?php include("part/all-js.php"); ?>
    <!-- All JS end -->

    <!-- Data Table JS -->
    <?php include("part/data-table-js.php"); ?>
    <!-- Data Table JS end -->

</body>
<script>
    $(function() {
        $("#doctor_table")
            .DataTable({
                responsive: true,
                lengthChange: false,
                autoWidth: false,
                searching: true,
                buttons: ["excel", "pdf", "print"],
            })
            .buttons()
            .container()
            .appendTo("#doctor_table_wrapper .col-md-6:eq(0)");
    });

    function submitDoctor() {
        const doctor_name = $('#doctor_name').val();
        $('#submit_doctor').prop('disabled', true);

        if (doctor_name !== "") {
            $.ajax({
                type: "POST",
                url: "actions/doctor/addDoctorAction.php",
                data: {
                    doctor_name
                },
                success: function(response) {
                    var result = JSON.parse(response);

                    if (result.status === 'success') {
                        SuccessMessageDisplay(result.message);
                        setTimeout(() => {
                            window.location.reload();
                        }, 3000);
                    } else {
                        ErrorMessageDisplay(result.message);
                        $('#submit_doctor').prop('disabled', false);
                    }
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                    ErrorMessageDisplay(xhr.responseText);
                },
            });
        } else {
            ErrorMessageDisplay("Enter Doctor name!");
            $('#submit_doctor').prop('disabled', false);
        }

    }
</script>

</html>