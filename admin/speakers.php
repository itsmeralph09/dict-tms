<!DOCTYPE html>
<html lang="en">

<?php include './include/head.php'; ?>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <?php include './include/sidebar.php'; ?>

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <?php include './include/topbar.php'; ?>

                <!-- Begin Page Content -->
                <div class="container-fluid">

                    <!-- Page Heading -->
                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <h1 class="h3 mb-0 text-gray-800">Speakers</h1>
                        <a href="#" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i
                                class="fas fa-download fa-sm text-white-50"></i> Generate Report</a>
                    </div>

        <!-- Content Row -->

        <div class="row">

            <!-- Full Calendar -->
            <div class="col-xl-12 col-lg-8">
                <div class="card shadow mb-4">
                    <!-- Card Header - Dropdown -->
                    <div
                        class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">List of Speakers</h6>
                     
                        <a class="btn btn-sm btn-success shadow-sm" data-toggle="modal" data-target="#addnew"><i class="fas fa-plus fa-sm text-white-50"></i> Add speaker</a>
                   
                    </div>
                    <!-- Card Body -->
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered nowrap" id="myTable" width="100%" cellspacing="0">
                                <thead class="">
                                    <tr>
                                      
                                        <th scope="col">#</th>                                        
                                        <th scope="col">Name</th>                                               
                                        <th scope="col">Action</th>                             
                                       
                                    </tr>
                                </thead>
                                
                                <tbody>

                                  <?php

                            require '../db/dbconn.php';
                            $display_speakers = "SELECT * FROM `speakers` WHERE deleted = 0";
                            $sqlQuery = mysqli_query($con, $display_speakers) or die(mysqli_error($con));

                            $counter = 1;

                            while($row = mysqli_fetch_array($sqlQuery)){
                                $speaker_id = $row['speaker_id'];
                                $first_name = $row['first_name'];
                                $mid_name = $row['mid_name'];
                                $last_name = $row['last_name'];
                                $ext_name = $row['ext_name'];
                                $upload_file = $row['upload_url'];

                                $speaker_name = $row['first_name'] . ' ' . $row['mid_name'] . ' ' . $row['last_name'] . ' ' . $row['ext_name'];
                            ?>
                            <tr>         
                                <td class=""><?php echo $counter; ?></td>
                                <td class=""><?php echo $speaker_name; ?></td>
                               
                                <td class="text-center">
                                    <a class="btn btn-sm btn-primary" data-toggle="modal" data-target="#edit_<?php echo $speaker_id; ?>"><i class="fa-solid fa-pen-to-square"></i></a>
                                 
                                    <a href="#" class="btn btn-sm btn-danger delete-speaker-btn" 
                                       data-speaker-id="<?php echo $speaker_id; ?>" 
                                       data-speaker-name="<?php echo htmlspecialchars($speaker_name); ?>">
                                       <i class="fa-solid fa-trash"></i>
                                    
                                    </a>
                                </td>
                            </tr>
                            <?php
                                $counter++;
                                include('modal/speaker_edit_modal.php');
                            } 
                            ?>
                            </tbody>

                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <?php include('modal/speaker_add_modal.php'); ?>     
        </div>

                </div>
                <!-- /.container-fluid -->

            </div>
            <!-- End of Main Content -->

            <!-- Footer -->
            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>Copyright &copy; Your Website 2020</span>
                    </div>
                </div>
            </footer>
            <!-- End of Footer -->

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Logout Modal-->
    <?php include './include/logout_modal.php'; ?>

    <?php include './include/script.php'; ?>

<script>
    $(document).ready(function(){
    //inialize datatable
    $('#myTable').DataTable({
        scrollX: true
    })

    });

</script>

<script>
    $(document).ready(function() {
        // Function for deleting event
        $('.delete-speaker-btn').on('click', function(e) {
            e.preventDefault();
            var deleteBtn = $(this);
            var speakerId = deleteBtn.data('speaker-id');
            var speakerName = decodeURIComponent(deleteBtn.data('speaker-name'));
            Swal.fire({
                title: 'Delete Speaker',
                html: "You are about to delete the following speaker:<br><br>" +
                      "<strong>Speaker Name:</strong> " + speakerName + "<br>",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        file: 'action/delete_speaker.php', // Adjust the file to your PHP script for deleting the event
                        type: 'POST',
                        data: {
                            speaker_id: speakerId
                        },
                        success: function(response) {
                            if (response.trim() === 'success') {
                                Swal.fire(
                                    'Deleted!',
                                    'Speaker has been deleted.',
                                    'success'
                                ).then(() => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire(
                                    'Error!',
                                    'Failed to delete Speaker.',
                                    'error'
                                );
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error(xhr.responseText);
                            Swal.fire(
                                'Error!',
                                'Failed to delete Speaker.',
                                'error'
                            );
                        }
                    });
                }
            });
        });
    });
</script>

<script>
    $(document).ready(function() {
        $('#addSpeaker').on('click', function(e) {
            e.preventDefault(); // Prevent default form submission
            var formData = $('#addnew form').serialize(); // Serialize form data

            $.ajax({
                file: 'action/add_speaker.php', // file to submit the form data
                type: 'POST',
                data: formData, // Form data to be submitted
                success: function(response) {
                    // Handle the success response
                    console.log(response); // Output response to console (for debugging)
                    Swal.fire({
                        icon: 'success',
                        title: 'Speaker added successfully',
                        showConfirmButton: true, // Show OK button
                        confirmButtonText: 'OK'
                    }).then(() => {
                        location.reload();
                    });   
                },
                error: function(xhr, status, error) {
                    // Handle the error response
                    console.error(xhr.responseText); // Output error response to console (for debugging)
                    Swal.fire({
                        icon: 'error',
                        title: 'Failed to add Speaker',
                        text: 'Please try again later.',
                        showConfirmButton: true, // Show OK button
                        confirmButtonText: 'OK'
                    }).then(() => {
                        location.reload();
                    }); 
                }
            });
        });
    });
</script>
<script>
    $(document).ready(function() {
        // For dynamically rendered modals
        $(document).on('click', '[id^="updateSpeaker_"]', function(e) {
            e.preventDefault(); // Prevent default form submission
            var speakerId = $(this).attr('id').split('_')[1]; // Extract event ID
            var formData = $('#edit_' + speakerId + ' form').serialize(); // Serialize form data

            $.ajax({
                file: 'action/update_speaker.php', // file to submit the form data
                type: 'POST',
                data: formData, // Form data to be submitted
                success: function(response) {
                    // Handle the success response
                    console.log(response); // Output response to console (for debugging)
                    Swal.fire({
                        icon: 'success',
                        title: 'Speaker updated successfully',
                        showConfirmButton: true, // Show OK button
                        confirmButtonText: 'OK'
                    }).then(() => {
                        location.reload();
                    });   
                },
                error: function(xhr, status, error) {
                    // Handle the error response
                    console.error(xhr.responseText); // Output error response to console (for debugging)
                    Swal.fire({
                        icon: 'error',
                        title: 'Failed to update Speaker',
                        text: 'Please try again later.',
                        showConfirmButton: true, // Show OK button
                        confirmButtonText: 'OK'
                    }).then(() => {
                        location.reload();
                    }); 
                }
            });
        });
    });
</script>

<script>
   $(document).on('change', '#inputupload', function() {
    console.log('File input changed'); // Debug output
    var filesCount = $(this)[0].files.length;
    var textbox = $(this).prev();

    if (filesCount === 1) {
        var fileName = $(this).val().split('\\').pop();
        textbox.text(fileName);
    } else {
        textbox.text(filesCount + ' files selected');
    }

    if (typeof FileReader != "undefined") {
        var dvPreview = $("#divImageMediaPreview");
        dvPreview.html("");            
        $(this)[0].files.forEach(function(file) {
            var reader = new FileReader();
            reader.onload = function (e) {
                var img = $("<img />");
                img.attr("style", "width: 100%;");
                img.attr("class", "rounded");
                img.attr("src", e.target.result);
                dvPreview.append(img);
            }
            reader.readAsDatafile(file);                
        });
    } else {
        console.log("This browser does not support HTML5 FileReader.");
    }
});

</script>

</body>

</html>